<?php

use App\Models\Category;
use App\Models\FinancialTransaction;
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

it('displays the financial transaction environment chooser for authenticated users', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get(route('financial-transactions.index'));

    $response->assertOk();
    $response->assertSee('Escolha um ambiente para ver as transacoes');
    $response->assertSee('Ver transacoes de Casa');
});

it('creates a financial transaction inside the selected environment and awards points to the user', function () {
    $user = User::factory()->create(['points' => 0]);
    $category = Category::query()->where('type', 'expense')->firstOrFail();

    $response = $this
        ->actingAs($user)
        ->post(route('financial-transactions.store'), [
            'environment_id' => $category->environment_id,
            'category_id' => $category->id,
            'type' => 'expense',
            'title' => 'Compra no mercado',
            'description' => 'Itens da semana',
            'amount' => 85.50,
            'transaction_date' => now()->toDateString(),
            'is_completed' => true,
            'is_recurring' => false,
        ]);

    $response->assertRedirect(route('financial-transactions.index', ['environment_id' => $category->environment_id]));

    $this->assertDatabaseHas('financial_transactions', [
        'user_id' => $user->id,
        'environment_id' => $category->environment_id,
        'category_id' => $category->id,
        'title' => 'Compra no mercado',
        'type' => 'expense',
    ]);

    expect(FinancialTransaction::query()->count())->toBe(1);
    expect($user->fresh()->points)->toBe(30);
    expect($user->fresh()->level)->not->toBeNull();
});
