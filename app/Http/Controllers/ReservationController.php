<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\User;
use App\Events\GymVisitStreakUpdated;
use App\Services\StreakService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ReservationController extends Controller
{
    const SESSIONS = [
        ['start' => '06:00', 'end' => '08:00'],
        ['start' => '08:00', 'end' => '10:00'],
        ['start' => '10:00', 'end' => '12:00'],
        ['start' => '12:00', 'end' => '14:00'],
        ['start' => '14:00', 'end' => '16:00'],
        ['start' => '16:00', 'end' => '18:00'],
        ['start' => '18:00', 'end' => '20:00'],
        ['start' => '20:00', 'end' => '22:00'],
    ];

    const MAX_CAPACITY = 20;
    const SESSION_FEE = 25000;
    public function index()
    {
        $user = Auth::user();

        $activeTicket = Reservation::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('session_date', '>=', today())
            ->orderBy('session_date')
            ->orderBy('session_start')
            ->first();

        $history = Reservation::where('user_id', $user->id)
            ->where(function ($q) {
                $q->whereNotIn('status', ['pending', 'confirmed'])
                    ->orWhere('session_date', '<', today());
            })
            ->orderByDesc('session_date')
            ->orderByDesc('session_start')
            ->limit(20)
            ->get();

        $slotAvailability = $this->getSlotAvailability(today()->toDateString());
        $receptionist     = User::where('role', 'receptionist')->first();

        return view('components.reservation.reservasi', compact(
            'activeTicket',
            'history',
            'slotAvailability',
            'receptionist',
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'session_date'  => ['required', 'date', 'after_or_equal:today'],
            'session_start' => ['required', 'date_format:H:i'],
            'session_end'   => ['required', 'date_format:H:i', 'after:session_start'],
            'notes'         => ['nullable', 'string', 'max:500'],
        ]);

        $taken = Reservation::where('session_date', $request->session_date)
            ->where('session_start', $request->session_start)
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();

        if ($taken >= self::MAX_CAPACITY) {
            return back()->withErrors(['session_start' => 'Sesi ini sudah penuh.'])->withInput();
        }

        $existing = Reservation::where('user_id', Auth::id())
            ->where('session_date', $request->session_date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($existing) {
            return back()->withErrors([
                'session_date' => 'Kamu sudah punya reservasi aktif di tanggal ini.',
            ])->withInput();
        }

        $code = 'GYM-'
            . Carbon::parse($request->session_date)->format('Ymd')
            . '-'
            . strtoupper(Str::random(4));

        Reservation::create([
            'user_id'       => Auth::id(),
            'code'          => $code,
            'session_date'  => $request->session_date,
            'session_start' => $request->session_start,
            'session_end'   => $request->session_end,
            'notes'         => $request->notes,
            'fee'           => self::SESSION_FEE,
            'status'        => 'pending',
        ]);

        return redirect()->route('reservasi')
            ->with('success', "Reservasi berhasil! Kode kamu: {$code}. Biaya sesi: Rp " . number_format(self::SESSION_FEE, 0, ',', '.') . " (dibayar di kasir).");
    }

    public function destroy($id)
    {
        $reservation = Reservation::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if (! $reservation->isPending()) {
            return back()->withErrors(['error' => 'Hanya reservasi pending yang bisa dibatalkan.']);
        }

        $reservation->update(['status' => 'cancelled']);

        return back()->with('success', 'Reservasi berhasil dibatalkan.');
    }

    public function slots(Request $request)
    {
        $request->validate([
            'date' => ['required', 'date', 'after_or_equal:today'],
        ]);

        return response()->json($this->getSlotAvailability($request->date));
    }

    public function scanPage()
    {
        $todayLogs = Reservation::with('user')
            ->where('session_date', today())
            ->where('status', 'confirmed')
            ->orderByDesc('confirmed_at')
            ->get();

        return view('components.reservation.scan', compact('todayLogs'));
    }

    public function lookup(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'max:30'],
        ]);

        $code = strtoupper(trim($request->code));

        if (! preg_match('/^GYM-\d{8}-[A-Z0-9]{4}$/', $code)) {
            return response()->json([
                'success' => false,
                'message' => 'Format kode tidak valid.',
            ], 422);
        }

        $reservation = Reservation::with('user')->where('code', $code)->first();

        if (! $reservation) {
            return response()->json([
                'success' => false,
                'message' => 'Kode reservasi tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success'     => true,
            'reservation' => [
                'code'       => $reservation->code,
                'name'       => $reservation->user->name,
                'gender'     => $reservation->user->gender,
                'email'      => $reservation->user->email,
                'date'       => $reservation->session_date->toDateString(),
                'session'    => $reservation->session_label,
                'status'     => $reservation->status,
                'notes'      => $reservation->notes,
                'fee'        => $reservation->fee ?? self::SESSION_FEE,
                'fee_label'  => $reservation->fee_label,
            ],
        ]);
    }

    public function confirm(Request $request)
{
    $request->validate([
        'code' => ['required', 'string', 'max:30'],
    ]);

    $code        = strtoupper(trim($request->code));
    $reservation = Reservation::with('user')->where('code', $code)->first();

    if (! $reservation) {
        return response()->json([
            'success' => false,
            'message' => 'Kode reservasi tidak ditemukan.',
        ], 404);
    }

    if ($reservation->status !== 'pending') {
        return response()->json([
            'success' => false,
            'message' => 'Reservasi ini sudah di-scan atau tidak aktif.',
        ], 422);
    }

    $reservation->update([
        'status'       => 'confirmed',
        'confirmed_at' => now(),
        'confirmed_by' => Auth::id(),
    ]);

    $streakService = app(StreakService::class);
    $streakResult  = $streakService->processGymVisit($reservation->user);
    if (! ($streakResult['already_claimed'] ?? false)) {
        broadcast(new GymVisitStreakUpdated(
            userId:     $reservation->user_id,
            streakData: $streakResult,
        ));
    }

    return response()->json([
        'success'     => true,
        'reservation' => [
            'code'      => $reservation->code,
            'name'      => $reservation->user->name,
            'status'    => 'confirmed',
            'fee_label' => $reservation->fee_label,
        ],
        'streak' => $streakResult['already_claimed'] ?? false ? null : [
            'streak'       => $streakResult['streak'],
            'total_points' => $streakResult['total_points'],
            'is_milestone' => $streakResult['is_milestone'],
            'points_earned'=> $streakResult['total_points_earned'],
        ],
    ]);
}

    private function getSlotAvailability(string $date): array
    {
        $counts = Reservation::where('session_date', $date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->selectRaw('session_start, COUNT(*) as total')
            ->groupBy('session_start')
            ->pluck('total', 'session_start')
            ->toArray();

        return collect(self::SESSIONS)->map(function ($slot) use ($counts) {
            $taken = $counts[$slot['start']] ?? 0;

            return [
                'start'     => $slot['start'],
                'end'       => $slot['end'],
                'taken'     => (int) $taken,
                'available' => self::MAX_CAPACITY - (int) $taken,
                'is_full'   => $taken >= self::MAX_CAPACITY,
                'fee'       => self::SESSION_FEE,
            ];
        })->toArray();
    }
}