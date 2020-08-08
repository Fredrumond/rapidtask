<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Admin\UserRequest;
use File;

class UsuarioController extends Controller
{

	
	public function perfil()
	{		

		$user = User::find(Auth::user()->id);

		return view('admin.usuario.ver',compact('user'));
	}

	public function update(UserRequest $request)
	{
		$user = User::find($request->usuario_id)->first();
		
		if($user){
			$user->name = $request->name;
			$user->save();

			if($request->file('avatar')){
				$nomeAvatar = md5($request->file('avatar')->getClientOriginalName());        
				$path = base_path() . '/public/avatar';
				$fileName = $nomeAvatar.'.'.$request->file('avatar')->getClientOriginalExtension();        
				$file = $request->file('avatar')->move($path,$fileName);

				if($user->avatar){
					File::delete('avatar/'.$user->avatar);
					$user->avatar =  $fileName;
					$user->save();  
				} else {
					$user->avatar =  $fileName;
					$user->save();
				}
			}

			if($request->senha){
				if($request->senha == $request->repita_senha){
					$user->password = Hash::make($request->senha);
					$user->save();
				}
			}

			return view('admin.usuario.ver',compact('user'));
		}
	}
}
