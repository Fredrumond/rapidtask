<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Clientes;
use App\Models\Projetos;
use App\Models\Time;
use App\Models\TimeMembro;
use App\Models\TimeMembroConvite;
use App\Services\ServiceLog;
use App\Models\Tarefas;
use App\Models\TarefaHistorico;
use App\Models\TarefaComentario;
use App\Models\ProjetosAnotacoes;
use App\Models\ProjetosArquivos;


class TimeController extends Controller
{

	private $log;
	private $time;

	public function __construct(ServiceLog $log, Time $time) 
	{
		$this->log = $log;		
		$this->time = $time;			
	}

	public function index()
	{
		$times = $this->time->listaTodosTimes(Auth::user()->id);	
		return view('admin.time.index',compact('times'));
	}	

	public function salvaTime(Request $request)
	{	
		$rules = [
			'nome' => 'required'
		];

		$messages = [
			'nome.required' => 'O nome é obrigatório.'
		];

		$validatedData = $request->validate($rules,$messages);

		$time = Time::create([
			'nome' =>$request->nome,
			'logo' =>'',
			'usuario_id' => Auth::user()->id
		]);

		$timeMembro = TimeMembro::create([
			'usuario_id' => Auth::user()->id,
			'time_id' => $time->id,
			'nivel_id' => 1
		]);
		
		$this->log->salvaLog(1,5,$time->id);

		return response()->json($time,201);			
	}

	public function verTime($id)
	{				
		$time = Time::findOrfail($id);
		$timeMembro = TimeMembro::where('time_id',$id)->get();
		$timeMembroConvite = TimeMembroConvite::where('time',$id)->get();
		
		return view('admin.time.ver',compact('time','timeMembro','timeMembroConvite'));
	}

	public function editarTime(Request $request)
	{
		$this->log->salvaLog(2,5,$request->time_id);

		$time = Time::find($request->time_id)->update(array(
			'nome' => $request->nome,           
			'logo' => $request->logo
		));		

		return response()->json(['id' => $request->time_id],200);
	}

	public function excluirTime($id)
	{		
		$this->log->salvaLog(3,5,$id);

		//BUSCA TODOS OS CLIENTE DO TIME
		$clientes = Clientes::where('time_id',$id)->get();
		foreach ($clientes as $cliente) {
			$projetos = Projetos::where('cliente_id',$cliente->id)->get();
			foreach ($projetos as $projeto) {
				$tarefas = Tarefas::where('projeto_id',$projeto->id)->get();
				foreach ($tarefas as $tarefa) {
					TarefaHistorico::where('tarefa_id',$tarefa->id)->delete();
					TarefaComentario::where('tarefa_id',$tarefa->id)->delete();
					Tarefas::find($tarefa->id)->delete();			
				}

				ProjetosArquivos::where('projeto_id',$projeto->id)->delete();
				ProjetosAnotacoes::where('projeto_id',$projeto->id)->delete();
				Projetos::find($projeto->id)->delete();
			}

			Clientes::find($cliente->id)->delete();
		}
		
		TimeMembro::where('time_id',$id)->delete();
		Time::find($id)->delete();

		return response()->json('Time Excluido',200);
	}
	
}