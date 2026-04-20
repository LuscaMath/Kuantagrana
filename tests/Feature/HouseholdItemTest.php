<?php

use App\Models\Environment;
use App\Models\HouseholdItem;
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

it('displays the household items environment chooser for authenticated users', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get(route('household-items.index'));

    $response->assertOk();
    $response->assertSee('Escolha um ambiente para gerenciar itens');
    $response->assertSee('Ver itens de Casa');
});

it('creates a household item inside the selected environment and awards points to the user', function () {
    $user = User::factory()->create(['points' => 0]);
    $environment = Environment::query()->active()->supporting('items')->firstOrFail();

    $response = $this
        ->actingAs($user)
        ->post(route('household-items.store'), [
            'environment_id' => $environment->id,
            'name' => 'Arroz',
            'unit' => 'kg',
            'quantity' => 2,
            'minimum_quantity' => 1,
            'expires_at' => now()->addMonths(3)->toDateString(),
            'notes' => 'Pacote aberto',
            'is_active' => true,
        ]);

    $response->assertRedirect(route('household-items.index', ['environment_id' => $environment->id]));

    $this->assertDatabaseHas('household_items', [
        'user_id' => $user->id,
        'environment_id' => $environment->id,
        'name' => 'Arroz',
        'unit' => 'kg',
    ]);

    expect(HouseholdItem::query()->count())->toBe(1);
    expect($user->fresh()->points)->toBe(30);
});
