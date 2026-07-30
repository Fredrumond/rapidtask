<?php

use App\Models\Cliente;
use App\Models\Projeto;
use App\Models\Tarefa;
use App\Models\Time;
use App\Models\User;
use Database\Factories\TimeMembroFactory;
use Database\Seeders\PrioridadesTableSeeder;
use Database\Seeders\SituacoesTableSeeder;
use Database\Seeders\TimeNivelTableSeeder;
use Database\Seeders\TiposTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class)->in('Feature');

uses(TestCase::class)->in('Unit');

function seedLookups(): void
{
    if (DB::table('tipos')->exists()) {
        return;
    }

    test()->seed([
        TiposTableSeeder::class,
        SituacoesTableSeeder::class,
        PrioridadesTableSeeder::class,
        TimeNivelTableSeeder::class,
    ]);
}

/**
 * @return array{
 *     userA: User,
 *     userB: User,
 *     timeA: Time,
 *     timeB: Time,
 *     clienteA: Cliente,
 *     clienteB: Cliente,
 *     projetoA: Projeto,
 *     projetoB: Projeto,
 *     tarefaA: Tarefa,
 *     tarefaB: Tarefa,
 * }
 */
function criarCenarioDoisTimes(): array
{
    seedLookups();

    $userA = User::factory()->create();
    $timeA = Time::factory()->create(['usuario_id' => $userA->id]);
    TimeMembroFactory::new()->admin()->create([
        'time_id' => $timeA->id,
        'usuario_id' => $userA->id,
    ]);

    $userB = User::factory()->create();
    $timeB = Time::factory()->create(['usuario_id' => $userB->id]);
    TimeMembroFactory::new()->admin()->create([
        'time_id' => $timeB->id,
        'usuario_id' => $userB->id,
    ]);

    $clienteA = Cliente::factory()->create([
        'time_id' => $timeA->id,
        'usuario_id' => $userA->id,
    ]);

    $clienteB = Cliente::factory()->create([
        'time_id' => $timeB->id,
        'usuario_id' => $userB->id,
    ]);

    $projetoA = Projeto::factory()->create([
        'time_id' => $timeA->id,
        'cliente_id' => $clienteA->id,
        'usuario_id' => $userA->id,
    ]);

    $projetoB = Projeto::factory()->create([
        'time_id' => $timeB->id,
        'cliente_id' => $clienteB->id,
        'usuario_id' => $userB->id,
    ]);

    $tarefaA = Tarefa::factory()->create([
        'projeto_id' => $projetoA->id,
        'usuario_id' => $userA->id,
    ]);

    $tarefaB = Tarefa::factory()->create([
        'projeto_id' => $projetoB->id,
        'usuario_id' => $userB->id,
    ]);

    return compact(
        'userA',
        'userB',
        'timeA',
        'timeB',
        'clienteA',
        'clienteB',
        'projetoA',
        'projetoB',
        'tarefaA',
        'tarefaB',
    );
}
