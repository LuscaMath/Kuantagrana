<?php

namespace App\Services;

use App\Models\Environment;
use App\Models\User;

class EnvironmentExperienceService
{
    public function getMapData(User $user): array
    {
        $environments = Environment::query()
            ->active()
            ->orderBy('display_order')
            ->get()
            ->map(fn (Environment $environment) => [
                'environment' => $environment,
                'summary' => $this->getEnvironmentSummary($user, $environment),
                'highlights' => $environment->getHighlights(),
                'theme' => $environment->getTheme(),
            ]);

        return [
            'environments' => $environments,
        ];
    }

    public function getEnvironmentPageData(User $user, string $slug): array
    {
        $environment = Environment::query()
            ->active()
            ->where('slug', $slug)
            ->firstOrFail();

        $highlights = $environment->getHighlights();

        return [
            'environment' => $environment,
            'summary' => $this->getEnvironmentSummary($user, $environment),
            'highlights' => $highlights,
            'theme' => $environment->getTheme(),
            'tips' => $environment->tips()
                ->where('is_active', true)
                ->orderBy('display_order')
                ->take(4)
                ->get(),
            'challenges' => $environment->challenges()
                ->where('is_active', true)
                ->take(3)
                ->get(),
            'recentTransactions' => $user->financialTransactions()
                ->with('category')
                ->where('environment_id', $environment->id)
                ->latest('transaction_date')
                ->take(5)
                ->get(),
            'recentGoals' => $user->goals()
                ->where('environment_id', $environment->id)
                ->latest()
                ->take(4)
                ->get(),
            'recentItems' => $user->householdItems()
                ->where('environment_id', $environment->id)
                ->latest()
                ->take(5)
                ->get(),
            'actionLinks' => $this->getActionLinks($environment),
            'supportsTransactions' => $environment->supportsFeature('transactions'),
            'supportsItems' => $environment->supportsFeature('items'),
            'supportsGoals' => $environment->supportsFeature('goals'),
        ];
    }

    protected function getEnvironmentSummary(User $user, Environment $environment): array
    {
        $transactions = $user->financialTransactions()->where('environment_id', $environment->id);
        $goals = $user->goals()->where('environment_id', $environment->id);
        $items = $user->householdItems()->where('environment_id', $environment->id);

        return [
            'transactions_count' => (clone $transactions)->count(),
            'income_total' => (clone $transactions)->where('type', 'income')->sum('amount'),
            'expense_total' => (clone $transactions)->where('type', 'expense')->sum('amount'),
            'goals_count' => (clone $goals)->count(),
            'goals_completed' => (clone $goals)->where('status', 'completed')->count(),
            'items_count' => (clone $items)->count(),
            'low_stock_items' => (clone $items)->whereColumn('quantity', '<=', 'minimum_quantity')->count(),
        ];
    }

    protected function getActionLinks(Environment $environment): array
    {
        return [
            'transactions' => route('financial-transactions.index', ['environment_id' => $environment->id]),
            'transactions_create' => route('financial-transactions.create', ['environment_id' => $environment->id]),
            'goals' => route('goals.index', ['environment_id' => $environment->id]),
            'goals_create' => route('goals.create', ['environment_id' => $environment->id]),
            'items' => route('household-items.index', ['environment_id' => $environment->id]),
            'items_create' => route('household-items.create', ['environment_id' => $environment->id]),
        ];
    }
}
