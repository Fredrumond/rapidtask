<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

class ContaBackfill
{
    /**
     * Cria uma conta por usuario_id distinto nos times sem conta_id e associa os times.
     */
    public static function run(): void
    {
        $ownerIds = DB::table('time')
            ->whereNull('conta_id')
            ->distinct()
            ->orderBy('usuario_id')
            ->pluck('usuario_id');

        $now = now();

        foreach ($ownerIds as $usuarioId) {
            $userName = DB::table('users')->where('id', $usuarioId)->value('name');
            $nome = filled($userName) ? 'Conta de '.$userName : 'Conta';

            $contaId = DB::table('conta')->insertGetId([
                'nome' => $nome,
                'usuario_id' => $usuarioId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('time')
                ->where('usuario_id', $usuarioId)
                ->whereNull('conta_id')
                ->update(['conta_id' => $contaId]);
        }
    }
}
