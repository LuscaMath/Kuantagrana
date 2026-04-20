<?php

use App\Models\Environment;
use App\Models\User;
use Database\Seeders\AchievementSeeder;
use Database\Seeders\CategorySeeder;
use Database\Seeders\ChallengeSeeder;
use Database\Seeders\EnvironmentSeeder;
use Database\Seeders\LevelSeeder;
use Database\Seeders\TipSeeder;

beforeEach(function () {
    $this->seed([
        LevelSeeder::class,
        EnvironmentSeeder::class,
        CategorySeeder::class,
        AchievementSeeder::class,
        ChallengeSeeder::class,
        TipSeeder::class,
    ]);
});

it('displays the environment map for authenticated users', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get(route('environments.index'));

    $response->assertOk();
    $response->assertSee('Escolha por contexto');
    $response->assertSee('Cada ambiente organiza uma parte da rotina');
    $response->assertSee('Casa');
    $response->assertSee('Parque de Divers');
});

it('displays a specific environment page with contextual content', function () {
    $user = User::factory()->create();
    $environment = Environment::query()->where('slug', 'casa')->firstOrFail();

    $response = $this
        ->actingAs($user)
        ->get(route('environments.show', $environment->slug));

    $response->assertOk();
    $response->assertSee($environment->name);
    $response->assertSee('Centro financeiro da rotina');
    $response->assertSee('Movimentacoes recentes');
    $response->assertSee('Dicas do ambiente');
});
