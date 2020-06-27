<?php

use Illuminate\Database\Seeder;
use App\Models\LogTipo;

class LogTipoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $logTipos = ['Cliente','Projeto','Atividade','Tarefa','Cliente'];
    	$this->command->info("---------- INSERINDO LOG TIPOS ----------");

    	foreach ($logTipos as $logTipo){
    		$this->command->info("Inserindo: " . $logTipo);
    		LogTipo::create([
    			'nome' => $logTipo
    		]);
    	};
    }
}
