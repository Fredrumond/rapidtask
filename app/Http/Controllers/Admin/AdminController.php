<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Tarefas;
use App\Models\Projetos;
use App\Models\Clientes;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{	
	private $tarefas;
	private $projetos;
	private $clientes;
	private $log;

	public function __construct(Tarefas $tarefas, Projetos $projetos, Clientes $clientes, Log $log) 
	{		
		$this->tarefas = $tarefas;
		$this->projetos = $projetos;
		$this->clientes = $clientes;
		$this->log = $log;
	}

	public function index()
	{
		$tarefas = $this->tarefas->listaTodasTarefas(Auth::user()->id);
		$tarefasTotal =  $this->tarefas->totalTarefas(Auth::user()->id);
		$projetosTotal =  $this->projetos->totalProjetos(Auth::user()->id);
		$clientesTotal = $this->clientes->totalClientes(Auth::user()->id);
		$logs = $this->log->listaLogs(Auth::user()->id,5);

		return view('admin.dashboard.index',compact('tarefas','tarefasTotal','projetosTotal','clientesTotal','logs'));
	}

	public function logs()
	{
		$logs = $this->log->listaLogs(Auth::user()->id);

		return view('admin.dashboard.logs',compact('logs'));
	}
}