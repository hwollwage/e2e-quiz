<?php

namespace Database\Seeders;

use App\Models\ProgressLog;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProgressLogSeeder extends Seeder
{
    public function run(): void
    {
        
        $member = User::where('email', 'member1@gymin.com')->first();
        $member = User::where('email', ['member1@gymin.com', 'member2@gymin.com'])->first();
        
        if (!$member) return;

        $logs = [
            ['log_date' => '2026-04-01', 'weight_kg' => 82.00, 'muscle_mass_kg' => 35.00, 'body_fat_pct' => 22.00, 'workout_notes' => 'tekan Ctrl+c Ctrl+v ChatGPT 3x10'],
            ['log_date' => '2026-04-02', 'weight_kg' => 85.50, 'muscle_mass_kg' => 27.20, 'body_fat_pct' => 19.50, 'workout_notes' => 'Pecut Claude code 4x18 '],
            ['log_date' => '2026-04-03', 'weight_kg' => 79.00, 'muscle_mass_kg' => 30.50, 'body_fat_pct' => 15.00, 'workout_notes' => 'Teriak antek-antek aseng 2x10'],
            ['log_date' => '2026-04-04', 'weight_kg' => 75.30, 'muscle_mass_kg' => 31.80, 'body_fat_pct' => 19.50, 'workout_notes' => 'Bilang "AKAN TERSEDIA 19 JUTA LAPANGAN PEKERJAAN"'],
            ['log_date' => '2026-04-05', 'weight_kg' => 81.80, 'muscle_mass_kg' => 39.00, 'body_fat_pct' => 25.00, 'workout_notes' => 'Sebat rokok Marlboro 3x8'],
            ['log_date' => '2026-04-06', 'weight_kg' => 79.20, 'muscle_mass_kg' => 40.30, 'body_fat_pct' => 19.50, 'workout_notes' => 'Ngehujat pemerintah 2x9'],
            ['log_date' => '2026-04-07', 'weight_kg' => 76.50, 'muscle_mass_kg' => 37.80, 'body_fat_pct' => 17.00, 'workout_notes' => 'Makan MBG 2x8'],
            ['log_date' => '2026-04-08', 'weight_kg' => 74.50, 'muscle_mass_kg' => 40.80, 'body_fat_pct' => 12.00, 'workout_notes' => 'Main Roblox 30 menit 2x8'],
        ];

        foreach ($logs as $log) {
            ProgressLog::firstOrCreate(
                ['user_id' => $member->id, 'log_date' => $log['log_date']],
                array_merge(
                    ['user_id' => $member->id], $log
                )
            );
        }
    }
}
