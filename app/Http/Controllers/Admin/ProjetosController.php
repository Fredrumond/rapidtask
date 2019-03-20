<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Projetos;

class ProjetosController extends Controller
{
	public function index(){
		$projetos = Projetos::all();		
		return view('admin.projetos.index',compact('projetos'));
	}

	public function novoProjeto()
	{		
		return view('admin.projetos.nova');
	}

	public function salvaProjeto(Request $request)
	{		
		$projeto = Projetos::create($request->all());
		
		$arrResponse['status'] = '200';	

		return $arrResponse;
	}

	public function verProjeto($id)
	{
		
		$projeto = Projetos::find($id);		

		return view('admin.projetos.ver',compact('projeto'));
	}

	public function editarProjeto(Request $request)
	{
		$projeto = Projetos::find($request->cliente_id)->update(array(
			'nome' => $request->nome,           
			'descricao' => $request->descricao,
			'sigla' => $request->sigla,
			'cliente_id' => $request->cliente_id
		));

		$arrResponse['status'] = '200';	

		return $arrResponse;
	}

	public function excluirProjeto(Request $request)
	{		
		Projetos::find($request->clienteId)->delete();

		$arrResponse['status'] = '200';	

		return $arrResponse;
	}

	
}