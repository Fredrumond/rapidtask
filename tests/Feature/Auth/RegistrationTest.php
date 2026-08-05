<?php

use App\Models\Conta;
use App\Models\Time;
use App\Models\TimeMembroConvite;
use App\Models\User;
use App\Support\CurrentTeam;
use Database\Factories\TimeMembroFactory;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
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

test('registration screen can be rendered for guests', function () {
    $response = $this->get('/register');

    $response->assertOk();
});
