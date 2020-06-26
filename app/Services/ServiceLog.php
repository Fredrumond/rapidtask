<?php

namespace App\Services;
use Illuminate\Support\Facades\Auth;
use App\Models\Log;

class ServiceLog
{
	/*
		- AÇÃO
			- 1: Registrou
			- 2: Atualizou
			- 3: Excluiu
			- 4: Arquivou

		- TIPO
			- 1: Cliente
			- 2: Projeto
			- 3: Atividade
			- 4: Tarefa
			- 5: Time
	*/

	public function salvaLog($acao,$tipo,$identificacao)
	{
		$log = new Log();
		$log->log_acao_id = $acao;
		$log->log_tipo_id =  $tipo;		
		$log->usuario_id =  Auth::user()->id;
		$log->identificacao =  $identificacao;
		$log->save();		
	}	

}