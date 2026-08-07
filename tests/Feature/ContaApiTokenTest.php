<?php

use App\Models\Conta;
use App\Models\User;
use Livewire\Volt\Volt;

test('owner can generate and revoke api token from conta settings', function (): void {
    $user = User::factory()->create();
    $conta = Conta::factory()->create(['usuario_id' => $user->id]);

    $this->actingAs($user);

    $component = Volt::test('contas.manage-api-token-form', ['conta' => $conta])
        ->call('generateToken');

    $component
        ->assertHasNoErrors()
        ->assertSet('hasActiveToken', true)
        ->assertSet('plainTextToken', fn (?string $token): bool => is_string($token) && $token !== '');

    expect($conta->tokens()->count())->toBe(1);

    $component = Volt::test('contas.manage-api-token-form', ['conta' => $conta])
        ->call('revokeToken');

    $component
        ->assertHasNoErrors()
        ->assertSet('hasActiveToken', false)
        ->assertSet('plainTextToken', null);

    expect($conta->fresh()->tokens()->count())->toBe(0);
});

test('plain text token is not persisted after conta token component reload', function (): void {
    $user = User::factory()->create();
    $conta = Conta::factory()->create(['usuario_id' => $user->id]);

    $this->actingAs($user);

    Volt::test('contas.manage-api-token-form', ['conta' => $conta])
        ->call('generateToken');

    $component = Volt::test('contas.manage-api-token-form', ['conta' => $conta]);

    $component
        ->assertSet('hasActiveToken', true)
        ->assertSet('plainTextToken', null);
});

test('non owner cannot manage conta api token', function (): void {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $conta = Conta::factory()->create(['usuario_id' => $owner->id]);

    $this->actingAs($other);

    Volt::test('contas.manage-api-token-form', ['conta' => $conta])
        ->assertForbidden();
});

test('profile page does not include api token form', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/profile')
        ->assertOk()
        ->assertDontSeeVolt('profile.manage-api-token-form')
        ->assertDontSeeVolt('contas.manage-api-token-form');
});
