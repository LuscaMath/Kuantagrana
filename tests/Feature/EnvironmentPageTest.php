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
    $response->assertSee('Ambientes do Vale das Moedas');
    $response->assertSee('Casa');
    $response->assertSee('Parque de Diversões');
});

it('displays a specific environment page with contextual content', function () {
    $user = User::factory()->create();
    $environment = Environment::query()->where('slug', 'casa')->firstOrFail();

    $response = $this
        ->actingAs($user)
        ->get(route('environments.show', $environment->slug));

    $response->assertOk();
    $response->assertSee($environment->name);
    $response->assertSee('Contas fixas e rotina da casa');
    $response->assertSee('Dicas do ambiente');
});
