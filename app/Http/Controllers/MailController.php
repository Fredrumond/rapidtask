<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\SendMail;
use Illuminate\Support\Facades\Mail;
use Log;

class MailController extends Controller
{

	public function sendMail($data)
	{
		try{
			Mail::send(
				$data['view'], 
				$data, 
				function($message) use ($data){
					$message->from('contato@rapidtask.com.br', $data['senderName']);
					$message->to($data['to'], $data['receiverName']);
					$message->subject($data['subject']);					
					if(isset($data['copy'])){
						$message->cc($data['copy']);
					}
				}
			);

			Log::info("E-mail enviado para: " . $data['to']);

			return response()->json(['success' => true , 'error' => ''], 200);

		} catch(\Exception $e){

			Log::warning("Verifique o metodo sendMail: " . $e->getMessage());

			return response()->json(['success' => '' , 'error' => 'true'], 500);
		}
	}


	public function emailConviteTime($data)
	{
		return $this->sendMail($data);		
	}	

	public function emailBoasVindas($data)
	{
		return $this->sendMail($data);		
	}
}
