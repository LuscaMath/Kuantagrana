<?php

namespace App\Services;

use App\Models\HouseholdItem;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class HouseholdItemService
{
    public function __construct(
        private readonly GamificationService $gamificationService,
    ) {
    }

    public function getPaginatedForUser(User $user, array $filters = []): LengthAwarePaginator
    {
        return HouseholdItem::query()
            ->with('environment')
            ->whereBelongsTo($user)
            ->when($filters['environment_id'] ?? null, fn (Builder $query, string $environmentId) => $query->where('environment_id', $environmentId))
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();
    }

    public function create(User $user, array $data): HouseholdItem
    {
        $item = $user->householdItems()->create($data);

        $this->gamificationService->awardPoints($user, 5);
        $this->gamificationService->trackMetric($user, 'household_items_created');

        return $item;
    }

    public function update(HouseholdItem $item, array $data): HouseholdItem
    {
        $item->update($data);

        return $item;
    }

    public function delete(HouseholdItem $item): void
    {
        $item->delete();
    }
}
