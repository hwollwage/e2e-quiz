<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Database\Seeders\RewardSeeder;
use Database\Seeders\ProgressLogSeeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $accounts = [
            [
                'name' => 'Owner Gym',
                'email' => 'owner@gymin.com',
                'password' => Hash::make('password'),
                'role' => 'owner',
            ],
            [
                'name' =>'Resepsionis',
                'email' => 'resepsionis@gymin.com',
                'password' => Hash::make('password'),
                'role' => 'receptionist',
            ],
            [
                'name' => 'Member 1',
                'email' => 'member1@gymin.com',
                'password' => Hash::make('password'),
                'role' => 'member',
            ],
        ];

        foreach($accounts as $account) {
            User::firstOrCreate(
                ['email' => $account['email']], $account
            );
        }

        $this->call([
            RewardSeeder::class,
            ProgressLogSeeder::class,
        ]);
    }
}
