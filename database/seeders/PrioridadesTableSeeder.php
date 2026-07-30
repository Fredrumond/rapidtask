<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PrioridadesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        DB::table('prioridades')->insert([
            ['id' => 1, 'nome' => 'Baixo', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'nome' => 'Normal', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'nome' => 'Urgente', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'nome' => 'Imediato', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
