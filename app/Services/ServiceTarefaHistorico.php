<?php

namespace App\Services;
use Illuminate\Support\Facades\Auth;
use App\Models\Tarefas;
use App\Models\TarefaHistorico;


class ServiceTarefaHistorico
{

	public function salvaHistorico($request)
	{

		$tipo = $this->verificaAlteracao($request->tarefa_id,$request->tipo_id,'o tipo');
		$situacao = $this->verificaAlteracao($request->tarefa_id,$request->situacao_id,'a situação');
		$prioridade = $this->verificaAlteracao($request->tarefa_id,$request->prioridade_id,'a prioridade');
	}

	private function verificaAlteracao($tarefaID,$item,$atividade)
	{
		$tarefa = Tarefas::find($tarefaID);

		if($tarefa->tipo_id != $item){
			$tarefaHistorico = new TarefaHistorico();
			$tarefaHistorico->tarefa_id = $tarefaID;
			$tarefaHistorico->usuario_id =  Auth::user()->id;
			$tarefaHistorico->atividade =  $atividade;
			$tarefaHistorico->save();
		}
		
	}

}