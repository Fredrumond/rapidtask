<?php

use Illuminate\Database\Seeder;
use App\Models\Situacoes;

class SituacoesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$situacoes = ['Novo','Andamento','Em espera','Finalizado'];
    	$this->command->info("---------- INSERINDO SITUACOES ----------");

    	foreach ($situacoes as $situacao){
    		$this->command->info("Inserindo: " . $situacao);
    		Situacoes::create([
    			'nome' => $situacao
    		]);
    	};
    	
    	$this->command->info("---------- F I N A L I Z A D O ----------");
    }
}
