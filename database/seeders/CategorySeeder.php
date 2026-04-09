<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Environment;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $environments = Environment::query()->pluck('id', 'slug');

        $categories = [
            ['name' => 'Salário', 'slug' => 'salario', 'type' => 'income', 'environment_slug' => 'casa', 'icon' => 'wallet', 'color' => '#7ccf7a', 'display_order' => 1],
            ['name' => 'Freelance', 'slug' => 'freelance', 'type' => 'income', 'environment_slug' => 'escola', 'icon' => 'briefcase', 'color' => '#8ed0ff', 'display_order' => 2],
            ['name' => 'Presente', 'slug' => 'presente', 'type' => 'income', 'environment_slug' => 'parque-de-diversoes', 'icon' => 'gift', 'color' => '#f0b429', 'display_order' => 3],
            ['name' => 'Alimentação', 'slug' => 'alimentacao', 'type' => 'expense', 'environment_slug' => 'mercado', 'icon' => 'utensils', 'color' => '#7ccf7a', 'display_order' => 4],
            ['name' => 'Transporte', 'slug' => 'transporte', 'type' => 'expense', 'environment_slug' => 'escola', 'icon' => 'bus', 'color' => '#8ed0ff', 'display_order' => 5],
            ['name' => 'Moradia', 'slug' => 'moradia', 'type' => 'expense', 'environment_slug' => 'casa', 'icon' => 'house', 'color' => '#8f5f33', 'display_order' => 6],
            ['name' => 'Saúde', 'slug' => 'saude', 'type' => 'expense', 'environment_slug' => 'farmacia', 'icon' => 'cross', 'color' => '#df6b57', 'display_order' => 7],
            ['name' => 'Lazer', 'slug' => 'lazer', 'type' => 'expense', 'environment_slug' => 'parque-de-diversoes', 'icon' => 'sparkles', 'color' => '#f0b429', 'display_order' => 8],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                [
                    'environment_id' => $environments[$category['environment_slug']] ?? null,
                    'name' => $category['name'],
                    'type' => $category['type'],
                    'icon' => $category['icon'],
                    'color' => $category['color'],
                    'description' => null,
                    'display_order' => $category['display_order'],
                    'is_active' => true,
                ]
            );
        }
    }
}
