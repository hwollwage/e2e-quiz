<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class OwnerController extends Controller
{
    public function hirePage()
    {
        $receptionists = User::where('role', 'receptionist')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('components.dashboard.hire', compact('receptionists'));
    }

    public function hireReceptionist(Request $request)
    {
        $request->validate([
            'name'                  => 'required|string|max:100',
            'email'                 => 'required|email|unique:users,email',
            'gender'                => 'nullable|in:male,female,other',
            'password'              => 'required|string|min:6|confirmed',
        ], [
            'name.required'         => 'Nama wajib diisi.',
            'email.required'        => 'Email wajib diisi.',
            'email.email'           => 'Format email tidak valid.',
            'email.unique'          => 'Email sudah terdaftar.',
            'password.required'     => 'Password wajib diisi.',
            'password.min'          => 'Password minimal 6 karakter.',
            'password.confirmed'    => 'Konfirmasi password tidak cocok.',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'gender'   => $request->gender,
            'password' => Hash::make($request->password),
            'role'     => 'receptionist',
        ]);

        return back()
            ->with('hire_success', "Akun resepsionis untuk {$request->name} berhasil dibuat.")
            ->with('active_tab', 'receptionist');
    }

    public function deleteReceptionist(Request $request, User $user)
    {
        abort_if($user->role !== 'receptionist', 403);

        $name = $user->name;
        $user->delete();

        return back()
            ->with('hire_success', "Akun {$name} berhasil dihapus.")
            ->with('active_tab', 'receptionist');
    }
}