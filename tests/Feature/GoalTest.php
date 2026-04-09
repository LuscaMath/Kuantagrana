<?php

use App\Models\Environment;
use App\Models\Goal;
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

it('displays the goals page for authenticated users', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get(route('goals.index'));

    $response->assertOk();
    $response->assertSee('Metas financeiras');
});

it('creates a goal and awards points to the user', function () {
    $user = User::factory()->create(['points' => 0]);
    $environment = Environment::query()->firstOrFail();

    $response = $this
        ->actingAs($user)
        ->post(route('goals.store'), [
            'environment_id' => $environment->id,
            'title' => 'Notebook novo',
            'description' => 'Guardar dinheiro para comprar um notebook.',
            'target_amount' => 3500,
            'start_date' => now()->toDateString(),
            'target_date' => now()->addMonths(6)->toDateString(),
            'status' => 'active',
        ]);

    $response->assertRedirect(route('goals.index'));

    $this->assertDatabaseHas('goals', [
        'user_id' => $user->id,
        'title' => 'Notebook novo',
        'status' => 'active',
    ]);

    expect($user->fresh()->points)->toBe(75);
});

it('adds a contribution and completes the goal when target is reached', function () {
    $user = User::factory()->create(['points' => 0]);
    $goal = Goal::query()->create([
        'user_id' => $user->id,
        'environment_id' => Environment::query()->value('id'),
        'title' => 'Reserva de emergência',
        'target_amount' => 500,
        'current_amount' => 450,
        'status' => 'active',
    ]);

    $response = $this
        ->actingAs($user)
        ->post(route('goals.contributions.store', $goal), [
            'amount' => 50,
            'contribution_date' => now()->toDateString(),
            'notes' => 'Aporte final',
        ]);

    $response->assertRedirect(route('goals.edit', $goal));

    $goal->refresh();

    $this->assertDatabaseHas('goal_contributions', [
        'goal_id' => $goal->id,
        'amount' => 50,
    ]);

    expect((float) $goal->current_amount)->toBe(500.0);
    expect($goal->status)->toBe('completed');
    expect($goal->completed_at)->not->toBeNull();
    expect($user->fresh()->points)->toBe(230);
});
