<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('post tokens returns unauthorized without bearer token', function (): void {
    $this->postJson('/api/tokens')
        ->assertUnauthorized();
});

test('delete tokens returns unauthorized without bearer token', function (): void {
    $this->deleteJson('/api/tokens')
        ->assertUnauthorized();
});

test('post tokens creates token and returns plain text', function (): void {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->postJson('/api/tokens');

    $response
        ->assertCreated()
        ->assertJsonStructure([
            'message',
            'data' => [
                'active',
                'name',
                'plain_text_token',
                'created_at',
            ],
        ])
        ->assertJsonPath('data.active', true)
        ->assertJsonPath('data.name', 'api');

    expect($user->tokens()->count())->toBe(1);
});

test('posting a second token revokes the previous one', function (): void {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $firstToken = $this->postJson('/api/tokens')->json('data.plain_text_token');

    auth()->forgetGuards();

    $secondToken = $this->withToken($firstToken)
        ->postJson('/api/tokens')
        ->assertCreated()
        ->json('data.plain_text_token');

    expect($firstToken)->not->toBe($secondToken);
    expect($user->tokens()->count())->toBe(1);

    auth()->forgetGuards();

    $this->withToken($firstToken)
        ->postJson('/api/tokens')
        ->assertUnauthorized();

    auth()->forgetGuards();

    $this->withToken($secondToken)
        ->deleteJson('/api/tokens')
        ->assertOk();
});

test('delete tokens revokes current token', function (): void {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $plainTextToken = $this->postJson('/api/tokens')->json('data.plain_text_token');

    auth()->forgetGuards();

    $this->withToken($plainTextToken)
        ->deleteJson('/api/tokens')
        ->assertOk()
        ->assertJsonPath('message', 'Token revogado com sucesso.');

    expect($user->tokens()->count())->toBe(0);

    auth()->forgetGuards();

    $this->withToken($plainTextToken)
        ->deleteJson('/api/tokens')
        ->assertUnauthorized();
});

test('user can have at most one active token after issue', function (): void {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $this->postJson('/api/tokens')->assertCreated();
    $this->postJson('/api/tokens')->assertCreated();
    $this->postJson('/api/tokens')->assertCreated();

    expect($user->tokens()->count())->toBe(1);
});
