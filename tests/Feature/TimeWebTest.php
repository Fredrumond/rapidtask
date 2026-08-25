<?php

use App\Models\Conta;
use App\Models\Time;
use App\Models\User;
use App\Support\CurrentTeam;
use Database\Factories\TimeMembroFactory;
use Livewire\Volt\Volt;

beforeEach(function () {
    seedLookups();
});

test('criar time associa conta, adiciona admin e define contexto atual', function (): void {
    $user = User::factory()->create();
    $conta = Conta::factory()->create(['usuario_id' => $user->id]);

    $this->actingAs($user);

    Volt::test('pages.times.index')
        ->set('nome', 'Time Alpha')
        ->call('create')
        ->assertHasNoErrors()
        ->assertRedirect();

    $time = Time::query()->where('nome', 'Time Alpha')->first();

    expect($time)->not->toBeNull()
        ->and($time->conta_id)->toBe($conta->id)
        ->and($time->usuario_id)->toBe($user->id)
        ->and($user->isAdminOf($time->id))->toBeTrue()
        ->and(session(CurrentTeam::SESSION_KEY))->toBe($time->id)
        ->and(session(CurrentTeam::CONTA_SESSION_KEY))->toBe($conta->id)
        ->and(current_conta_id())->toBe($conta->id);
});

test('criar time sem conta existente cria a conta do usuario', function (): void {
    $user = User::factory()->create(['name' => 'Maria Silva']);

    $this->actingAs($user);

    expect(Conta::query()->where('usuario_id', $user->id)->exists())->toBeFalse();

    Volt::test('pages.times.index')
        ->set('nome', 'Primeiro Time')
        ->call('create')
        ->assertHasNoErrors()
        ->assertRedirect();

    $conta = Conta::query()->where('usuario_id', $user->id)->first();
    $time = Time::query()->where('nome', 'Primeiro Time')->first();

    expect($conta)->not->toBeNull()
        ->and($conta->nome)->toBe('Conta de Maria Silva')
        ->and($time)->not->toBeNull()
        ->and($time->conta_id)->toBe($conta->id)
        ->and(session(CurrentTeam::SESSION_KEY))->toBe($time->id)
        ->and(session(CurrentTeam::CONTA_SESSION_KEY))->toBe($conta->id);
});

test('criar time sem nome mostra erro de validacao', function (): void {
    $user = User::factory()->create();
    Conta::factory()->create(['usuario_id' => $user->id]);

    $this->actingAs($user);

    Volt::test('pages.times.index')
        ->set('nome', '')
        ->call('create')
        ->assertHasErrors(['nome']);

    expect(Time::query()->count())->toBe(0);
});

test('criar time com nome em branco dispara erro de dominio', function (): void {
    $user = User::factory()->create();
    Conta::factory()->create(['usuario_id' => $user->id]);

    $this->actingAs($user);

    Volt::test('pages.times.index')
        ->set('nome', '   ')
        ->call('create')
        ->assertHasErrors(['nome']);

    expect(Time::query()->where('nome', '   ')->exists())->toBeFalse();
});

test('admin exclui o proprio time', function (): void {
    $admin = User::factory()->create();
    $time = Time::factory()->create(['usuario_id' => $admin->id]);
    TimeMembroFactory::new()->admin()->create([
        'time_id' => $time->id,
        'usuario_id' => $admin->id,
    ]);

    $this->actingAs($admin)
        ->withSession([
            CurrentTeam::SESSION_KEY => $time->id,
            CurrentTeam::CONTA_SESSION_KEY => $time->conta_id,
        ]);

    Volt::test('pages.times.show', ['time' => $time])
        ->call('delete')
        ->assertHasNoErrors()
        ->assertRedirect(route('times.index', absolute: false));

    expect(Time::withTrashed()->find($time->id)?->trashed())->toBeTrue()
        ->and(Time::find($time->id))->toBeNull();
});

test('membro nao admin nao pode excluir time', function (): void {
    $admin = User::factory()->create();
    $membro = User::factory()->create();
    $time = Time::factory()->create(['usuario_id' => $admin->id]);
    TimeMembroFactory::new()->admin()->create([
        'time_id' => $time->id,
        'usuario_id' => $admin->id,
    ]);
    TimeMembroFactory::new()->membro()->create([
        'time_id' => $time->id,
        'usuario_id' => $membro->id,
    ]);

    $this->actingAs($membro)
        ->withSession([
            CurrentTeam::SESSION_KEY => $time->id,
            CurrentTeam::CONTA_SESSION_KEY => $time->conta_id,
        ]);

    Volt::test('pages.times.show', ['time' => $time])
        ->call('delete')
        ->assertForbidden();

    expect(Time::find($time->id))->not->toBeNull();
});

test('excluir time ativo troca para outro time do usuario', function (): void {
    $admin = User::factory()->create();
    $timeA = Time::factory()->create(['usuario_id' => $admin->id, 'nome' => 'Alpha']);
    $timeB = Time::factory()->create([
        'usuario_id' => $admin->id,
        'conta_id' => $timeA->conta_id,
        'nome' => 'Beta',
    ]);

    foreach ([$timeA, $timeB] as $time) {
        TimeMembroFactory::new()->admin()->create([
            'time_id' => $time->id,
            'usuario_id' => $admin->id,
        ]);
    }

    $this->actingAs($admin)
        ->withSession([
            CurrentTeam::SESSION_KEY => $timeA->id,
            CurrentTeam::CONTA_SESSION_KEY => $timeA->conta_id,
        ]);

    Volt::test('pages.times.show', ['time' => $timeA])
        ->call('delete')
        ->assertRedirect(route('times.index', absolute: false));

    expect(CurrentTeam::id())->toBe($timeB->id);
});
