<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\TarefaRequest;
use App\Models\Tarefas;
use App\Models\Tipos;
use App\Models\Prioridades;
use App\Models\Situacoes;

class TarefasController extends Controller
{
	public function index(){
		$tarefas = Tarefas::where('status',0)->get();		
		return view('admin.tarefas.index',compact('tarefas'));
	}

	public function novaTarefa()
	{
		return view('admin.tarefas.nova');
	}

	public function salvaTarefa(Request $request)
	{		
		$tarefa = Tarefas::create($request->all());
		
		$arrResponse['status'] = '200';	

		return $arrResponse;
	}

	public function verTarefa($id)
	{
		
		$tarefa = Tarefas::find($id);
		$tipos = Tipos::all();
		$situacoes = Situacoes::all();
		$prioridades = Prioridades::all();

		return view('admin.tarefas.ver',compact('tarefa','tipos','situacoes','prioridades'));
	}

	public function editarTarefa(Request $request)
	{
		$tarefa = Tarefas::find($request->tarefa_id)->update(array(
			'titulo' => $request->titulo,           
			'tipo_id' => $request->tipo_id,
			'situacao_id' => $request->situacao_id,
			'prioridade_id' => $request->prioridade_id,
			'descricao' => $request->descricao,
			'dt_inicio' => $request->dt_inicio,
			'dt_prevista' => $request->dt_prevista,
			'tempo_estimado' => $request->tempo_estimado
		));

		$arrResponse['status'] = '200';	

		return $arrResponse;
	}

	public function excluirTarefa(Request $request)
	{		
		Tarefas::find($request->tarefaId)->delete();

		$arrResponse['status'] = '200';	

		return $arrResponse;
	}

	public function arquivarTarefa(Request $request)
	{
		$tarefa = Tarefas::find($request->tarefaId)->update(array(
			'status' => 1
		));
		
		$arrResponse['status'] = '200';	

		return $arrResponse;
	}

	public function verTarefasArquivadas()
	{
		$tarefas = Tarefas::where('status',1)->get();		
		return view('admin.tarefas.arquivadas',compact('tarefas'));
	}
}