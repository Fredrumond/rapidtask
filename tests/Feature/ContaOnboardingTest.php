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

beforeEach(function () {
    seedLookups();
    Mail::fake();
});

test('criar time associa conta_id do owner e grava current_conta_id na sessao', function () {
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
        ->and(session(CurrentTeam::SESSION_KEY))->toBe($time->id)
        ->and(session(CurrentTeam::CONTA_SESSION_KEY))->toBe($conta->id)
        ->and(current_conta_id())->toBe($conta->id);
});

test('CurrentTeam::set atualiza current_conta_id ao trocar de time', function () {
    $user = User::factory()->create();
    $conta = Conta::factory()->create(['usuario_id' => $user->id]);

    $timeA = Time::factory()->create([
        'usuario_id' => $user->id,
        'conta_id' => $conta->id,
    ]);
    $timeB = Time::factory()->create([
        'usuario_id' => $user->id,
        'conta_id' => $conta->id,
    ]);

    TimeMembroFactory::new()->admin()->create([
        'time_id' => $timeA->id,
        'usuario_id' => $user->id,
    ]);
    TimeMembroFactory::new()->admin()->create([
        'time_id' => $timeB->id,
        'usuario_id' => $user->id,
    ]);

    CurrentTeam::set($timeA->id);
    expect(current_conta_id())->toBe($conta->id)
        ->and(current_time_id())->toBe($timeA->id);

    CurrentTeam::set($timeB->id);
    expect(current_conta_id())->toBe($conta->id)
        ->and(current_time_id())->toBe($timeB->id);

    CurrentTeam::clear();
    expect(current_conta_id())->toBeNull()
        ->and(current_time_id())->toBeNull();
});

test('convidar bloqueia email vinculado a outra conta', function () {
    $ownerA = User::factory()->create();
    $contaA = Conta::factory()->create(['usuario_id' => $ownerA->id]);
    $timeA = Time::factory()->create([
        'usuario_id' => $ownerA->id,
        'conta_id' => $contaA->id,
    ]);
    TimeMembroFactory::new()->admin()->create([
        'time_id' => $timeA->id,
        'usuario_id' => $ownerA->id,
    ]);

    $ownerB = User::factory()->create(['email' => 'outro@example.com']);
    Conta::factory()->create(['usuario_id' => $ownerB->id]);

    $this->actingAs($ownerA);
    CurrentTeam::set($timeA->id);

    Volt::test('pages.times.show', ['time' => $timeA])
        ->set('nome', 'Convidado')
        ->set('email', 'outro@example.com')
        ->call('convidar')
        ->assertHasErrors(['email']);

    expect(TimeMembroConvite::query()->count())->toBe(0);
});

test('convidar permite email sem usuario na plataforma', function () {
    $owner = User::factory()->create();
    $conta = Conta::factory()->create(['usuario_id' => $owner->id]);
    $time = Time::factory()->create([
        'usuario_id' => $owner->id,
        'conta_id' => $conta->id,
    ]);
    TimeMembroFactory::new()->admin()->create([
        'time_id' => $time->id,
        'usuario_id' => $owner->id,
    ]);

    $this->actingAs($owner);
    CurrentTeam::set($time->id);

    Volt::test('pages.times.show', ['time' => $time])
        ->set('nome', 'Novo')
        ->set('email', 'novo@example.com')
        ->call('convidar')
        ->assertHasNoErrors();

    expect(TimeMembroConvite::query()->where('email', 'novo@example.com')->exists())->toBeTrue();
});

