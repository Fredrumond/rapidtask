<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UploadController extends Controller
{

	public function uploadData($data){

		$arquivoExtensao = $data['arquivo']->getClientOriginalExtension();
		$arquivoTamanho = $data['arquivo']->getSize();

		$verificaTipo = $this->verificaTipoArquivo($arquivoExtensao,$data['arquivo_permitido']);

		if($verificaTipo){

			$nomeArquivo = md5($data['arquivo']->getClientOriginalName() . date('H:m:s'));
			$path = base_path() . $data['caminho'];
			$fileName = $nomeArquivo.'.'.$arquivoExtensao;        
			$file = $data['arquivo']->move($path,$fileName);

			return $fileName;
		}

		return response()->json(['msg' => 'Tipo de arquivo não permitido! Permitido apenas: ' . $data['arquivo_permitido']], 400);		
		
	}


	private function verificaTipoArquivo($tipo,$permitido)
	{

		if($tipo == $permitido)
			return true;

		return false;
	}
	
}
