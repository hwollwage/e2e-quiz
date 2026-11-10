<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Reservation;
use App\Models\ChatMessage;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'gender',
        'email',
        'password',
        'role',
        'points',
        'streak_days',
        'longest_streak',
        'last_login_date',
        'total_logins',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    public function getGenderLabelAttribute(): string
    {
        return match ($this->gender) {
            'male'   => 'Laki-laki',
            'female' => 'Perempuan',
            default  => '-',
        };
    }

    public function progressLogs()
    {
        return $this->hasMany(ProgressLog::class);
    }

    public function reservations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function activeReservation(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Reservation::class)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('session_date', '>=', today())
            ->orderBy('session_date')
            ->orderBy('session_start');
    }

    public function sentMessages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ChatMessage::class, 'sender_id');
    }

    public function receivedMessages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ChatMessage::class, 'receiver_id');
    }

    public function unreadMessageCount(): int
    {
        return $this->receivedMessages()->whereNull('read_at')->count();
    }

    public function isReceptionist(): bool
    {
        return $this->role === 'receptionist';
    }

    public function isMember(): bool
    {
        return $this->role === 'member';
    }
}