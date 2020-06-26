<?php

use Illuminate\Database\Seeder;
use App\Models\Prioridades;

class PrioridadesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$prioridades = ['Baixo','Normal','Urgente','Imediato'];
    	$this->command->info("---------- INSERINDO PRIORIDADES ----------");

    	foreach ($prioridades as $prioridade){
    		$this->command->info("Inserindo: " . $prioridade);
    		Prioridades::create([
    			'nome' => $prioridade
    		]);
    	};
    	
    	$this->command->info("---------- F I N A L I Z A D O ----------");
    }
}
