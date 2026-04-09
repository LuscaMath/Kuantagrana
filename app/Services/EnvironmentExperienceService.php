<?php

namespace App\Services;

use App\Models\Environment;
use App\Models\User;

class EnvironmentExperienceService
{
    public function getMapData(User $user): array
    {
        $environments = Environment::query()
            ->where('is_active', true)
            ->orderBy('display_order')
            ->get()
            ->map(fn (Environment $environment) => [
                'environment' => $environment,
                'summary' => $this->getEnvironmentSummary($user, $environment),
                'highlights' => $this->getEnvironmentHighlights($environment->slug),
                'theme' => $this->getEnvironmentTheme($environment->slug),
            ]);

        return [
            'environments' => $environments,
        ];
    }

    public function getEnvironmentPageData(User $user, string $slug): array
    {
        $environment = Environment::query()
            ->where('is_active', true)
            ->where('slug', $slug)
            ->firstOrFail();

        return [
            'environment' => $environment,
            'summary' => $this->getEnvironmentSummary($user, $environment),
            'highlights' => $this->getEnvironmentHighlights($environment->slug),
            'theme' => $this->getEnvironmentTheme($environment->slug),
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

    protected function getEnvironmentHighlights(string $slug): array
    {
        return match ($slug) {
            'casa' => [
                'title' => 'Contas fixas e rotina da casa',
                'description' => 'Aqui ficam despesas como agua, luz, aluguel, internet e o controle dos itens domesticos.',
                'focus' => ['Moradia', 'Itens da casa', 'Contas recorrentes'],
                'kind' => 'operational',
            ],
            'escola' => [
                'title' => 'Educacao financeira e planejamento',
                'description' => 'Ambiente voltado para informacoes, dicas e aprendizado financeiro para formar habitos melhores.',
                'focus' => ['Dicas financeiras', 'Planejamento', 'Aprendizado'],
                'kind' => 'educational',
            ],
            'mercado' => [
                'title' => 'Compras e alimentacao',
                'description' => 'Use este espaco para organizar despesas com mercado, alimentacao e reposicao do mes.',
                'focus' => ['Compras do mes', 'Alimentacao', 'Reposicao'],
                'kind' => 'operational',
            ],
            'farmacia' => [
                'title' => 'Saude e higiene',
                'description' => 'Concentre aqui remedios, higiene pessoal e outros gastos essenciais de cuidado.',
                'focus' => ['Saude', 'Higiene', 'Essenciais'],
                'kind' => 'operational',
            ],
            'parque-de-diversoes' => [
                'title' => 'Metas, recompensas e evolucao',
                'description' => 'O lado ludico do sistema: metas, desafios, conquistas e celebracao do progresso.',
                'focus' => ['Metas', 'Conquistas', 'Desafios'],
                'kind' => 'gamified',
            ],
            default => [
                'title' => 'Ambiente',
                'description' => 'Acompanhe as informacoes relacionadas a este contexto do mapa.',
                'focus' => [],
                'kind' => 'default',
            ],
        };
    }

    protected function getEnvironmentTheme(string $slug): array
    {
        return match ($slug) {
            'casa' => [
                'card_class' => 'environment-theme-home',
                'label' => 'Lar aconchegante',
            ],
            'escola' => [
                'card_class' => 'environment-theme-school',
                'label' => 'Espaco de aprendizado',
            ],
            'mercado' => [
                'card_class' => 'environment-theme-market',
                'label' => 'Corredor de compras',
            ],
            'farmacia' => [
                'card_class' => 'environment-theme-pharmacy',
                'label' => 'Cuidado e saude',
            ],
            'parque-de-diversoes' => [
                'card_class' => 'environment-theme-park',
                'label' => 'Recompensa e progresso',
            ],
            default => [
                'card_class' => '',
                'label' => 'Ambiente',
            ],
        };
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
