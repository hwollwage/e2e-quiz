<?php

namespace App\Services;

use App\Models\PointLog;
use App\Models\User;
use Carbon\Carbon;

class StreakService
{
    const STREAK_MILESTONES = [
        3  => 50,
        7  => 150,
        14 => 300,
        30 => 750,
        60 => 1500,
        90 => 3000,
    ];

    const BASE_POINTS      = 10;
    const STREAK_BONUS_PER = 2;
    const STREAK_CAP_DAYS  = 30;
    public function processGymVisit(User $user): array
    {
        $today        = Carbon::today();
        $lastVisit    = $user->last_login_date
            ? Carbon::parse($user->last_login_date)
            : null;

        if ($lastVisit && $lastVisit->isSameDay($today)) {
            return ['already_claimed' => true];
        }

        $prevStreak  = $user->streak_days;
        $isReset     = false;

        if ($lastVisit && $lastVisit->isSameDay($today->copy()->subDay())) {
            $newStreak = $prevStreak + 1;
        } else {
            $newStreak = 1;
            $isReset   = $prevStreak > 0;
        }

        $streakBonus  = min($newStreak - 1, self::STREAK_CAP_DAYS) * self::STREAK_BONUS_PER;
        $pointsEarned = self::BASE_POINTS + $streakBonus;

        $milestoneBonus       = 0;
        $milestoneDescription = null;
        if (isset(self::STREAK_MILESTONES[$newStreak])) {
            $milestoneBonus       = self::STREAK_MILESTONES[$newStreak];
            $milestoneDescription = "Bonus milestone streak {$newStreak} hari! 🔥";
        }

        $totalPoints = $pointsEarned + $milestoneBonus;
        $user->points        += $totalPoints;
        $user->streak_days    = $newStreak;
        $user->longest_streak = max($user->longest_streak, $newStreak);
        $user->last_login_date = $today;
        $user->total_logins  += 1;
        $user->save();
        PointLog::create([
            'user_id'     => $user->id,
            'points'      => $pointsEarned,
            'type'        => 'gym_visit',
            'description' => "Gym visit (streak {$newStreak} hari)",
        ]);

        if ($milestoneBonus > 0) {
            PointLog::create([
                'user_id'     => $user->id,
                'points'      => $milestoneBonus,
                'type'        => 'streak_bonus',
                'description' => $milestoneDescription,
            ]);
        }

        return [
            'already_claimed'       => false,
            'streak'                => $newStreak,
            'prev_streak'           => $prevStreak,
            'is_reset'              => $isReset,
            'points_earned'         => $pointsEarned,
            'milestone_bonus'       => $milestoneBonus,
            'total_points_earned'   => $totalPoints,
            'total_points'          => $user->points,
            'is_milestone'          => $milestoneBonus > 0,
            'milestone_description' => $milestoneDescription,
            'next_milestone'        => $this->getNextMilestone($newStreak),
        ];
    }

    public function deductPoints(User $user, int $amount, string $description): bool
    {
        if ($user->points < $amount) {
            return false;
        }

        $user->decrement('points', $amount);

        PointLog::create([
            'user_id'     => $user->id,
            'points'      => -$amount,
            'type'        => 'redeem',
            'description' => $description,
        ]);

        return true;
    }

    private function getNextMilestone(int $currentStreak): ?array
    {
        foreach (self::STREAK_MILESTONES as $day => $bonus) {
            if ($day > $currentStreak) {
                return [
                    'day'       => $day,
                    'bonus'     => $bonus,
                    'remaining' => $day - $currentStreak,
                ];
            }
        }
        return null;
    }
}