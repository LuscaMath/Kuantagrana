<?php

namespace App\Services;

use App\Models\Achievement;
use App\Models\Challenge;
use App\Models\Level;
use App\Models\UserAchievement;
use App\Models\UserChallenge;
use App\Models\User;
use Illuminate\Session\Store;
use Illuminate\Support\Carbon;

class GamificationService
{
    public function snapshot(User $user): array
    {
        $user->loadMissing(['level', 'userAchievements.achievement', 'userChallenges.challenge']);

        return [
            'points' => $user->points,
            'level_id' => $user->level_id,
            'level_name' => $user->level?->name,
            'achievement_ids' => $user->userAchievements->pluck('achievement_id')->all(),
            'completed_challenge_ids' => $user->userChallenges
                ->where('status', 'completed')
                ->pluck('challenge_id')
                ->all(),
        ];
    }

    public function buildFeedback(User $user, array $snapshot, ?string $contextMessage = null): array
    {
        $user->refresh();
        $user->loadMissing(['level', 'userAchievements.achievement', 'userChallenges.challenge']);

        $feedback = [];
        $pointsGained = max(0, $user->points - ($snapshot['points'] ?? 0));

        if ($pointsGained > 0) {
            $feedback[] = [
                'type' => 'points',
                'title' => "+{$pointsGained} pontos",
                'body' => $contextMessage ?? 'Seu progresso foi atualizado.',
            ];
        }

        if (($snapshot['level_id'] ?? null) !== $user->level_id && $user->level) {
            $feedback[] = [
                'type' => 'level',
                'title' => 'Nivel alcancado',
                'body' => "Voce chegou ao nivel {$user->level->name}.",
            ];
        }

        $newAchievements = $user->userAchievements
            ->whereNotIn('achievement_id', $snapshot['achievement_ids'] ?? [])
            ->filter(fn (UserAchievement $achievement) => $achievement->achievement !== null)
            ->values();

        foreach ($newAchievements as $achievement) {
            $feedback[] = [
                'type' => 'achievement',
                'title' => 'Conquista desbloqueada',
                'body' => $achievement->achievement->name,
            ];
        }

        $newCompletedChallenges = $user->userChallenges
            ->where('status', 'completed')
            ->whereNotIn('challenge_id', $snapshot['completed_challenge_ids'] ?? [])
            ->filter(fn (UserChallenge $challenge) => $challenge->challenge !== null)
            ->values();

        foreach ($newCompletedChallenges as $challenge) {
            $feedback[] = [
                'type' => 'challenge',
                'title' => 'Quest concluida',
                'body' => $challenge->challenge->name,
            ];
        }

        return $feedback;
    }

    public function flashFeedback(Store $session, User $user, array $snapshot, ?string $contextMessage = null): void
    {
        $feedback = $this->buildFeedback($user, $snapshot, $contextMessage);

        if ($feedback !== []) {
            $session->flash('gamification_feedback', $feedback);
        }
    }

    public function awardPoints(User $user, int $points): void
    {
        if ($points <= 0) {
            return;
        }

        $user->increment('points', $points);
        $user->refresh();

        $this->syncLevel($user);
    }

    public function syncLevel(User $user): void
    {
        $level = Level::query()
            ->where('is_active', true)
            ->where('min_points', '<=', $user->points)
            ->where(function ($query) use ($user) {
                $query->whereNull('max_points')
                    ->orWhere('max_points', '>=', $user->points);
            })
            ->orderByDesc('min_points')
            ->first();

        if ($level && $user->level_id !== $level->id) {
            $user->update(['level_id' => $level->id]);
        }
    }

    public function trackMetric(User $user, string $metric): void
    {
        $progress = match ($metric) {
            'transactions_created' => $user->financialTransactions()->count(),
            'goals_created' => $user->goals()->count(),
            'goals_completed' => $user->goals()->where('status', 'completed')->count(),
            default => 0,
        };

        if ($progress <= 0) {
            return;
        }

        $this->syncChallenges($user, $metric, $progress);
        $this->syncAchievements($user, $metric, $progress);
    }

    protected function syncChallenges(User $user, string $metric, int $progress): void
    {
        $challenges = Challenge::query()
            ->where('is_active', true)
            ->where('goal_metric', $metric)
            ->get();

        foreach ($challenges as $challenge) {
            $userChallenge = UserChallenge::query()->firstOrCreate(
                [
                    'user_id' => $user->id,
                    'challenge_id' => $challenge->id,
                ],
                [
                    'progress' => 0,
                    'status' => 'in_progress',
                    'started_at' => now(),
                ],
            );

            $wasCompleted = $userChallenge->status === 'completed';
            $isCompleted = $progress >= $challenge->goal_target;

            $userChallenge->update([
                'progress' => $progress,
                'status' => $isCompleted ? 'completed' : 'in_progress',
                'started_at' => $userChallenge->started_at ?? now(),
                'completed_at' => $isCompleted ? ($userChallenge->completed_at ?? now()) : null,
            ]);

            if (! $wasCompleted && $isCompleted) {
                $this->awardPoints($user, $challenge->points_reward);
            }
        }
    }

    protected function syncAchievements(User $user, string $metric, int $progress): void
    {
        $achievementSlug = match ($metric) {
            'transactions_created' => $progress >= 1 ? 'primeiro-registro' : null,
            'goals_created' => $progress >= 1 ? 'sonho-em-andamento' : null,
            'goals_completed' => $progress >= 1 ? 'meta-concluida' : null,
            default => null,
        };

        if (! $achievementSlug) {
            return;
        }

        $achievement = Achievement::query()
            ->where('is_active', true)
            ->where('slug', $achievementSlug)
            ->first();

        if (! $achievement) {
            return;
        }

        $unlocked = UserAchievement::query()->firstOrCreate(
            [
                'user_id' => $user->id,
                'achievement_id' => $achievement->id,
            ],
            [
                'unlocked_at' => Carbon::now(),
            ],
        );

        if ($unlocked->wasRecentlyCreated) {
            $this->awardPoints($user, $achievement->points_reward);
        }
    }
}
