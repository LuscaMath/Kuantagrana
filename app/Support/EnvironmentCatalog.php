<?php

namespace App\Support;

class EnvironmentCatalog
{
    /**
     * @return array<string, mixed>
     */
    public static function definition(string $slug): array
    {
        return self::definitions()[$slug] ?? self::defaultDefinition();
    }

    /**
     * @return array<int, string>
     */
    public static function slugsFor(string $capability): array
    {
        return collect(self::definitions())
            ->filter(fn (array $definition) => in_array($capability, $definition['capabilities'], true))
            ->keys()
            ->values()
            ->all();
    }

    public static function supports(string $slug, string $capability): bool
    {
        return in_array($capability, self::definition($slug)['capabilities'], true);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    protected static function definitions(): array
    {
        return [
            'casa' => [
                'kind' => 'home',
                'capabilities' => ['transactions', 'items', 'income_transactions'],
                'highlights' => [
                    'title' => 'Centro financeiro da rotina',
                    'description' => 'A Casa concentra receitas, contas fixas, despesas da base da vida financeira e itens domesticos.',
                    'focus' => ['Receitas', 'Contas da casa', 'Itens domesticos'],
                    'kind' => 'home',
                ],
                'theme' => [
                    'card_class' => 'environment-theme-home',
                    'label' => 'Lar aconchegante',
                ],
            ],
            'escola' => [
                'kind' => 'educational',
                'capabilities' => ['education'],
                'highlights' => [
                    'title' => 'Educacao financeira e orientacao',
                    'description' => 'A Escola existe para aprender: dicas, conceitos e apoio para tomar decisoes financeiras melhores.',
                    'focus' => ['Dicas financeiras', 'Planejamento', 'Aprendizado'],
                    'kind' => 'educational',
                ],
                'theme' => [
                    'card_class' => 'environment-theme-school',
                    'label' => 'Espaco de aprendizado',
                ],
            ],
            'mercado' => [
                'kind' => 'operational',
                'capabilities' => ['transactions', 'items'],
                'highlights' => [
                    'title' => 'Compras e alimentacao',
                    'description' => 'Use este espaco para organizar despesas com mercado, alimentacao e reposicao do mes.',
                    'focus' => ['Compras do mes', 'Alimentacao', 'Reposicao'],
                    'kind' => 'operational',
                ],
                'theme' => [
                    'card_class' => 'environment-theme-market',
                    'label' => 'Corredor de compras',
                ],
            ],
            'farmacia' => [
                'kind' => 'operational',
                'capabilities' => ['transactions', 'items'],
                'highlights' => [
                    'title' => 'Saude e higiene',
                    'description' => 'Concentre aqui remedios, higiene pessoal e outros gastos essenciais de cuidado.',
                    'focus' => ['Saude', 'Higiene', 'Essenciais'],
                    'kind' => 'operational',
                ],
                'theme' => [
                    'card_class' => 'environment-theme-pharmacy',
                    'label' => 'Cuidado e saude',
                ],
            ],
            'parque-de-diversoes' => [
                'kind' => 'gamified',
                'capabilities' => ['goals'],
                'highlights' => [
                    'title' => 'Metas, recompensas e evolucao',
                    'description' => 'O Parque e o espaco das metas, do progresso e da parte ludica do sistema.',
                    'focus' => ['Metas', 'Conquistas', 'Desafios'],
                    'kind' => 'gamified',
                ],
                'theme' => [
                    'card_class' => 'environment-theme-park',
                    'label' => 'Recompensa e progresso',
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected static function defaultDefinition(): array
    {
        return [
            'kind' => 'default',
            'capabilities' => [],
            'highlights' => [
                'title' => 'Ambiente',
                'description' => 'Acompanhe as informacoes relacionadas a este contexto do mapa.',
                'focus' => [],
                'kind' => 'default',
            ],
            'theme' => [
                'card_class' => '',
                'label' => 'Ambiente',
            ],
        ];
    }
}
