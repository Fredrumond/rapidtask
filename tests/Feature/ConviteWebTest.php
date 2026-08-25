<?php

use App\Mail\ConviteTimeMail;
use App\Models\Conta;
use App\Models\Time;
use App\Models\TimeMembro;
use App\Models\TimeMembroConvite;
use App\Models\User;
use App\Support\CurrentTeam;
use Database\Factories\TimeMembroFactory;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Livewire\Volt\Volt;

beforeEach(function (): void {
    seedLookups();
    Mail::fake();
});

/**
 * @return array{admin: User, conta: Conta, time: Time}
 */
function conviteCenarioAdmin(): array
{
    $admin = User::factory()->create();
    $conta = Conta::factory()->create(['usuario_id' => $admin->id]);
    $time = Time::factory()->create([
        'usuario_id' => $admin->id,
        'conta_id' => $conta->id,
    ]);
    TimeMembroFactory::new()->admin()->create([
        'time_id' => $time->id,
        'usuario_id' => $admin->id,
    ]);

    return compact('admin', 'conta', 'time');
}

function conviteUrlAssinada(TimeMembroConvite $convite, string $rota = 'convites.aceitar', int $dias = 1): string
{
    return URL::temporarySignedRoute(
        $rota,
        now()->addDays($dias),
        ['convite' => $convite->id]
    );
}

test('admin emite convite pendente e enfileira e-mail', function (): void {
    ['admin' => $admin, 'time' => $time] = conviteCenarioAdmin();

    $this->actingAs($admin);
    CurrentTeam::set($time->id);

    Volt::test('pages.times.show', ['time' => $time])
        ->set('nome', 'Novo Membro')
        ->set('email', 'novo@example.com')
        ->call('convidar')
        ->assertHasNoErrors();

    $convite = TimeMembroConvite::query()->where('email', 'novo@example.com')->first();

    expect($convite)->not->toBeNull()
        ->and($convite->nome)->toBe('Novo Membro')
        ->and($convite->time_id)->toBe($time->id)
        ->and($convite->status)->toBe(0)
        ->and($convite->token)->not->toBeEmpty();

    Mail::assertQueued(ConviteTimeMail::class, function (ConviteTimeMail $mail) use ($convite): bool {
        return $mail->convite->is($convite);
    });
});

test('membro nao admin nao pode emitir convite', function (): void {
    ['admin' => $admin, 'time' => $time] = conviteCenarioAdmin();
    $membro = User::factory()->create();
    TimeMembroFactory::new()->membro()->create([
        'time_id' => $time->id,
        'usuario_id' => $membro->id,
    ]);

    $this->actingAs($membro);
    CurrentTeam::set($time->id);

    Volt::test('pages.times.show', ['time' => $time])
        ->set('nome', 'Convidado')
        ->set('email', 'alguem@example.com')
        ->call('convidar')
        ->assertForbidden();

    expect(TimeMembroConvite::query()->count())->toBe(0);
});

test('emitir bloqueia e-mail que ja pertence a outra conta', function (): void {
    ['admin' => $admin, 'time' => $time] = conviteCenarioAdmin();

    $ownerB = User::factory()->create(['email' => 'outro@example.com']);
    Conta::factory()->create(['usuario_id' => $ownerB->id]);

    $this->actingAs($admin);
    CurrentTeam::set($time->id);

    Volt::test('pages.times.show', ['time' => $time])
        ->set('nome', 'Convidado')
        ->set('email', 'outro@example.com')
        ->call('convidar')
        ->assertHasErrors(['email']);

    expect(TimeMembroConvite::query()->count())->toBe(0);
});

test('aceitar convite pendente adiciona membro nivel 2 e define o time atual', function (): void {
    ['time' => $time] = conviteCenarioAdmin();
    $convidado = User::factory()->create(['email' => 'novo@example.com']);

    $convite = TimeMembroConvite::factory()->pendente()->create([
        'email' => 'novo@example.com',
        'time_id' => $time->id,
    ]);

    $this->actingAs($convidado)
        ->get(conviteUrlAssinada($convite))
        ->assertRedirect(route('times.show', $time, absolute: false));

    expect($convite->fresh()->status)->toBe(1)
        ->and(TimeMembro::query()->where([
            'time_id' => $time->id,
            'usuario_id' => $convidado->id,
            'nivel_id' => 2,
        ])->exists())->toBeTrue()
        ->and(session(CurrentTeam::SESSION_KEY))->toBe($time->id)
        ->and(session(CurrentTeam::CONTA_SESSION_KEY))->toBe($time->conta_id);
});

