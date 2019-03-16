<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Tarefas;

class AdminController extends Controller
{
	public function index(){
		$tarefas = Tarefas::where('status',0)->get();
		return view('admin.dashboard.index',compact('tarefas'));
	}
}