<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Clientes;


class ClientesController extends Controller
{
	public function index(){
		$clientes = Clientes::all();		
		return view('admin.clientes.index',compact('clientes'));
	}

	public function novoCliente()
	{
		
		return view('admin.clientes.novo');
	}

	public function salvaCliente(Request $request)
	{		
		$tarefa = Clientes::create($request->all());
		
		$arrResponse['status'] = '200';	

		return $arrResponse;
	}

	public function verCliente($id)
	{
		
		$cliente = Clientes::find($id);	
		return view('admin.clientes.ver',compact('cliente'));
	}

	public function editarCliente(Request $request)
	{
		$tarefa = Clientes::find($request->cliente_id)->update(array(
			'nome' => $request->nome,           
			'email' => $request->email,
			'telefone' => $request->telefone
		));

		$arrResponse['status'] = '200';	

		return $arrResponse;
	}

	public function excluirCliente(Request $request)
	{		
		Clientes::find($request->clienteId)->delete();

		$arrResponse['status'] = '200';	

		return $arrResponse;
	}
	
}