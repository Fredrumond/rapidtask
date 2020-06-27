<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {    	
    	$this->call([
    		SituacoesTableSeeder::class,
    		TiposTableSeeder::class,
    		PrioridadesTableSeeder::class,
    		TimeNivelTableSeeder::class,
    		LogAcaoTableSeeder::class,
    		LogTipoTableSeeder::class,
    	]);
    }
}
