<?php

namespace App\Services;

use App\Models\FinancialTransaction;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class FinancialTransactionService
{
    public function __construct(
        private readonly GamificationService $gamificationService,
    ) {
    }

    public function getPaginatedForUser(User $user, array $filters = []): LengthAwarePaginator
    {
        return FinancialTransaction::query()
            ->with(['category', 'environment'])
            ->whereBelongsTo($user)
            ->when($filters['environment_id'] ?? null, fn (Builder $query, string $environmentId) => $query->where('environment_id', $environmentId))
            ->when($filters['type'] ?? null, fn (Builder $query, string $type) => $query->where('type', $type))
            ->when($filters['month'] ?? null, function (Builder $query, string $month) {
                $date = Carbon::createFromFormat('Y-m', $month);

                $query->whereBetween('transaction_date', [
                    $date->copy()->startOfMonth()->toDateString(),
                    $date->copy()->endOfMonth()->toDateString(),
                ]);
            })
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();
    }

    public function getSummary(User $user, ?string $month = null, ?string $environmentId = null): array
    {
        $date = $month
            ? Carbon::createFromFormat('Y-m', $month)
            : now();

        $baseQuery = FinancialTransaction::query()
            ->whereBelongsTo($user)
            ->when($environmentId, fn (Builder $query, string $value) => $query->where('environment_id', $value))
            ->whereBetween('transaction_date', [
                $date->copy()->startOfMonth()->toDateString(),
                $date->copy()->endOfMonth()->toDateString(),
            ]);

        $income = (clone $baseQuery)->where('type', 'income')->sum('amount');
        $expense = (clone $baseQuery)->where('type', 'expense')->sum('amount');

        return [
            'income' => $income,
            'expense' => $expense,
            'balance' => $income - $expense,
            'month_label' => $date->translatedFormat('F \d\e Y'),
        ];
    }

    public function create(User $user, array $data): FinancialTransaction
    {
        $transaction = $user->financialTransactions()->create($data);

        $this->gamificationService->awardPoints($user, 10);
        $this->gamificationService->trackMetric($user, 'transactions_created');

        return $transaction;
    }

    public function update(FinancialTransaction $transaction, array $data): FinancialTransaction
    {
        $transaction->update($data);

        return $transaction;
    }

    public function delete(FinancialTransaction $transaction): void
    {
        $transaction->delete();
    }
}
