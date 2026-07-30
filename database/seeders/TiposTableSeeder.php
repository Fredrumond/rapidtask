<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TiposTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        DB::table('tipos')->insert([
            ['id' => 1, 'nome' => 'Problema', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'nome' => 'Funcionalidade', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'nome' => 'Suporte', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'nome' => 'Proposta', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'nome' => 'Melhoria', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 6, 'nome' => 'Orçamento', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 7, 'nome' => 'Ideia', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
