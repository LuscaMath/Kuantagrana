<?php

namespace App\Services;

use App\Models\Challenge;
use App\Models\Level;
use App\Models\User;

class DashboardService
{
    public function getData(User $user): array
    {
        $user->loadMissing(['level', 'userAchievements.achievement', 'userChallenges.challenge']);

        $currentLevel = $user->level;
        $nextLevel = $currentLevel
            ? Level::query()
                ->where('is_active', true)
                ->where('min_points', '>', $currentLevel->min_points)
                ->orderBy('min_points')
                ->first()
            : Level::query()->where('is_active', true)->orderBy('min_points')->first();

        $progressToNextLevel = $this->calculateLevelProgress($user->points, $currentLevel?->min_points, $nextLevel?->min_points);

        $monthlyTransactions = $user->financialTransactions()
            ->whereBetween('transaction_date', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()])
            ->selectRaw("type, SUM(amount) as total")
            ->groupBy('type')
            ->pluck('total', 'type');

        $activeGoals = $user->goals()->where('status', 'active')->count();
        $completedGoals = $user->goals()->where('status', 'completed')->count();

        return [
            'user' => $user,
            'currentLevel' => $currentLevel,
            'nextLevel' => $nextLevel,
            'progressToNextLevel' => $progressToNextLevel,
            'stats' => [
                'transactions_count' => $user->financialTransactions()->count(),
                'income_month' => (float) ($monthlyTransactions['income'] ?? 0),
                'expense_month' => (float) ($monthlyTransactions['expense'] ?? 0),
                'active_goals' => $activeGoals,
                'completed_goals' => $completedGoals,
            ],
            'recentAchievements' => $user->userAchievements()
                ->with('achievement')
                ->whereNotNull('unlocked_at')
                ->latest('unlocked_at')
                ->take(3)
                ->get(),
            'challenges' => $user->userChallenges()
                ->with('challenge')
                ->orderByRaw("case when status = 'in_progress' then 0 else 1 end")
                ->orderByDesc('updated_at')
                ->take(4)
                ->get(),
            'availableChallenges' => Challenge::query()
                ->where('is_active', true)
                ->whereNotIn('id', $user->userChallenges()->select('challenge_id'))
                ->orderBy('name')
                ->take(3)
                ->get(),
        ];
    }

    protected function calculateLevelProgress(int $points, ?int $currentMinPoints, ?int $nextMinPoints): int
    {
        if (! $nextMinPoints || $currentMinPoints === null) {
            return 100;
        }

        $range = max(1, $nextMinPoints - $currentMinPoints);
        $progress = (($points - $currentMinPoints) / $range) * 100;

        return (int) max(0, min(100, round($progress)));
    }
}
