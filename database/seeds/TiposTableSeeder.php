<?php

use Illuminate\Database\Seeder;
use App\Models\Tipos;

class TiposTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$tipos = ['Problema','Funcionalidade','Suporte','Proposta','Melhoria','Orçamento','Ideia'];
    	$this->command->info("---------- INSERINDO TIPOS ----------");

    	foreach ($tipos as $tipo){
    		$this->command->info("Inserindo: " . $tipo);
    		Tipos::create([
    			'nome' => $tipo
    		]);
    	};
    	
    	$this->command->info("---------- F I N A L I Z A D O ----------");
    }
}
