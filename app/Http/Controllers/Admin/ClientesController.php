<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Clientes;
use App\Services\ServiceLog; 
use App\Models\TimeMembro;
use App\Models\Projetos;
use Illuminate\Support\Facades\Auth;
use DB;


class ClientesController extends Controller
{

	private $log;
	private $cliente;
	private $timeMembro;
	private $projetos;

	public function __construct(ServiceLog $log, Clientes $cliente, TimeMembro $timeMembro, Projetos $projetos) 
	{
		$this->log = $log;		
		$this->cliente = $cliente;		
		$this->timeMembro = $timeMembro;
		$this->projetos = $projetos;		
	}

	public function index()
	{
		$clientes = $this->cliente->listaTodosClientes(Auth::user()->id);

		return view('admin.clientes.index',compact('clientes'));
	}

	public function novoCliente()
	{
		$timeMembro = $this->timeMembro->where('usuario_id',Auth::user()->id)->get();

		return view('admin.clientes.novo',compact('timeMembro'));
	}

	public function salvaCliente(Request $request)
	{
		$cliente = Clientes::create([
			'nome' => $request->nome,
			'email' => $request->email,
			'telefone' => $request->telefone,      
			'time_id' => $request->time_id,
			'usuario_id' => Auth::user()->id
		]);
		
		$this->log->salvaLog(1,1,$cliente->id);
		
		return response()->json($cliente,201);
	}

	public function verCliente($id)
	{		
		$cliente = Clientes::find($id);
		$nivel = TimeMembro::where('usuario_id',Auth::user()->id)->get();
		$projetos = Projetos::where('cliente_id',$cliente->id)->get();
				
		return view('admin.clientes.ver',compact('cliente','nivel','projetos'));
	}

	public function editarCliente(Request $request)
	{
		$this->log->salvaLog(2,1,$request->cliente_id);
		$tarefa = Clientes::find($request->cliente_id)->update(array(
			'nome' => $request->nome,           
			'email' => $request->email,
			'telefone' => $request->telefone
		));

		return response()->json('Cliente atualizado!',200);
	}

	public function excluirCliente(Request $request)
	{		
		$this->log->salvaLog(3,1,$request->clienteId);
		Clientes::find($request->clienteId)->delete();

		return response()->json('Cliente excluido!',200);		
	}
	
}