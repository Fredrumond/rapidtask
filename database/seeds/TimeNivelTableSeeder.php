<?php

use Illuminate\Database\Seeder;
use App\Models\TimeNivel;

class TimeNivelTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $niveis = ['Nivel I','Nivel II','Nivel III'];
    	$this->command->info("---------- INSERINDO TIME NIVEIS ----------");

    	foreach ($niveis as $nivel){
    		$this->command->info("Inserindo: " . $nivel);
    		TimeNivel::create([
    			'nome' => $nivel
    		]);
    	};
    	
    	$this->command->info("---------- F I N A L I Z A D O ----------");
    }
}
