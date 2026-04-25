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
            ['name' => 'Registrar 3 Transacoes', 'slug' => 'registrar-3-transacoes', 'environment_slug' => 'casa', 'description' => 'Cadastre tres movimentacoes financeiras.', 'goal_metric' => 'transactions_created', 'goal_target' => 3, 'points_reward' => 40],
            ['name' => 'Criar 1 Meta', 'slug' => 'criar-1-meta', 'environment_slug' => 'escola', 'description' => 'Defina uma meta financeira para comecar a se planejar.', 'goal_metric' => 'goals_created', 'goal_target' => 1, 'points_reward' => 30],
            ['name' => 'Concluir 1 Meta', 'slug' => 'concluir-1-meta', 'environment_slug' => 'parque-de-diversoes', 'description' => 'Complete uma meta financeira para ganhar experiencia.', 'goal_metric' => 'goals_completed', 'goal_target' => 1, 'points_reward' => 100],
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
