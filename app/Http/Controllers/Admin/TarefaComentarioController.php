<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TarefaComentario;
use Illuminate\Support\Facades\Auth;
use DB;

class TarefaComentarioController extends Controller
{
	public function todosComentarios(Request $request){

		$select = "SELECT
		tc.id,
		tc.tarefa_id,
		tc.usuario_id,
		tc.comentario,
		date_format(tc.created_at,'%d %b, %Y às %Hh%i') as data,
		u.name AS nome
		FROM
		tarefa_comentario tc
		INNER JOIN users u ON
		tc.usuario_id = u.id
		WHERE
		tc.tarefa_id = '" . $request->tarefa_id . "'
		ORDER BY
		tc.created_at
		DESC
		";

		$tarefaComentarios = collect(DB::select($select))->all();				
		$arrResponse['comentarios'] = $tarefaComentarios;		
		return $arrResponse;
	}	

	public function salvaComentario(Request $request)
	{	

		$tarefaComentario = new TarefaComentario();
		$tarefaComentario->comentario = $request->comentario;
		$tarefaComentario->tarefa_id =  $request->tarefaId;		
		$tarefaComentario->usuario_id =  Auth::user()->id;
		$tarefaComentario->save();
		
		$arrResponse['status'] = '200';	

		return $arrResponse;
	}

	public function verComentario($id)
	{
		
		$tarefa = Tarefas::find($id);
		$tipos = Tipos::all();
		$situacoes = Situacoes::all();
		$prioridades = Prioridades::all();
		$projetos = Projetos::all();

		return view('admin.tarefas.ver',compact('tarefa','tipos','situacoes','prioridades','projetos'));
	}

	public function editarComentario(Request $request)
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

	public function excluirComentario(Request $request)
	{		
		Tarefas::find($request->tarefaId)->delete();

		$arrResponse['status'] = '200';	

		return $arrResponse;
	}

	
}