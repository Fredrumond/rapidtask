<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\TarefaRequest;
use App\Models\Tarefas;
use App\Models\Tipos;
use App\Models\Prioridades;
use App\Models\Situacoes;
use App\Models\Projetos;
use Illuminate\Support\Facades\Auth;
use App\Services\ServiceTarefaHistorico; 
use App\Services\ServiceLog; 
use DB;

class TarefasController extends Controller
{

	private $log;
	private $tarefaHistorico;
	private $tarefas;

	public function __construct(ServiceLog $log, ServiceTarefaHistorico $tarefaHistorico, Tarefas $tarefas, Projetos $projetos) 
	{
		$this->log = $log;
		$this->tarefaHistorico = $tarefaHistorico;
		$this->tarefas = $tarefas;
		$this->projetos = $projetos;
	}

	public function index()
	{		
		$tarefas = $this->tarefas->listaTodasTarefas(Auth::user()->id);
		return view('admin.tarefas.index',compact('tarefas'));
	}

	public function novaTarefa()
	{
		$projetos = $this->projetos->listaTodosProjetos(Auth::user()->id);		
		$tipos = Tipos::all();
		$situacoes = Situacoes::all();
		$prioridades = Prioridades::all();
		
		return view('admin.tarefas.nova',compact('tipos','situacoes','prioridades','projetos'));
	}

	public function salvaTarefa(Request $request)
	{		
		$tarefa = new Tarefas();
		$tarefa->titulo = $request->titulo;
		$tarefa->projeto_id =  $request->projeto_id;
		$tarefa->tipo_id =  $request->tipo_id;
		$tarefa->situacao_id =  $request->situacao_id;
		$tarefa->prioridade_id =  $request->prioridade_id;
		$tarefa->descricao =  $request->descricao;
		$tarefa->dt_inicio =  $request->dt_inicio;
		$tarefa->dt_prevista =  $request->dt_prevista;
		$tarefa->dt_fim =  $request->dt_fim;
		$tarefa->tempo_estimado =  $request->tempo_estimado;
		$tarefa->usuario_id =  Auth::user()->id;
		$tarefa->save();

		$this->log->salvaLog(1,4,$tarefa->id);

		return response()->json($tarefa,201);
	}

	public function verTarefa($id)
	{
		
		$tarefa = Tarefas::find($id);
		$tipos = Tipos::all();
		$situacoes = Situacoes::all();
		$prioridades = Prioridades::all();
		$projetos = Projetos::all();

		return view('admin.tarefas.ver',compact('tarefa','tipos','situacoes','prioridades','projetos'));
	}

	public function editarTarefa(Request $request)
	{
		
		$this->tarefaHistorico->salvaHistorico($request);
		$this->log->salvaLog(2,4,$request->tarefa_id);
		$redireciona = false;

		//VERIFICA SE A TAREFA FOI FINALIZADA
		if($request->situacao_id == 4){
			$dt_fim = date("Y-m-d");
			$redireciona = true;
		} else {
			$dt_fim = $request->dt_fim;			
		}

		$tarefa = Tarefas::find($request->tarefa_id)->update(array(
			'titulo' => $request->titulo,           
			'tipo_id' => $request->tipo_id,
			'situacao_id' => $request->situacao_id,
			'prioridade_id' => $request->prioridade_id,
			'descricao' => $request->descricao,
			'dt_inicio' => $request->dt_inicio,
			'dt_prevista' => $request->dt_prevista,
			'dt_fim' => $dt_fim,
			'tempo_estimado' => $request->tempo_estimado,
			'projeto_id' => $request->projeto_id
		));

		$arrResponse['status'] = '200';	
		$arrResponse['id'] = $request->tarefa_id;	
		$arrResponse['redireciona'] = $redireciona;	

		return $arrResponse;
	}

	public function excluirTarefa(Request $request)
	{	
		$this->log->salvaLog(3,4,$request->tarefaId);	
		Tarefas::find($request->tarefaId)->delete();

		$arrResponse['status'] = '200';	

		return $arrResponse;
	}

	public function arquivarTarefa(Request $request)
	{
		
		$tarefa = Tarefas::find($request->tarefaId)->update(array(
			'status' => 1
		));
		
		$arrResponse['status'] = '200';	

		return $arrResponse;
	}

	public function recuperarTarefa(Request $request)
	{
		
		$tarefa = Tarefas::find($request->tarefaId)->update(array(
			'status' => 0
		));
		
		$arrResponse['status'] = '200';	

		return $arrResponse;
	}

	public function verTarefasArquivadas()
	{
		$tarefas = Tarefas::where('status',1)->get();		
		return view('admin.tarefas.arquivadas',compact('tarefas'));
	}

	public function verTarefasRelatorio()
	{
		$select = "SELECT 
		tp.nome as TIPO,
		COUNT(case when t.situacao_id = 1 then 1 end ) as NOVO,
		COUNT(case when t.situacao_id = 2 then 1 end ) as ANDAMENTO,
		COUNT(case when t.situacao_id = 3 then 1 end ) as ESPERA,
		COUNT(case when t.situacao_id = 4 then 1 end ) as FINALIZADO,
		COUNT(case when t.prioridade_id = 1 then 1 end ) as BAIXA,
		COUNT(case when t.prioridade_id = 2 then 1 end ) as NORMAL,
		COUNT(case when t.prioridade_id = 3 then 1 end ) as URGENTE,
		COUNT(case when t.prioridade_id = 4 then 1 end ) as IMEDIATO
		from tarefas t 
		INNER JOIN tipos tp ON t.tipo_id = tp.id
		INNER JOIN projetos p ON p.id = t.projeto_id
		INNER JOIN TIME ti ON ti.id = p.time_id
		INNER JOIN time_membro tm ON tm.time_id = ti.id
		WHERE tm.usuario_id = '" . Auth::user()->id . "'
		GROUP BY t.tipo_id";		

		$relatorio = collect(DB::select($select))->all();		
		
		return view('admin.tarefas.relatorio',compact('relatorio'));
	}
}