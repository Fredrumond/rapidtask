<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;



class UsuarioController extends Controller
{

	
	public function perfil()
	{		

		$perfil = User::find(Auth::user()->id);

		return view('admin.usuario.ver',compact('perfil'));
	}

	public function salvarPerfil(Request $request)
	{
		
		$perfil = User::find($request->usuario_id)->update(array(
			'name' => $request->name,
		));		


		$perfil = User::find(Auth::user()->id);

		return view('admin.usuario.ver',compact('perfil'));
	}

	public function salvarAvatar(Request $request)
	{
		
		$nomeAvatar = md5($request->file('avatar')->getClientOriginalName());        
		$path = base_path() . '/public/avatar';
		$fileName = $nomeAvatar.'.'.$request->file('avatar')->getClientOriginalExtension();        
		$file = $request->file('avatar')->move($path,$fileName);

		$perfil = User::find(Auth::user()->id);
		$perfil->avatar =  $fileName;
		$perfil->save();  

		$perfil = User::find(Auth::user()->id);

		return view('admin.usuario.ver',compact('perfil'));

	}

	public function salvarSenha(Request $request)
	{
		if($request->senha == $request->repita_senha){

			$perfil = User::find($request->usuario_id)->update(array(
				'password' =>  Hash::make($request->senha)
			));

			$perfil = User::find(Auth::user()->id);

			return view('admin.usuario.ver',compact('perfil'));
		}
		
		$perfil = User::find(Auth::user()->id);

		return view('admin.usuario.ver',compact('perfil'));
	}

	
	
}