test('convidar permite email da mesma conta', function () {
    $owner = User::factory()->create();
    $conta = Conta::factory()->create(['usuario_id' => $owner->id]);
    $timeA = Time::factory()->create([
        'usuario_id' => $owner->id,
        'conta_id' => $conta->id,
        'nome' => 'Time A',
    ]);
    $timeB = Time::factory()->create([
        'usuario_id' => $owner->id,
        'conta_id' => $conta->id,
        'nome' => 'Time B',
    ]);
    TimeMembroFactory::new()->admin()->create([
        'time_id' => $timeA->id,
        'usuario_id' => $owner->id,
    ]);
    TimeMembroFactory::new()->admin()->create([
        'time_id' => $timeB->id,
        'usuario_id' => $owner->id,
    ]);

    $membro = User::factory()->create(['email' => 'membro@example.com']);
    TimeMembroFactory::new()->membro()->create([
        'time_id' => $timeA->id,
        'usuario_id' => $membro->id,
    ]);

    $this->actingAs($owner);
    CurrentTeam::set($timeB->id);

    Volt::test('pages.times.show', ['time' => $timeB])
        ->set('nome', 'Membro')
        ->set('email', 'membro@example.com')
        ->call('convidar')
        ->assertHasNoErrors();

    expect(TimeMembroConvite::query()->where('email', 'membro@example.com')->exists())->toBeTrue();
});

test('aceitar convite bloqueia usuario de outra conta', function () {
    $ownerA = User::factory()->create();
    $contaA = Conta::factory()->create(['usuario_id' => $ownerA->id]);
    $timeA = Time::factory()->create([
        'usuario_id' => $ownerA->id,
        'conta_id' => $contaA->id,
    ]);

    $ownerB = User::factory()->create(['email' => 'bloqueado@example.com']);
    Conta::factory()->create(['usuario_id' => $ownerB->id]);

    $convite = TimeMembroConvite::factory()->create([
        'email' => 'bloqueado@example.com',
        'time_id' => $timeA->id,
        'status' => 0,
    ]);

    $url = URL::temporarySignedRoute(
        'convites.aceitar',
        now()->addDay(),
        ['convite' => $convite->id]
    );

    $this->actingAs($ownerB)
        ->get($url)
        ->assertForbidden();
});

test('aceitar convite sem assinatura e rejeitado', function () {
    $user = User::factory()->create(['email' => 'convidado@example.com']);
    $conta = Conta::factory()->create(['usuario_id' => $user->id]);
    $time = Time::factory()->create([
        'usuario_id' => $user->id,
        'conta_id' => $conta->id,
    ]);

    $convite = TimeMembroConvite::factory()->create([
        'email' => 'convidado@example.com',
        'time_id' => $time->id,
        'status' => 0,
    ]);

    $this->actingAs($user)
        ->get(route('convites.aceitar', $convite))
        ->assertForbidden();
});

test('aceitar convite redireciona guest para login', function () {
    $owner = User::factory()->create();
    $conta = Conta::factory()->create(['usuario_id' => $owner->id]);
    $time = Time::factory()->create([
        'usuario_id' => $owner->id,
        'conta_id' => $conta->id,
    ]);

    $convite = TimeMembroConvite::factory()->create([
        'email' => 'novo@example.com',
        'time_id' => $time->id,
        'status' => 0,
    ]);

    $url = URL::temporarySignedRoute(
        'convites.aceitar',
        now()->addDay(),
        ['convite' => $convite->id]
    );

    $this->get($url)->assertRedirect(route('login'));
});

test('aceitar convite com email divergente retorna 403', function () {
    $owner = User::factory()->create();
    $conta = Conta::factory()->create(['usuario_id' => $owner->id]);
    $time = Time::factory()->create([
        'usuario_id' => $owner->id,
        'conta_id' => $conta->id,
    ]);

    $outro = User::factory()->create(['email' => 'outro@example.com']);

    $convite = TimeMembroConvite::factory()->create([
        'email' => 'destinatario@example.com',
        'time_id' => $time->id,
        'status' => 0,
    ]);

    $url = URL::temporarySignedRoute(
        'convites.aceitar',
        now()->addDay(),
        ['convite' => $convite->id]
    );

    $this->actingAs($outro)
        ->get($url)
        ->assertForbidden();
});

test('owner pode editar nome da conta', function () {
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

test('nao owner recebe 403 ao editar conta', function () {
    $owner = User::factory()->create();
    $conta = Conta::factory()->create(['usuario_id' => $owner->id]);
    $outro = User::factory()->create();

    $this->actingAs($outro);

    Volt::test('pages.contas.edit', ['conta' => $conta])
        ->assertForbidden();
});
