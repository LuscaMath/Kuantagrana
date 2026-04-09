<?php

namespace Database\Seeders;

use App\Models\Challenge;
use App\Models\Environment;
use Illuminate\Database\Seeder;

class ChallengeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $environments = Environment::query()->pluck('id', 'slug');

        $challenges = [
            ['name' => 'Registrar 3 Transações', 'slug' => 'registrar-3-transacoes', 'environment_slug' => 'casa', 'description' => 'Cadastre três movimentações financeiras.', 'goal_metric' => 'transactions_created', 'goal_target' => 3, 'points_reward' => 40],
            ['name' => 'Criar 1 Meta', 'slug' => 'criar-1-meta', 'environment_slug' => 'escola', 'description' => 'Defina uma meta financeira para começar a se planejar.', 'goal_metric' => 'goals_created', 'goal_target' => 1, 'points_reward' => 30],
            ['name' => 'Concluir 1 Meta', 'slug' => 'concluir-1-meta', 'environment_slug' => 'parque-de-diversoes', 'description' => 'Complete uma meta financeira para ganhar experiência.', 'goal_metric' => 'goals_completed', 'goal_target' => 1, 'points_reward' => 100],
            ['name' => 'Cadastrar 5 Itens Domésticos', 'slug' => 'cadastrar-5-itens-domesticos', 'environment_slug' => 'casa', 'description' => 'Organize cinco itens domésticos no sistema.', 'goal_metric' => 'household_items_created', 'goal_target' => 5, 'points_reward' => 35],
        ];

        foreach ($challenges as $challenge) {
            Challenge::updateOrCreate(
                ['slug' => $challenge['slug']],
                [
                    'environment_id' => $environments[$challenge['environment_slug']] ?? null,
                    'name' => $challenge['name'],
                    'description' => $challenge['description'],
                    'goal_metric' => $challenge['goal_metric'],
                    'goal_target' => $challenge['goal_target'],
                    'points_reward' => $challenge['points_reward'],
                    'start_date' => null,
                    'end_date' => null,
                    'is_active' => true,
                ]
            );
        }
    }
}
