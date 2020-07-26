<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Projetos;
use App\Models\ProjetosArquivos;
use App\Models\Clientes;
use App\Services\ServiceLog; 
use Illuminate\Support\Facades\Auth;
use App\Models\TimeMembro;
use App\Models\Tarefas;
use App\Http\Controllers\UploadController;
use App\Models\TarefaHistorico;
use App\Models\TarefaComentario;
use App\Models\ProjetosAnotacoes;
use File;

class ProjetosController extends Controller
{
	private $log;
	private $upload;
	private $projetos;

	public function __construct(ServiceLog $log, UploadController $upload, Projetos $projetos) 
	{
		$this->log = $log;		
		$this->upload = $upload;		
		$this->projetos = $projetos;		
	}

	public function index()
	{
		$projetos = $this->projetos->listaTodosProjetos(Auth::user()->id);		
		return view('admin.projetos.index',compact('projetos'));
	}

	public function novoProjeto()
	{		
		$clientes = Clientes::all();
		$timeMembro = TimeMembro::where('usuario_id',Auth::user()->id)->get();
		
		return view('admin.projetos.novo',compact('clientes','timeMembro'));
	}

	public function salvaProjeto(Request $request)
	{
		$projeto = new Projetos();
		$projeto->nome = $request->nome;
		$projeto->sigla =  $request->sigla;
		$projeto->time_id =  $request->time_id;
		$projeto->cliente_id =  $request->cliente_id;
		$projeto->descricao =  $request->descricao;		
		$projeto->usuario_id =  Auth::user()->id;
		$projeto->save();
		
		$this->log->salvaLog(1,2,$projeto->id);

		return response()->json(['id' => $projeto->id],200);
	}

	public function verProjeto($id)
	{
		$clientes = Clientes::all();
		$projeto = Projetos::find($id);		

		return view('admin.projetos.ver',compact('projeto','clientes'));
	}

	public function detalheProjeto($id)
	{		
		$projeto = Projetos::find($id);
		$atraso = $this->calculaAtraso($projeto->dt_inicio,$projeto->dt_prevista);
		$tarefasTotal = Tarefas::where('projeto_id',$projeto->id)->get();
		$tarefasNovas = Tarefas::where('projeto_id',$projeto->id)->where('situacao_id','=','1')->where('status','=','0')->get();
		$tarefasAndamento = Tarefas::where('projeto_id',$projeto->id)->where('situacao_id','=','2')->where('status','=','0')->get();
		$tarefasEspera = Tarefas::where('projeto_id',$projeto->id)->where('situacao_id','=','3')->where('status','=','0')->get();
		$tarefasConcluida = Tarefas::where('projeto_id',$projeto->id)->where('situacao_id','=','4')->where('status','=','0')->get();		
		$progressoProjeto = $this->calculaProgresso($tarefasTotal,$tarefasConcluida);
		
		return view('admin.projetos.detalhes',compact('projeto','atraso','progressoProjeto','tarefasNovas','tarefasAndamento','tarefasEspera','tarefasConcluida'));
	}

	public function editarProjeto(Request $request)
	{
		$this->log->salvaLog(2,2,$request->projeto_id);

		$projeto = Projetos::find($request->projeto_id)->update(array(
			'nome' => $request->nome,           
			'descricao' => $request->descricao,
			'sigla' => $request->sigla,
			'cliente_id' => $request->cliente_id,
			'dt_inicio' => $request->dt_inicio,
			'dt_prevista' => $request->dt_prevista,
			'dt_fim' => $request->dt_fim
		));

		return response()->json(['id' => $request->projeto_id],200);		
	}

	public function excluirProjeto(Request $request)
	{	

		$this->log->salvaLog(3,2,$request->projetoId);

		//BUSCA TODAS AS TAREFAS E APAGA OS COMENTARIOS E HISTORICO
		$tarefas = Tarefas::where('projeto_id',$request->projetoId)->get();
		foreach ($tarefas as $tarefa) {
			TarefaHistorico::where('tarefa_id',$tarefa->id)->delete();
			TarefaComentario::where('tarefa_id',$tarefa->id)->delete();
			Tarefas::find($tarefa->id)->delete();			
		}

		ProjetosArquivos::where('projeto_id',$request->projetoId)->delete();
		ProjetosAnotacoes::where('projeto_id',$request->projetoId)->delete();

		Projetos::find($request->projetoId)->delete();

		return response()->json('Time Excluido',200);
	}

	public function arquivos(Request $request)
	{
		$arquivos = ProjetosArquivos::where('projeto_id',$request->projeto_id)->get();
		return response()->json(['arquivos' => $arquivos],200);
	}

	public function novoArquivoProjeto(Request $request)
	{		
		$data = [
			'arquivo' => $request->file('arquivo'),
			'arquivo_permitido' => 'pdf',
			'caminho' => '/public/projetos/arquivos' 			
		];

		$upload = $this->upload->uploadData($data);

		$projetoArquivo = new ProjetosArquivos();
		$projetoArquivo->projeto_id = $request->projeto_id;
		$projetoArquivo->usuario_id = Auth::user()->id;
		$projetoArquivo->nome = $request->nome;
		$projetoArquivo->descricao = $request->descricao;
		$projetoArquivo->src = $upload;	
		$projetoArquivo->save();
		
		return response()->json(['msg' => 'Arquivo salvo com sucesso!'], 200);	
	}

	public function excluirArquivoProjeto(Request $request)
	{
		// dd($request->all());
		$retornaArquivo = ProjetosArquivos::find($request->arquivo_id);
		File::delete('projetos/arquivos/'.$retornaArquivo->src);
		ProjetosArquivos::find($request->arquivo_id)->delete();

		return response()->json('Arquivo excluido com sucesso!',200);
	}

	public function calculaAtraso($dt_inicio,$dt_fim): int
	{		
		if ($dt_fim != null) {
			if(date('Y/m/d') > $dt_fim){
				$diferenca = strtotime(date('Y/m/d')) - strtotime($dt_fim);
				$dias = floor($diferenca / (60 * 60 * 24));
				return $dias;
			}
		}

		return 0;
	}

	public function calculaProgresso($total,$finalizadas)
	{
		if(count($finalizadas) > 0) {
			$progresso = (count($finalizadas) * 100 ) / count($total);
			return number_format($progresso, 2);
		}		
		return 0;		
	}	
}