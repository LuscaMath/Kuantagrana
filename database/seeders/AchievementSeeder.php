<?php

namespace Database\Seeders;

use App\Models\Achievement;
use App\Models\Environment;
use Illuminate\Database\Seeder;

class AchievementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $environments = Environment::query()->pluck('id', 'slug');

        $achievements = [
            ['name' => 'Primeiro Registro', 'slug' => 'primeiro-registro', 'environment_slug' => 'casa', 'description' => 'Registrou sua primeira transacao financeira.', 'points_reward' => 20, 'badge_color' => '#8ed0ff'],
            ['name' => 'Sonho em Andamento', 'slug' => 'sonho-em-andamento', 'environment_slug' => 'escola', 'description' => 'Criou sua primeira meta financeira.', 'points_reward' => 30, 'badge_color' => '#7ccf7a'],
            ['name' => 'Meta Concluida', 'slug' => 'meta-concluida', 'environment_slug' => 'parque-de-diversoes', 'description' => 'Concluiu uma meta financeira pela primeira vez.', 'points_reward' => 80, 'badge_color' => '#f0b429'],
        ];

        foreach ($achievements as $achievement) {
            Achievement::updateOrCreate(
                ['slug' => $achievement['slug']],
                [
                    'environment_id' => $environments[$achievement['environment_slug']] ?? null,
                    'name' => $achievement['name'],
                    'icon' => 'badge',
                    'badge_color' => $achievement['badge_color'],
                    'description' => $achievement['description'],
                    'points_reward' => $achievement['points_reward'],
                    'is_active' => true,
                ]
            );
        }
    }
}
