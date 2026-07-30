<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LogAcaoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        DB::table('log_acao')->insert([
            ['id' => 1, 'nome' => 'Registrou', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'nome' => 'Atualizou', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'nome' => 'Excluiu', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'nome' => 'Arquivou', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
