<?php

use App\Models\Conta;
use App\Models\Time;
use App\Models\User;
use App\Support\ContaBackfill;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

test('schema possui tabela conta e time.conta_id', function () {
    expect(Schema::hasTable('conta'))->toBeTrue()
        ->and(Schema::hasColumn('time', 'conta_id'))->toBeTrue();
});

test('Conta factory persiste com usuario', function () {
    $conta = Conta::factory()->comNome('Acme Corp')->create();

    expect($conta)->toBeInstanceOf(Conta::class)
        ->and($conta->nome)->toBe('Acme Corp')
        ->and($conta->usuario)->toBeInstanceOf(User::class)
        ->and($conta->deleted_at)->toBeNull();
});

test('Time factory associa Conta com o mesmo usuario_id', function () {
    $user = User::factory()->create();
    $time = Time::factory()->create(['usuario_id' => $user->id]);

    expect($time->conta_id)->not->toBeNull()
        ->and($time->conta)->toBeInstanceOf(Conta::class)
        ->and($time->conta->usuario_id)->toBe($user->id);
});

test('relacoes Conta::times e Time::conta', function () {
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

    expect($conta->times)->toHaveCount(2)
        ->and($conta->times->pluck('id')->all())->toContain($timeA->id, $timeB->id)
        ->and($timeA->fresh()->conta->id)->toBe($conta->id);
});

test('mesmo usuario pode ser owner de multiplas contas', function () {
    $user = User::factory()->create();

    $contaA = Conta::factory()->create(['usuario_id' => $user->id, 'nome' => 'Conta A']);
    $contaB = Conta::factory()->create(['usuario_id' => $user->id, 'nome' => 'Conta B']);

    expect(Conta::query()->where('usuario_id', $user->id)->count())->toBe(2)
        ->and($contaA->id)->not->toBe($contaB->id);
});

test('backfill cria uma conta por usuario_id distinto e associa times', function () {
    $userA = User::factory()->create(['name' => 'Alice']);
    $userB = User::factory()->create(['name' => 'Bob']);

    Schema::table('time', function (Blueprint $table) {
        $table->unsignedBigInteger('conta_id')->nullable()->change();
    });

    $timeA1Id = DB::table('time')->insertGetId([
        'nome' => 'Time A1',
        'logo' => null,
        'usuario_id' => $userA->id,
        'conta_id' => null,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $timeA2Id = DB::table('time')->insertGetId([
        'nome' => 'Time A2',
        'logo' => null,
        'usuario_id' => $userA->id,
        'conta_id' => null,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $timeBId = DB::table('time')->insertGetId([
        'nome' => 'Time B',
        'logo' => null,
        'usuario_id' => $userB->id,
        'conta_id' => null,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    ContaBackfill::run();

    $timeA1 = DB::table('time')->where('id', $timeA1Id)->first();
    $timeA2 = DB::table('time')->where('id', $timeA2Id)->first();
    $timeB = DB::table('time')->where('id', $timeBId)->first();

    expect($timeA1->conta_id)->not->toBeNull()
        ->and($timeA2->conta_id)->toBe($timeA1->conta_id)
        ->and($timeB->conta_id)->not->toBeNull()
        ->and($timeB->conta_id)->not->toBe($timeA1->conta_id);

    $contaA = Conta::query()->find($timeA1->conta_id);
    $contaB = Conta::query()->find($timeB->conta_id);

    expect($contaA->usuario_id)->toBe($userA->id)
        ->and($contaA->nome)->toBe('Conta de Alice')
        ->and($contaB->usuario_id)->toBe($userB->id)
        ->and($contaB->nome)->toBe('Conta de Bob');

    expect(DB::table('time')->whereNull('conta_id')->count())->toBe(0);
});

test('criarCenarioDoisTimes cria times com conta_id', function () {
    $cenario = criarCenarioDoisTimes();

    expect($cenario['timeA']->conta_id)->not->toBeNull()
        ->and($cenario['timeB']->conta_id)->not->toBeNull()
        ->and($cenario['timeA']->conta_id)->not->toBe($cenario['timeB']->conta_id);
});
