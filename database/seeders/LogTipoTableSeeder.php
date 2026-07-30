<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LogTipoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        DB::table('log_tipo')->insert([
            ['id' => 1, 'nome' => 'Cliente', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'nome' => 'Projeto', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'nome' => 'Atividade', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'nome' => 'Tarefa', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'nome' => 'Cliente', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
