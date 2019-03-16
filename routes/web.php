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
	$this->get('/tarefa/nova', 'TarefasController@novaTarefa')->name('nova-tarefa');
	$this->post('/tarefa/salvar', 'TarefasController@salvaTarefa')->name('salva-tarefa');
	$this->get('/tarefa/ver/{id}', 'TarefasController@verTarefa')->name('ver-tarefa');
	$this->post('/tarefa/editar', 'TarefasController@editarTarefa')->name('editar-tarefa');
	$this->get('/tarefa/excluir', 'TarefasController@excluirTarefa')->name('excluir-tarefa');
	$this->get('/tarefa/arquivar', 'TarefasController@arquivarTarefa')->name('arquivar-tarefa');
	$this->get('/tarefa/arquivadas', 'TarefasController@verTarefasArquivadas')->name('arquivadas-tarefa');
});


Route::get('/email',function(){
	\Illuminate\Support\Facades\Mail::to('fredrumond@gmail.com')->send(new \App\Mail\BemVindo());

});
