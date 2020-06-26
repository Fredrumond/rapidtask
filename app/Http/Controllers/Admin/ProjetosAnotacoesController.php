<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ProjetosAnotacoes;
use Illuminate\Support\Facades\Auth;
use DB;

class ProjetosAnotacoesController extends Controller
{
	public function todasAnotacoes(Request $request){

		$select = "SELECT
		pa.id,
		pa.projeto_id,
		pa.usuario_id,
		pa.anotacao,
		date_format(pa.created_at,'%d %b, %Y às %Hh%i') as data,
		u.name AS nome
		FROM
		projetos_anotacoes pa
		INNER JOIN users u ON
		pa.usuario_id = u.id
		WHERE
		pa.projeto_id = '" . $request->projeto_id . "'
		ORDER BY
		pa.created_at
		DESC
		";

		$projetoAnotacoes = collect(DB::select($select))->all();				
		$arrResponse['anotacoes'] = $projetoAnotacoes;		
		return $arrResponse;
	}	

	public function salvaAnotacao(Request $request)
	{	

		$projetoAnotacao = new ProjetosAnotacoes();
		$projetoAnotacao->anotacao = $request->anotacao;
		$projetoAnotacao->projeto_id =  $request->projeto_id;		
		$projetoAnotacao->usuario_id =  Auth::user()->id;
		$projetoAnotacao->save();
		
		$arrResponse['status'] = '200';	

		return $arrResponse;
	}

	public function verAnotacao(Request $request)
	{
		
		$projetoAnotacao = ProjetosAnotacoes::find($request->anotacao_id);

		$arrResponse['status'] = '200';	
		$arrResponse['data'] = $projetoAnotacao;	

		return $arrResponse;		
	}

	public function editarAnotacao(Request $request)
	{
		
		$projetoAnotacao = ProjetosAnotacoes::find($request->anotacao_id)->update(array(
			'anotacao' => $request->anotacaoEditar
		));

		$arrResponse['status'] = '200';	

		return $arrResponse;
	}

	public function excluirAnotacao(Request $request)
	{		
		ProjetosAnotacoes::find($request->anotacao_id)->delete();

		$arrResponse['status'] = '200';	

		return $arrResponse;
	}

	
}