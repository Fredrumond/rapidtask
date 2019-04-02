<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TarefaHistorico;
use Illuminate\Support\Facades\Auth;
use DB;

class TarefaHistoricoController extends Controller
{
	public function todoHistorico(Request $request){

		$select = "SELECT
		th.atividade,
		DATE_FORMAT(
		th.created_at,
		'%d %b, %Y às %Hh%i'
		) AS data,
		u.name AS nome
		FROM
		tarefa_historico th
		INNER JOIN users u ON
		th.usuario_id = u.id
		WHERE
		th.tarefa_id = '" . $request->tarefa_id . "'
		ORDER BY
		th.created_at
		DESC

		";

		$tarefaHistorico = collect(DB::select($select))->all();				
		$arrResponse['historico'] = $tarefaHistorico;		
		return $arrResponse;
	}
	
}