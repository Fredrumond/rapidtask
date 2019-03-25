<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\TarefaRequest;
use App\Models\Tarefas;
use App\Models\Tipos;
use App\Models\Prioridades;
use App\Models\Situacoes;
use App\Models\Projetos;
use Illuminate\Support\Facades\Auth;

class TarefasController extends Controller
{
	public function index(){
		$tarefas = Tarefas::where('status',0)->get();		
		return view('admin.tarefas.index',compact('tarefas'));
	}

	public function novaTarefa()
	{
		$tipos = Tipos::all();
		$situacoes = Situacoes::all();
		$prioridades = Prioridades::all();
		$projetos = Projetos::all();
		return view('admin.tarefas.nova',compact('tipos','situacoes','prioridades','projetos'));
	}

	public function salvaTarefa(Request $request)
	{		
		
		$tarefa = new Tarefas();
		$tarefa->titulo = $request->titulo;
		$tarefa->projeto_id =  $request->projeto_id;
		$tarefa->tipo_id =  $request->tipo_id;
		$tarefa->situacao_id =  $request->situacao_id;
		$tarefa->prioridade_id =  $request->prioridade_id;
		$tarefa->descricao =  $request->descricao;
		$tarefa->dt_inicio =  $request->dt_inicio;
		$tarefa->dt_prevista =  $request->dt_prevista;
		$tarefa->dt_fim =  $request->dt_fim;
		$tarefa->tempo_estimado =  $request->tempo_estimado;
		$tarefa->usuario_id =  Auth::user()->id;
		$tarefa->save();
		
		$arrResponse['status'] = '200';	

		return $arrResponse;
	}

	public function verTarefa($id)
	{
		
		$tarefa = Tarefas::find($id);
		$tipos = Tipos::all();
		$situacoes = Situacoes::all();
		$prioridades = Prioridades::all();
		$projetos = Projetos::all();

		return view('admin.tarefas.ver',compact('tarefa','tipos','situacoes','prioridades','projetos'));
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
			'dt_fim' => $request->dt_fim,
			'tempo_estimado' => $request->tempo_estimado,
			'projeto_id' => $request->projeto_id
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