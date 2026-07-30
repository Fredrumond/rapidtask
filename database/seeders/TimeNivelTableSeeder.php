<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TimeNivelTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        DB::table('time_nivel')->insert([
            ['id' => 1, 'nome' => 'Nivel I', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'nome' => 'Nivel II', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'nome' => 'Nivel III', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
