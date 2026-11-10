<?php

namespace Database\Seeders;

use App\Models\PointLog;
use App\Models\User;
use Illuminate\Database\Seeder;

class PointTestSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'excell22@gmail.com')->firstOrFail();
        $user->increment('points', 1000);
        PointLog::create([
            'user_id'     => $user->id,
            'points'      => 1000,
            'type'        => 'gym_visit',
            'description' => 'Test poin guis',
        ]);

        $this->command->info("✓ Berhasil tambah 1000 poin ke {$user->name} ({$user->email})");
        $this->command->info("  Total poin sekarang: {$user->fresh()->points} pts");
    }
}