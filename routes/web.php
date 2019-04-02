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

Route::get('/versao', 'versao\VersaoController@index')->name('versao');

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

	/*TAREFA COMENTARIO*/
	$this->get('/tarefa-comentarios', 'TarefaComentarioController@todosComentarios')->name('comentarios');
	$this->post('/tarefa-comentario/salvar', 'TarefaComentarioController@salvaComentario')->name('salva-comentario');
	$this->get('/tarefa-comentario/excluir', 'TarefaComentarioController@excluirComentario')->name('excluir-comentario');
	$this->get('/tarefa-comentario/ver', 'TarefaComentarioController@verComentario')->name('ver-comentario');
	$this->post('/tarefa-comentario/editar', 'TarefaComentarioController@editarComentario')->name('editar-comentario');

	/*TAREFA HISTORICO*/
	$this->get('/tarefa-historico', 'TarefaHistoricoController@todoHistorico')->name('historico');


	/*PROJETOS*/
	$this->get('/projetos', 'ProjetosController@index')->name('projetos');
	$this->get('/projeto/nova', 'ProjetosController@novoProjeto')->name('novo-projeto');
	$this->post('/projeto/salvar', 'ProjetosController@salvaProjeto')->name('salva-projeto');
	$this->get('/projeto/ver/{id}', 'ProjetosController@verProjeto')->name('ver-projeto');
	$this->get('/projeto/detalhe/{id}', 'ProjetosController@detalheProjeto')->name('detalhe-projeto');
	$this->post('/projeto/editar', 'ProjetosController@editarProjeto')->name('editar-projeto');
	$this->get('/projeto/excluir', 'ProjetosController@excluirProjeto')->name('excluir-projeto');
	

	/*CLIENTES*/
	$this->get('/clientes', 'ClientesController@index')->name('clientes');
	$this->get('/cliente/novo', 'ClientesController@novoCliente')->name('novo-cliente');
	$this->post('/cliente/salvar', 'ClientesController@salvaCliente')->name('salva-cliente');
	$this->get('/cliente/ver/{id}', 'ClientesController@verCliente')->name('ver-cliente');
	$this->post('/cliente/editar', 'ClientesController@editarCliente')->name('editar-cliente');
	$this->get('/cliente/excluir', 'ClientesController@excluirCliente')->name('excluir-cliente');

});


Route::get('/email',function(){
	\Illuminate\Support\Facades\Mail::to('fredrumond@gmail.com')->send(new \App\Mail\BemVindo());

});
