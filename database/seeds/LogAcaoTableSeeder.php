<?php

use Illuminate\Database\Seeder;
use App\Models\LogAcao;

class LogAcaoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $logAcoes = ['Registrou','Atualizou','Excluiu','Arquivou'];
    	$this->command->info("---------- INSERINDO LOG AÇOES ----------");

    	foreach ($logAcoes as $logAcao){
    		$this->command->info("Inserindo: " . $logAcao);
    		LogAcao::create([
    			'nome' => $logAcao
    		]);
    	};
    }
}
