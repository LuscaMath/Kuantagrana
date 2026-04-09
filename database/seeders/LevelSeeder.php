<?php

namespace Database\Seeders;

use App\Models\Level;
use Illuminate\Database\Seeder;

class LevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $levels = [
            ['name' => 'Iniciante', 'min_points' => 0, 'max_points' => 99, 'badge_color' => '#8ed0ff', 'description' => 'Começando a organizar a vida financeira.'],
            ['name' => 'Aprendiz', 'min_points' => 100, 'max_points' => 249, 'badge_color' => '#7ccf7a', 'description' => 'Já criou hábitos financeiros importantes.'],
            ['name' => 'Planejador', 'min_points' => 250, 'max_points' => 499, 'badge_color' => '#f0b429', 'description' => 'Controla melhor receitas, despesas e metas.'],
            ['name' => 'Guardião', 'min_points' => 500, 'max_points' => 999, 'badge_color' => '#d48912', 'description' => 'Mantém constância e boas decisões financeiras.'],
            ['name' => 'Mestre das Moedas', 'min_points' => 1000, 'max_points' => null, 'badge_color' => '#8f5f33', 'description' => 'Alcançou alto domínio no Vale das Moedas.'],
        ];

        foreach ($levels as $level) {
            Level::updateOrCreate(
                ['name' => $level['name']],
                $level + ['is_active' => true]
            );
        }
    }
}
