<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Time;
use App\Models\TimeMembro;
use App\Models\TimeNivel;
use App\Models\TimeMembroConvite;
use App\Services\ServiceLog;
use App\Http\Controllers\MailController;
use Illuminate\Support\Facades\Auth;


class TimeMembroController extends Controller
{

	private $log;
	private $mail;
	private $timeMembroConvite;
	private $timeMembro;
	private $usuario;
	private $time;

	public function __construct(ServiceLog $log,MailController $mail, TimeMembroConvite $timeMembroConvite, TimeMembro $timeMembro, User $usuario, Time $time) {
		$this->log = $log;		
		$this->mail = $mail;		
		$this->timeMembroConvite = $timeMembroConvite;		
		$this->timeMembro = $timeMembro;		
		$this->usuario = $usuario;		
		$this->time = $time;		
	}

	public function novoMembro(Request $request)
	{	
		$verificaUsuario = $this->usuario->where('email', '=', $request->email)->first();

		if($verificaUsuario){

			$this->timeMembro::create([
				'usuario_id' => $verificaUsuario->id,
				'time_id' => $request->time,
				'nivel_id' => 2
			]);

			return response()->json(['success' => 'Novo membro adicionado ao time!'], 201);
		}

		$timeIdentificacao = $this->time->find($request->time);		

		$timeMembroConvite = $this->timeMembroConvite::create([
			'nome' => $request->nome,
			'email' => $request->email,
			'time' => $request->time,
			'status' => 0,
		]);

		$data = [
			'view' => 'mail.conviteTime',
			'to'   => $request->email, 
			'receiverName' => $request->nome,
			'subject' => 'Convite para o time ' . $timeIdentificacao->nome,
			'senderName' => 'RAPIDTASK',
			'nomeDonoTime' => Auth::user()->name,
			'nomeTime' => $timeIdentificacao->nome,
			'identificacao' => $timeMembroConvite->id
		];

		$this->mail->emailConviteTime($data);
		
		return response()->json(['success' => 'Convite enviado!','time' => $request->time], 201);		
	}

	public function aceitarConvite($id)
	{
		$convite = $this->timeMembroConvite->find($id);
		$senha = $this->gerar_senha(8, true, true, true, false);

		$user = User::create([
			'name' => $convite->nome,
			'email' => $convite->email,
			'satus' => 0,
			'password' => Hash::make($senha)
		]);

		$this->timeMembroConvite->find($convite->id)->delete();

		$timeMembro = new $this->timeMembro;
		$timeMembro->usuario_id = $user->id;
		$timeMembro->time_id = $convite->time;
		$timeMembro->nivel_id = 2;
		$timeMembro->save();

		$data = [
			'view' => 'mail.novoUsuario',
			'to'   => $user->email, 
			'receiverName' => $user->name,
			'subject' => 'Muito bem! Agora você faz parte da plataforma',
			'senderName' => 'RAPIDTASK',			
			'senha' => $senha
		];

		$this->mail->emailBoasVindas($data);

		return view('mail.conviteAceito');
	}

	public function recusarConvite($id)
	{
		$convite = $this->timeMembroConvite->find($id);
		$this->timeMembroConvite->find($convite->id)->delete();
		
		return view('mail.conviteRecusado');
	}

	public function verMembro($time,$membro)
	{
		$niveis = TimeNivel::all();
		$timeMembro = TimeMembro::where('time_id',$time)->where('usuario_id',$membro)->first();		
		return view('admin.time.ver-membro',compact('timeMembro','niveis'));
	}

	public function editarMembro(Request $request)
	{
		$time = TimeMembro::find($request->membro_id)->update(array(
			'nivel_id' => $request->nivel_id
		));

		$arrResponse['status'] = '200';	
		$arrResponse['id'] = $request->time_id;

		return $arrResponse;		
	}

	public function excluirMembro($id)
	{	
		$time = TimeMembro::find($id)->first();
		TimeMembro::find($id)->delete();
		

		$arrResponse['status'] = '200';	
		$arrResponse['id'] = $time->time_id;

		return $arrResponse;
	}

	function gerar_senha($tamanho, $maiusculas, $minusculas, $numeros, $simbolos)
	{
		$ma = "ABCDEFGHIJKLMNOPQRSTUVYXWZ"; 
		$mi = "abcdefghijklmnopqrstuvyxwz"; 
		$nu = "0123456789"; 
		$si = "!@#$%¨&*()_+="; 
		$senha = "";

		if ($maiusculas){        
			$senha .= str_shuffle($ma);
		}

		if ($minusculas){       
			$senha .= str_shuffle($mi);
		}

		if ($numeros){       
			$senha .= str_shuffle($nu);
		}

		if ($simbolos){       
			$senha .= str_shuffle($si);
		}

		return substr(str_shuffle($senha),0,$tamanho);
	}

}