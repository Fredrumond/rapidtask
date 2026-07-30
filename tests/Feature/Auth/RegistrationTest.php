<?php

use App\Models\User;
use Livewire\Volt\Volt;

test('registration screen can be rendered for guests', function () {
    $response = $this->get('/register');

    $response->assertOk();
});

test('guests can register new users', function () {
    $component = Volt::test('pages.auth.register')
        ->set('name', 'Test User')
        ->set('email', 'test@example.com')
        ->set('password', 'password')
        ->set('password_confirmation', 'password');

    $component->call('register');

    $component->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
    expect(User::where('email', 'test@example.com')->exists())->toBeTrue();
});
