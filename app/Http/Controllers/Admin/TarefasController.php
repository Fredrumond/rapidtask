<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\TarefaRequest;
use App\Models\Tarefas;

class TarefasController extends Controller
{
	public function index(){
		$tarefas = Tarefas::all();		
		return view('admin.tarefas.index',compact('tarefas'));
	}

	public function novaTarefa()
	{
		return view('admin.tarefas.nova');
	}

	public function salvaTarefa(Request $request)
	{		
		$tarefa = Tarefas::create($request->all());
		
		$arrResponse['status'] = '200';
		

		return $arrResponse;

        
	}
}