test('aceitar convite com e-mail divergente retorna 403', function (): void {
    ['time' => $time] = conviteCenarioAdmin();
    $outro = User::factory()->create(['email' => 'outro@example.com']);

    $convite = TimeMembroConvite::factory()->pendente()->create([
        'email' => 'destinatario@example.com',
        'time_id' => $time->id,
    ]);

    $this->actingAs($outro)
        ->get(conviteUrlAssinada($convite))
        ->assertForbidden();

    expect($convite->fresh()->status)->toBe(0);
});

test('aceitar convite nao pendente retorna 410', function (): void {
    ['time' => $time] = conviteCenarioAdmin();
    $convidado = User::factory()->create(['email' => 'novo@example.com']);

    $convite = TimeMembroConvite::factory()->aceito()->create([
        'email' => 'novo@example.com',
        'time_id' => $time->id,
    ]);

    $this->actingAs($convidado)
        ->get(conviteUrlAssinada($convite))
        ->assertGone();
});

test('aceitar convite bloqueia usuario de outra conta', function (): void {
    ['time' => $time] = conviteCenarioAdmin();

    $ownerB = User::factory()->create(['email' => 'bloqueado@example.com']);
    Conta::factory()->create(['usuario_id' => $ownerB->id]);

    $convite = TimeMembroConvite::factory()->pendente()->create([
        'email' => 'bloqueado@example.com',
        'time_id' => $time->id,
    ]);

    $this->actingAs($ownerB)
        ->get(conviteUrlAssinada($convite))
        ->assertForbidden();

    expect($convite->fresh()->status)->toBe(0)
        ->and(TimeMembro::query()->where([
            'time_id' => $time->id,
            'usuario_id' => $ownerB->id,
        ])->exists())->toBeFalse();
});

test('aceitar convite com link expirado e rejeitado', function (): void {
    ['time' => $time] = conviteCenarioAdmin();
    $convidado = User::factory()->create(['email' => 'novo@example.com']);

    $convite = TimeMembroConvite::factory()->pendente()->create([
        'email' => 'novo@example.com',
        'time_id' => $time->id,
    ]);

    $url = conviteUrlAssinada($convite, dias: 7);

    $this->travel(8)->days();

    $this->actingAs($convidado)
        ->get($url)
        ->assertForbidden();

    expect($convite->fresh()->status)->toBe(0);
});

test('recusar convite pendente marca status 2 e nao cria membro', function (): void {
    ['time' => $time] = conviteCenarioAdmin();
    $convidado = User::factory()->create(['email' => 'novo@example.com']);

    $convite = TimeMembroConvite::factory()->pendente()->create([
        'email' => 'novo@example.com',
        'time_id' => $time->id,
    ]);

    $this->actingAs($convidado)
        ->get(conviteUrlAssinada($convite, 'convites.recusar'))
        ->assertRedirect(route('dashboard', absolute: false));

    expect($convite->fresh()->status)->toBe(2)
        ->and(TimeMembro::query()->where([
            'time_id' => $time->id,
            'usuario_id' => $convidado->id,
        ])->exists())->toBeFalse();
});

test('recusar convite com e-mail divergente retorna 403', function (): void {
    ['time' => $time] = conviteCenarioAdmin();
    $outro = User::factory()->create(['email' => 'outro@example.com']);

    $convite = TimeMembroConvite::factory()->pendente()->create([
        'email' => 'destinatario@example.com',
        'time_id' => $time->id,
    ]);

    $this->actingAs($outro)
        ->get(conviteUrlAssinada($convite, 'convites.recusar'))
        ->assertForbidden();

    expect($convite->fresh()->status)->toBe(0);
});

test('recusar convite nao pendente retorna 410', function (): void {
    ['time' => $time] = conviteCenarioAdmin();
    $convidado = User::factory()->create(['email' => 'novo@example.com']);

    $convite = TimeMembroConvite::factory()->recusado()->create([
        'email' => 'novo@example.com',
        'time_id' => $time->id,
    ]);

    $this->actingAs($convidado)
        ->get(conviteUrlAssinada($convite, 'convites.recusar'))
        ->assertGone();
});
