<?php

use App\Models\Category;
use App\Models\Environment;
use App\Models\FinancialTransaction;
use App\Models\User;
use App\Services\FinancialTransactionService;
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

it('uses casa income as monthly balance reference for market and pharmacy summaries', function () {
    $user = User::factory()->create();
    $service = app(FinancialTransactionService::class);
    $casaIncomeCategory = Category::query()->where('slug', 'salario')->firstOrFail();
    $marketExpenseCategory = Category::query()->where('slug', 'alimentacao')->firstOrFail();
    $market = Environment::query()->where('slug', 'mercado')->firstOrFail();

    $user->financialTransactions()->create([
        'environment_id' => $casaIncomeCategory->environment_id,
        'category_id' => $casaIncomeCategory->id,
        'type' => 'income',
        'title' => 'Salario do mes',
        'amount' => 1200,
        'transaction_date' => now()->toDateString(),
        'is_completed' => true,
        'is_recurring' => false,
    ]);

    $user->financialTransactions()->create([
        'environment_id' => $marketExpenseCategory->environment_id,
        'category_id' => $marketExpenseCategory->id,
        'type' => 'expense',
        'title' => 'Compras do mercado',
        'amount' => 250,
        'transaction_date' => now()->toDateString(),
        'is_completed' => true,
        'is_recurring' => false,
    ]);

    $summary = $service->getSummary($user, now()->format('Y-m'), (string) $market->id);

    expect((float) $summary['income'])->toBe(1200.0);
    expect((float) $summary['expense'])->toBe(250.0);
    expect((float) $summary['balance'])->toBe(950.0);
});

it('uses all operational expenses in market monthly balance summary', function () {
    $user = User::factory()->create();
    $service = app(FinancialTransactionService::class);
    $casaIncomeCategory = Category::query()->where('slug', 'salario')->firstOrFail();
    $casaExpenseCategory = Category::query()->where('slug', 'moradia')->firstOrFail();
    $marketExpenseCategory = Category::query()->where('slug', 'alimentacao')->firstOrFail();
    $pharmacyExpenseCategory = Category::query()->where('slug', 'saude')->firstOrFail();
    $market = Environment::query()->where('slug', 'mercado')->firstOrFail();

    $user->financialTransactions()->create([
        'environment_id' => $casaIncomeCategory->environment_id,
        'category_id' => $casaIncomeCategory->id,
        'type' => 'income',
        'title' => 'Salario do mes',
        'amount' => 2000,
        'transaction_date' => now()->toDateString(),
        'is_completed' => true,
        'is_recurring' => false,
    ]);

    $user->financialTransactions()->create([
        'environment_id' => $casaExpenseCategory->environment_id,
        'category_id' => $casaExpenseCategory->id,
        'type' => 'expense',
        'title' => 'Conta da casa',
        'amount' => 500,
        'transaction_date' => now()->toDateString(),
        'is_completed' => true,
        'is_recurring' => false,
    ]);

    $user->financialTransactions()->create([
        'environment_id' => $marketExpenseCategory->environment_id,
        'category_id' => $marketExpenseCategory->id,
        'type' => 'expense',
        'title' => 'Compras do mercado',
        'amount' => 300,
        'transaction_date' => now()->toDateString(),
        'is_completed' => true,
        'is_recurring' => false,
    ]);

    $user->financialTransactions()->create([
        'environment_id' => $pharmacyExpenseCategory->environment_id,
        'category_id' => $pharmacyExpenseCategory->id,
        'type' => 'expense',
        'title' => 'Farmacia',
        'amount' => 100,
        'transaction_date' => now()->toDateString(),
        'is_completed' => true,
        'is_recurring' => false,
    ]);

    $summary = $service->getSummary($user, now()->format('Y-m'), (string) $market->id);

    expect((float) $summary['income'])->toBe(2000.0);
    expect((float) $summary['expense'])->toBe(300.0);
    expect((float) $summary['balance'])->toBe(1100.0);
});

it('explains that monthly balance is consolidated on the market transactions page', function () {
    $user = User::factory()->create();
    $market = Environment::query()->where('slug', 'mercado')->firstOrFail();

    $response = $this
        ->actingAs($user)
        ->get(route('financial-transactions.index', ['environment_id' => $market->id]));

    $response->assertOk();
    $response->assertSee('Saldo geral do mes');
    $response->assertSee('Despesas registradas em Mercado');
    $response->assertSee('Considera receitas da base financeira e despesas de Casa, Mercado e Farmacia.');
});
