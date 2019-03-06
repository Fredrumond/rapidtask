<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
	return view('welcome');
});

Auth::routes();

//Route::get('/home', 'HomeController@index')->name('home');

$this->group(['middleware' => ['auth'], 'namespace' => 'Admin', 'prefix' => 'admin', 'as' => 'admin.'],function(){

	/*DASHBOARD*/
	$this->get('/dashboard', 'AdminController@index')->name('dashboard');

	/*TAREAS*/
	$this->get('/tarefas', 'TarefasController@index')->name('tarefas');
});


Route::get('/email',function(){
	\Illuminate\Support\Facades\Mail::to('fredrumond@gmail.com')->send(new \App\Mail\BemVindo());

});
