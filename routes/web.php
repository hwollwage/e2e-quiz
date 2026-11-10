<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\GymDensityController;
use App\Http\Controllers\RewardController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\OwnerController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => view('home'))->name('home');

// Guest only
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
    Route::get('/register', [RegisterController::class, 'show'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
});

// Authenticated
Route::middleware('auth')->group(function () {

    Route::post('/logout', LogoutController::class)->name('logout');
    Route::get('/dashboard', function () {
        return match (Auth::user()->role) {
            'owner'        => redirect()->route('owner.dashboard'),
            'receptionist' => redirect()->route('receptionist.dashboard'),
            default        => view('components.dashboard.index'),
        };
    })->name('dashboard');

    // WebSocket
    Route::get('/chat/history', [ChatController::class, 'history'])->name('chat.history');
    Route::post('/chat/send',   [ChatController::class, 'send'])->name('chat.send');
    Route::get('/chat/unread',  [ChatController::class, 'unreadCount'])->name('chat.unread');

    Route::middleware('role:owner')->prefix('owner')->name('owner.')->group(function () {
        Route::get('/dashboard', fn() => view('components.dashboard.owner'))->name('dashboard');
        Route::get('/hire',                        [OwnerController::class, 'hirePage'])->name('hire');
        Route::post('/hire/receptionist',          [OwnerController::class, 'hireReceptionist'])->name('hire.receptionist');
        Route::delete('/hire/receptionist/{user}', [OwnerController::class, 'deleteReceptionist'])->name('hire.delete');
    });

    Route::middleware('role:receptionist')->prefix('receptionist')->name('receptionist.')->group(function () {
        Route::get('/dashboard', fn() => view('components.dashboard.receptionist'))->name('dashboard');

        // Scan reservasi
        Route::get('/scan',          [ReservationController::class, 'scanPage'])->name('reservation.scan');
        Route::get('/scan/lookup',   [ReservationController::class, 'lookup'])->name('reservation.lookup');
        Route::post('/scan/confirm', [ReservationController::class, 'confirm'])->name('reservation.confirm');

        // Scan penukaran hadiah
        Route::get('/redeem-scan',     [RewardController::class, 'scanRedeemPage'])->name('redeem.scan');
        Route::get('/redeem/lookup',   [RewardController::class, 'lookupRedeem'])->name('redeem.lookup');
        Route::post('/redeem/confirm', [RewardController::class, 'confirmRedeem'])->name('redeem.confirm');

        // Chat
        Route::get('/chat', [ChatController::class, 'index'])->name('chat');
    });

    Route::middleware('role:member')->group(function () {
        
        // Progress
        Route::get('/dashboard/progress',                  [ProgressController::class, 'index'])->name('progress.index');
        Route::post('/dashboard/progress',                 [ProgressController::class, 'store'])->name('progress.store');
        Route::delete('/dashboard/progress/{progressLog}', [ProgressController::class, 'destroy'])->name('progress.destroy');

        // Gym density
        Route::get('/dashboard/gym-density',      [GymDensityController::class, 'index'])->name('gym.density');
        Route::get('/dashboard/gym-density/live', [GymDensityController::class, 'live'])->name('gym.density.live');

        // Rewards
        Route::get('/dashboard/rewards',                    [RewardController::class, 'index'])->name('rewards.index');
        Route::get('/dashboard/rewards/{id}/redeem-ticket', [RewardController::class, 'redeemTicket'])->name('rewards.redeem-ticket');
        Route::post('/dashboard/rewards/{id}/redeem',       [RewardController::class, 'redeem'])->name('rewards.redeem');

        // Reservasi
        Route::get('/reservasi',         [ReservationController::class, 'index'])->name('reservasi');
        Route::get('/reservasi/slots',   [ReservationController::class, 'slots'])->name('reservasi.slots');
        Route::post('/reservasi',        [ReservationController::class, 'store'])->name('reservasi.store');
        Route::delete('/reservasi/{id}', [ReservationController::class, 'destroy'])->name('reservasi.destroy');

        // Personal Trainer
        Route::get('/personal-trainer', fn() => view('components.personal-trainer.personal-trainer'))->name('personal-trainer.index');
    });
});