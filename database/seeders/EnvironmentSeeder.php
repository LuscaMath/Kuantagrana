<?php

namespace Database\Seeders;

use App\Models\Environment;
use Illuminate\Database\Seeder;

class EnvironmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $environments = [
            ['name' => 'Casa', 'slug' => 'casa', 'icon' => 'home', 'theme_color' => '#8f5f33', 'description' => 'Organização das finanças e itens da rotina doméstica.', 'display_order' => 1],
            ['name' => 'Escola', 'slug' => 'escola', 'icon' => 'school', 'theme_color' => '#8ed0ff', 'description' => 'Aprendizado, metas e educação financeira.', 'display_order' => 2],
            ['name' => 'Mercado', 'slug' => 'mercado', 'icon' => 'shopping-cart', 'theme_color' => '#7ccf7a', 'description' => 'Compras do dia a dia e controle de consumo.', 'display_order' => 3],
            ['name' => 'Farmácia', 'slug' => 'farmacia', 'icon' => 'heart', 'theme_color' => '#df6b57', 'description' => 'Saúde, bem-estar e gastos essenciais.', 'display_order' => 4],
            ['name' => 'Parque de Diversões', 'slug' => 'parque-de-diversoes', 'icon' => 'star', 'theme_color' => '#f0b429', 'description' => 'Recompensas, desafios e conquistas.', 'display_order' => 5],
        ];

        foreach ($environments as $environment) {
            Environment::updateOrCreate(
                ['slug' => $environment['slug']],
                $environment + ['is_active' => true]
            );
        }
    }
}
