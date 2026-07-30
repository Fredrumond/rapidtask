<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SituacoesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        DB::table('situacoes')->insert([
            ['id' => 1, 'nome' => 'Novo', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'nome' => 'Andamento', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'nome' => 'Em espera', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'nome' => 'Finalizado', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
