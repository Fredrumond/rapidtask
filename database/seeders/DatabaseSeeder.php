<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            TiposTableSeeder::class,
            SituacoesTableSeeder::class,
            PrioridadesTableSeeder::class,
            TimeNivelTableSeeder::class,
            LogAcaoTableSeeder::class,
            LogTipoTableSeeder::class,
            AdminUserSeeder::class,
        ]);
    }
}
