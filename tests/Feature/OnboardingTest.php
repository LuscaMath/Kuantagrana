<?php

use App\Models\User;

test('onboarding tutorial is shown for users who have not dismissed it', function () {
    $user = User::factory()->create([
        'onboarding_dismissed_at' => null,
    ]);

    $response = $this
        ->actingAs($user)
        ->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee('Tutorial interativo');
    $response->assertSee('Como navegar pelo sistema');
});

test('users can dismiss onboarding and stop seeing it automatically', function () {
    $user = User::factory()->create([
        'onboarding_dismissed_at' => null,
    ]);

    $this->actingAs($user)
        ->post(route('onboarding.dismiss'))
        ->assertRedirect();

    expect($user->fresh()->onboarding_dismissed_at)->not->toBeNull();

    $response = $this
        ->actingAs($user->fresh())
        ->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee('open: false', false);
    $response->assertDontSee('open: true', false);
});

test('users can reopen the tutorial manually', function () {
    $user = User::factory()->create([
        'onboarding_dismissed_at' => now(),
    ]);

    $response = $this
        ->actingAs($user)
        ->get(route('onboarding.show'));

    $response->assertRedirect(route('dashboard', ['tutorial' => 1], false));
});
