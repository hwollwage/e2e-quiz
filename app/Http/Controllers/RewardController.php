<?php

namespace App\Http\Controllers;

use App\Models\PointLog;
use App\Models\Reward;
use App\Models\User;
use App\Services\StreakService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RewardController extends Controller
{
    public function index()
    {
        $user    = Auth::user();
        $rewards = Reward::where('is_active', true)->orderByDesc('is_featured')->get();

        $redemptionHistory = PointLog::where('user_id', $user->id)
            ->where('type', 'redeem')
            ->latest()
            ->get()
            ->map(function ($log) {
                $log->reward_name   = $log->description;
                $log->points_spent  = abs($log->points);
                $log->status        = 'success';
                return $log;
            });

        $pointHistory = PointLog::where('user_id', $user->id)
            ->latest()
            ->take(20)
            ->get();

        return view('components.streak.reward', compact(
            'user',
            'rewards',
            'redemptionHistory',
            'pointHistory',
        ));
    }

    public function redeemTicket($id)
    {
        $user = User::findOrFail(Auth::id());
        $reward = Reward::findOrFail($id);

        $canRedeem = $user->points >= $reward->point_cost;
        $payload = [
            'uid' => $user->id,
            'rid' => $reward->id,
            'ts'  => now()->timestamp,
        ];
        $payload['sig'] = substr(
            hash_hmac('sha256', json_encode($payload), config('app.key')),
            0, 16
        );
        $qrData = 'REDEEM:' . base64_encode(json_encode($payload));

        return view('components.streak.redeem', compact(
            'user',
            'reward',
            'qrData',
            'canRedeem',
        ));
    }
    public function scanRedeemPage()
    {
        $todayLogs = PointLog::with('user')
            ->where('type', 'redeem')
            ->whereDate('created_at', today())
            ->latest()
            ->get();

        return view('components.streak.scan-redeem', compact('todayLogs'));
    }

    public function lookupRedeem(Request $request)
    {
        $request->validate(['qr' => ['required', 'string', 'max:2000']]);

        $qr = trim($request->qr);

        if (! str_starts_with($qr, 'REDEEM:')) {
            return response()->json([
                'success' => false,
                'message' => 'Format QR tidak valid. Pastikan member membuka halaman tiket hadiah.',
            ], 422);
        }

        $payload = json_decode(base64_decode(substr($qr, 7)), true);

        if (! $payload || ! isset($payload['uid'], $payload['rid'], $payload['ts'], $payload['sig'])) {
            return response()->json([
                'success' => false,
                'message' => 'Data QR tidak bisa dibaca.',
            ], 422);
        }
        $toSign      = ['uid' => $payload['uid'], 'rid' => $payload['rid'], 'ts' => $payload['ts']];
        $expectedSig = substr(hash_hmac('sha256', json_encode($toSign), config('app.key')), 0, 16);

        if (! hash_equals($expectedSig, $payload['sig'])) {
            return response()->json([
                'success' => false,
                'message' => 'QR tidak valid atau telah dimodifikasi.',
            ], 422);
        }
        if (now()->timestamp - $payload['ts'] > 1800) {
            return response()->json([
                'success' => false,
                'message' => 'QR sudah kedaluwarsa (> 30 menit). Minta member buka halaman tiket lagi.',
            ], 422);
        }

        $user   = User::find($payload['uid']);
        $reward = Reward::find($payload['rid']);

        if (! $user || ! $reward) {
            return response()->json([
                'success' => false,
                'message' => 'Data member atau hadiah tidak ditemukan.',
            ], 404);
        }

        $canRedeem = $user->points >= $reward->point_cost;

        return response()->json([
            'success' => true,
            'data'    => [
                'qr'         => $qr,
                'can_redeem' => $canRedeem,
                'remaining_after' => $user->points - $reward->point_cost,
                'user'   => [
                    'id'     => $user->id,
                    'name'   => $user->name,
                    'email'  => $user->email,
                    'gender' => $user->gender,
                    'points' => $user->points,
                    'streak' => $user->streak_days,
                ],
                'reward' => [
                    'id'          => $reward->id,
                    'name'        => $reward->name,
                    'category'    => $reward->category,
                    'point_cost'  => $reward->point_cost,
                    'description' => $reward->description,
                ],
            ],
        ]);
    }

    public function confirmRedeem(Request $request, StreakService $streakService)
    {
        $request->validate(['qr' => ['required', 'string', 'max:2000']]);

        $qr = trim($request->qr);

        if (! str_starts_with($qr, 'REDEEM:')) {
            return response()->json(['success' => false, 'message' => 'Format QR tidak valid.'], 422);
        }

        $payload = json_decode(base64_decode(substr($qr, 7)), true);

        if (! $payload || ! isset($payload['uid'], $payload['rid'], $payload['ts'], $payload['sig'])) {
            return response()->json(['success' => false, 'message' => 'Data QR tidak valid.'], 422);
        }

        $toSign      = ['uid' => $payload['uid'], 'rid' => $payload['rid'], 'ts' => $payload['ts']];
        $expectedSig = substr(hash_hmac('sha256', json_encode($toSign), config('app.key')), 0, 16);

        if (! hash_equals($expectedSig, $payload['sig'])) {
            return response()->json(['success' => false, 'message' => 'QR tidak valid.'], 422);
        }

        if (now()->timestamp - $payload['ts'] > 1800) {
            return response()->json(['success' => false, 'message' => 'QR sudah kedaluwarsa.'], 422);
        }

        $user   = User::findOrFail($payload['uid']);
        $reward = Reward::findOrFail($payload['rid']);

        $success = $streakService->deductPoints($user, $reward->point_cost, "Penukaran: {$reward->name}");

        if (! $success) {
            return response()->json([
                'success' => false,
                'message' => "Poin {$user->name} tidak mencukupi. Punya {$user->points} pts, butuh {$reward->point_cost} pts.",
            ], 422);
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'user_name'        => $user->name,
                'reward_name'      => $reward->name,
                'points_spent'     => $reward->point_cost,
                'remaining_points' => $user->fresh()->points,
            ],
        ]);
    }
}