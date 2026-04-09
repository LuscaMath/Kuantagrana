<?php

namespace App\Services;

use App\Models\Goal;
use App\Models\GoalContribution;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class GoalService
{
    public function __construct(
        private readonly GamificationService $gamificationService,
    ) {
    }

    public function getPaginatedForUser(User $user, array $filters = []): LengthAwarePaginator
    {
        return Goal::query()
            ->with(['environment', 'contributions'])
            ->whereBelongsTo($user)
            ->when($filters['environment_id'] ?? null, fn (Builder $query, string $environmentId) => $query->where('environment_id', $environmentId))
            ->orderByRaw("case when status = 'active' then 0 when status = 'completed' then 1 else 2 end")
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();
    }

    public function create(User $user, array $data): Goal
    {
        return DB::transaction(function () use ($user, $data) {
            $goal = $user->goals()->create([
                ...$data,
                'current_amount' => 0,
                'completed_at' => $data['status'] === 'completed' ? now() : null,
            ]);

            $this->gamificationService->awardPoints($user, 15);
            $this->gamificationService->trackMetric($user, 'goals_created');

            if ($goal->status === 'completed') {
                $this->gamificationService->awardPoints($user, 50);
                $this->gamificationService->trackMetric($user, 'goals_completed');
            }

            return $goal;
        });
    }

    public function update(Goal $goal, array $data): Goal
    {
        return DB::transaction(function () use ($goal, $data) {
            $wasCompleted = $goal->status === 'completed';

            $goal->update([
                ...$data,
                'completed_at' => $data['status'] === 'completed'
                    ? ($goal->completed_at ?? now())
                    : null,
            ]);

            if (! $wasCompleted && $goal->status === 'completed') {
                $this->gamificationService->awardPoints($goal->user, 50);
                $this->gamificationService->trackMetric($goal->user, 'goals_completed');
            }

            return $goal->refresh();
        });
    }

    public function delete(Goal $goal): void
    {
        $goal->delete();
    }

    public function addContribution(Goal $goal, array $data): GoalContribution
    {
        return DB::transaction(function () use ($goal, $data) {
            $contribution = $goal->contributions()->create($data);

            $goal->increment('current_amount', $data['amount']);
            $goal->refresh();

            if ($goal->current_amount >= $goal->target_amount && $goal->status !== 'completed') {
                $goal->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);

                $this->gamificationService->awardPoints($goal->user, 50);
                $this->gamificationService->trackMetric($goal->user, 'goals_completed');
            }

            return $contribution;
        });
    }
}
