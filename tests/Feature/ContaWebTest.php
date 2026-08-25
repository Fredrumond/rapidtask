<?php

use App\Models\Conta;
use App\Models\User;
use Livewire\Volt\Volt;

test('owner renomeia a conta via service', function (): void {
    $owner = User::factory()->create();
    $conta = Conta::factory()->create([
        'usuario_id' => $owner->id,
        'nome' => 'Conta Antiga',
    ]);

    $this->actingAs($owner);

    Volt::test('pages.contas.edit', ['conta' => $conta])
        ->set('nome', 'Conta Nova')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('contas.edit', $conta, absolute: false));

    expect($conta->fresh()->nome)->toBe('Conta Nova');
});

test('nao owner recebe 403 ao abrir edicao da conta', function (): void {
    $owner = User::factory()->create();
    $conta = Conta::factory()->create(['usuario_id' => $owner->id]);
    $outro = User::factory()->create();

    $this->actingAs($outro);

    Volt::test('pages.contas.edit', ['conta' => $conta])
        ->assertForbidden();
});

test('renomear conta com nome em branco dispara erro de dominio', function (): void {
    $owner = User::factory()->create();
    $conta = Conta::factory()->create([
        'usuario_id' => $owner->id,
        'nome' => 'Conta Antiga',
    ]);

    $this->actingAs($owner);

    Volt::test('pages.contas.edit', ['conta' => $conta])
        ->set('nome', '   ')
        ->call('save')
        ->assertHasErrors(['nome']);

    expect($conta->fresh()->nome)->toBe('Conta Antiga');
});
