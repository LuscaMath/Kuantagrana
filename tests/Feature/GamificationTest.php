<?php

use App\Models\Category;
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

it('unlocks achievements and completes challenges based on user actions', function () {
    $user = User::factory()->create(['points' => 0]);
    $category = Category::query()->where('type', 'expense')->firstOrFail();

    foreach (range(1, 3) as $index) {
        $this->actingAs($user)->post(route('financial-transactions.store'), [
            'environment_id' => $category->environment_id,
            'category_id' => $category->id,
            'type' => 'expense',
            'title' => "Despesa {$index}",
            'amount' => 10,
            'transaction_date' => now()->toDateString(),
            'is_completed' => true,
            'is_recurring' => false,
        ]);
    }

    $user->refresh();

    $this->assertDatabaseHas('user_achievements', [
        'user_id' => $user->id,
    ]);

    $this->assertDatabaseHas('user_challenges', [
        'user_id' => $user->id,
        'status' => 'completed',
        'progress' => 3,
    ]);

    expect($user->achievements()->pluck('slug'))->toContain('primeiro-registro');
    expect($user->points)->toBe(90);
});

it('shows dynamic gamification data on the dashboard', function () {
    $user = User::factory()->create(['points' => 0]);
    $category = Category::query()->where('type', 'expense')->firstOrFail();

    $this->actingAs($user)->post(route('financial-transactions.store'), [
        'environment_id' => $category->environment_id,
        'category_id' => $category->id,
        'type' => 'expense',
        'title' => 'Primeira compra',
        'amount' => 25,
        'transaction_date' => now()->toDateString(),
        'is_completed' => true,
        'is_recurring' => false,
    ]);

    $response = $this
        ->actingAs($user->fresh())
        ->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee('voce tem', false);
    $response->assertSee('Primeiro Registro');
});
