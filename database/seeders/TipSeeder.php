<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Environment;
use App\Models\Tip;
use Illuminate\Database\Seeder;

class TipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $environments = Environment::query()->pluck('id', 'slug');
        $categories = Category::query()->pluck('id', 'slug');

        $tips = [
            ['title' => 'Separe gastos fixos e variáveis', 'environment_slug' => 'casa', 'category_slug' => 'moradia', 'content' => 'Dividir os gastos por tipo ajuda a entender melhor para onde seu dinheiro está indo.', 'display_order' => 1],
            ['title' => 'Leve uma lista ao mercado', 'environment_slug' => 'mercado', 'category_slug' => 'alimentacao', 'content' => 'Planejar as compras antes de sair evita gastos por impulso e desperdício.', 'display_order' => 2],
            ['title' => 'Crie metas com prazo realista', 'environment_slug' => 'escola', 'category_slug' => null, 'content' => 'Metas alcançáveis mantêm a motivação e ajudam a construir constância.', 'display_order' => 3],
            ['title' => 'Reserve um valor para emergências', 'environment_slug' => 'farmacia', 'category_slug' => 'saude', 'content' => 'Ter uma reserva reduz o impacto de gastos inesperados com saúde.', 'display_order' => 4],
            ['title' => 'Recompense seu progresso', 'environment_slug' => 'parque-de-diversoes', 'category_slug' => 'lazer', 'content' => 'Celebrar pequenas vitórias ajuda a manter o hábito do controle financeiro.', 'display_order' => 5],
        ];

        foreach ($tips as $tip) {
            Tip::updateOrCreate(
                ['title' => $tip['title']],
                [
                    'environment_id' => $environments[$tip['environment_slug']] ?? null,
                    'category_id' => $tip['category_slug'] ? ($categories[$tip['category_slug']] ?? null) : null,
                    'content' => $tip['content'],
                    'display_order' => $tip['display_order'],
                    'is_active' => true,
                ]
            );
        }
    }
}
