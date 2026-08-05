<?php

use App\Models\Time;
use App\Models\User;
use App\Support\CurrentTeam;
use Database\Factories\TimeMembroFactory;
use Livewire\Volt\Volt;

beforeEach(function () {
    seedLookups();
});

test('admin pode excluir o proprio time', function () {
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

test('membro nao admin nao pode excluir time', function () {
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

test('admin nao exclui time de outra conta mesmo com membership indevido', function () {
    extract(criarCenarioDoisTimes());

    TimeMembroFactory::new()->admin()->create([
        'time_id' => $timeB->id,
        'usuario_id' => $userA->id,
    ]);

    $this->actingAs($userA)
        ->withSession([
            CurrentTeam::SESSION_KEY => $timeA->id,
            CurrentTeam::CONTA_SESSION_KEY => $contaA->id,
        ]);

    expect(auth()->user()->can('delete', $timeB))->toBeFalse();
});

test('excluir time ativo troca para outro time do usuario', function () {
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
