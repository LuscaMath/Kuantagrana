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
                'capabilities' => ['transactions', 'income_transactions'],
                'highlights' => [
                    'title' => 'Centro financeiro da rotina',
                    'description' => 'A Casa e a base do controle financeiro: concentra entradas, contas fixas, despesas da rotina e a visao mais completa da vida financeira.',
                    'focus' => ['Receitas', 'Despesas', 'Contas da casa'],
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
                    'description' => 'A Escola existe para aprender melhor antes de agir, com dicas e orientacoes para tomar decisoes financeiras mais conscientes.',
                    'focus' => ['Dicas financeiras', 'Orientacao', 'Aprendizado'],
                    'kind' => 'educational',
                ],
                'theme' => [
                    'card_class' => 'environment-theme-school',
                    'label' => 'Espaco de aprendizado',
                ],
            ],
            'mercado' => [
                'kind' => 'operational',
                'capabilities' => ['transactions'],
                'highlights' => [
                    'title' => 'Compras e alimentacao',
                    'description' => 'Use este ambiente para registrar gastos de mercado, compras do mes e despesas ligadas a alimentacao e reposicao.',
                    'focus' => ['Compras do mes', 'Despesas do mercado', 'Reposicao'],
                    'kind' => 'operational',
                ],
                'theme' => [
                    'card_class' => 'environment-theme-market',
                    'label' => 'Corredor de compras',
                ],
            ],
            'farmacia' => [
                'kind' => 'operational',
                'capabilities' => ['transactions'],
                'highlights' => [
                    'title' => 'Saude e higiene',
                    'description' => 'Concentre aqui gastos com saude, higiene pessoal, remedios e outros cuidados essenciais do dia a dia.',
                    'focus' => ['Saude', 'Higiene', 'Gastos essenciais'],
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
                    'description' => 'O Parque e o espaco das metas, do progresso acumulado e da camada de recompensa da jornada.',
                    'focus' => ['Metas', 'Progresso', 'Conquistas'],
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
