<?php

use App\Models\Category;
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

it('shows only context-appropriate summaries on the map', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get(route('environments.index'));

    $response->assertOk();
    $response->assertSee('Receitas');
    $response->assertSee('Despesas');
    $response->assertSee('Dicas');
    $response->assertSee('Metas');
    $response->assertDontSee('Itens');
});

it('shows receitas and despesas as the main summary for casa on the map', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get(route('environments.index'));

    $response->assertOk();
    $response->assertSee('Centro financeiro da rotina');
    $response->assertSee('Receitas');
    $response->assertSee('Despesas');
});

it('does not show income stats on the market environment page', function () {
    $user = User::factory()->create();
    $environment = Environment::query()->where('slug', 'mercado')->firstOrFail();

    $response = $this
        ->actingAs($user)
        ->get(route('environments.show', $environment->slug));

    $response->assertOk();
    $response->assertSee('Despesas');
    $response->assertSee('Transacoes');
    $response->assertDontSee('Receitas');
});

it('groups escola and parque in the lower map section', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get(route('environments.index'));

    $response->assertOk();
    $response->assertSee('Aprendizado e Progresso');
    $response->assertSee('Escola');
    $response->assertSee('Parque de Divers');
});

it('consolidates market and pharmacy expenses into casa financial summary', function () {
    $user = User::factory()->create();
    $casaIncomeCategory = Category::query()->where('slug', 'salario')->firstOrFail();
    $marketExpenseCategory = Category::query()->where('slug', 'alimentacao')->firstOrFail();
    $pharmacyExpenseCategory = Category::query()->where('slug', 'saude')->firstOrFail();
    $casa = Environment::query()->where('slug', 'casa')->firstOrFail();

    $user->financialTransactions()->create([
        'environment_id' => $casaIncomeCategory->environment_id,
        'category_id' => $casaIncomeCategory->id,
        'type' => 'income',
        'title' => 'Salario',
        'amount' => 1000,
        'transaction_date' => now()->toDateString(),
        'is_completed' => true,
        'is_recurring' => false,
    ]);

    $user->financialTransactions()->create([
        'environment_id' => $marketExpenseCategory->environment_id,
        'category_id' => $marketExpenseCategory->id,
        'type' => 'expense',
        'title' => 'Mercado',
        'amount' => 150,
        'transaction_date' => now()->toDateString(),
        'is_completed' => true,
        'is_recurring' => false,
    ]);

    $user->financialTransactions()->create([
        'environment_id' => $pharmacyExpenseCategory->environment_id,
        'category_id' => $pharmacyExpenseCategory->id,
        'type' => 'expense',
        'title' => 'Farmacia',
        'amount' => 50,
        'transaction_date' => now()->toDateString(),
        'is_completed' => true,
        'is_recurring' => false,
    ]);

    $response = $this
        ->actingAs($user)
        ->get(route('environments.show', $casa->slug));

    $response->assertOk();
    $response->assertSee('R$ 1.000,00', false);
    $response->assertSee('R$ 200,00', false);
    $response->assertSee('Salario');
    $response->assertSee('Mercado');
    $response->assertSee('Farmacia');
});
