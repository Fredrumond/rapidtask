<?php

namespace App\Http\Controllers\Versao;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class VersaoController extends Controller
{
	public function index(){		
		return view('versao.index');
	}
	
}