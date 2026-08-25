<?php

use App\Models\Conta;
use App\Models\Time;
use App\Models\User;
use App\Support\CurrentTeam;
use Livewire\Volt\Volt;

test('guests can register new users', function () {
    $component = Volt::test('pages.auth.register')
        ->set('name', 'Test User')
        ->set('email', 'test@example.com')
        ->set('password', 'password')
        ->set('password_confirmation', 'password');

    $component->call('register');

    $component->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();

    $user = User::where('email', 'test@example.com')->first();

    expect($user)->not->toBeNull()
        ->and(Conta::query()->where('usuario_id', $user->id)->exists())->toBeTrue()
        ->and(Conta::query()->where('usuario_id', $user->id)->value('nome'))->toBe('Conta de Test User');
});

test('apos o registro o usuario cria o time inicial e o contexto fica definido', function () {
    seedLookups();

    Volt::test('pages.auth.register')
        ->set('name', 'Test User')
        ->set('email', 'test@example.com')
        ->set('password', 'password')
        ->set('password_confirmation', 'password')
        ->call('register');

    $user = User::where('email', 'test@example.com')->first();
    $conta = Conta::query()->where('usuario_id', $user->id)->first();

    expect($conta)->not->toBeNull();

    Volt::test('pages.times.index')
        ->set('nome', 'Time Inicial')
        ->call('create')
        ->assertHasNoErrors()
        ->assertRedirect();

    $time = Time::query()->where('nome', 'Time Inicial')->first();

    expect($time)->not->toBeNull()
        ->and($time->conta_id)->toBe($conta->id)
        ->and($user->isAdminOf($time->id))->toBeTrue()
        ->and(session(CurrentTeam::SESSION_KEY))->toBe($time->id)
        ->and(session(CurrentTeam::CONTA_SESSION_KEY))->toBe($conta->id);
});

test('registration screen can be rendered for guests', function () {
    $response = $this->get('/register');

    $response->assertOk();
});
