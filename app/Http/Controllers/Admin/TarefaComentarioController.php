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

	public function verComentario(Request $request)
	{
		
		$tarefaComentario = TarefaComentario::find($request->comentarioId);

		$arrResponse['status'] = '200';	
		$arrResponse['data'] = $tarefaComentario;	

		return $arrResponse;		
	}

	public function editarComentario(Request $request)
	{
		
		$tarefaComentario = TarefaComentario::find($request->comentarioId)->update(array(
			'comentario' => $request->comentarioEditar
		));

		$arrResponse['status'] = '200';	

		return $arrResponse;
	}

	public function excluirComentario(Request $request)
	{		
		TarefaComentario::find($request->comentarioId)->delete();

		$arrResponse['status'] = '200';	

		return $arrResponse;
	}

	
}