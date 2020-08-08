<?php

Route::get('/', function () {
	return view('welcome');
});

Route::get('/versao', 'versao\VersaoController@index')->name('versao');

Auth::routes();

Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');

//Route::get('/home', 'HomeController@index')->name('home');

$this->group(['middleware' => ['auth'], 'namespace' => 'Admin', 'prefix' => 'admin', 'as' => 'admin.'],function(){

	/*DASHBOARD*/
	$this->get('/dashboard', 'AdminController@index')->name('dashboard');
	$this->get('/atividades', 'AdminController@logs')->name('atividades');

	/*TAREAS*/
	$this->get('/tarefas', 'TarefasController@index')->name('tarefas');
	$this->get('/tarefa/nova', 'TarefasController@novaTarefa')->name('nova-tarefa');
	$this->post('/tarefa/salvar', 'TarefasController@salvaTarefa')->name('salva-tarefa');
	$this->get('/tarefa/ver/{id}', 'TarefasController@verTarefa')->name('ver-tarefa');
	$this->post('/tarefa/editar', 'TarefasController@editarTarefa')->name('editar-tarefa');
	$this->get('/tarefa/excluir', 'TarefasController@excluirTarefa')->name('excluir-tarefa');
	$this->get('/tarefa/recuperar', 'TarefasController@recuperarTarefa')->name('recuperar-tarefa');
	$this->get('/tarefa/arquivar', 'TarefasController@arquivarTarefa')->name('arquivar-tarefa');
	$this->get('/tarefa/arquivadas', 'TarefasController@verTarefasArquivadas')->name('arquivadas-tarefa');
	$this->get('/tarefa/relatorio', 'TarefasController@verTarefasRelatorio')->name('relatorio-tarefa');

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
	
	$this->get('/projeto/arquivos', 'ProjetosController@arquivos')->name('arquivos');
	$this->post('/projeto/arquivo/novo', 'ProjetosController@novoArquivoProjeto')->name('novo-arquivo-projeto');
	$this->get('/projeto/arquivo/excluir', 'ProjetosController@excluirArquivoProjeto')->name('excluir-arquivo-projeto');

	/*PROJETOS ANOTACOES*/
	$this->get('/projeto-anotacoes', 'ProjetosAnotacoesController@todasAnotacoes')->name('anotacoes');
	$this->post('/projeto-anotacao/salvar', 'ProjetosAnotacoesController@salvaAnotacao')->name('salva-anotacao');
	$this->get('/projeto-anotacao/excluir', 'ProjetosAnotacoesController@excluirAnotacao')->name('excluir-anotacao');
	$this->get('/projeto-anotacao/ver', 'ProjetosAnotacoesController@verAnotacao')->name('ver-anotacao');
	$this->post('/projeto-anotacao/editar', 'ProjetosAnotacoesController@editarAnotacao')->name('editar-anotacao');

	/*CLIENTES*/
	$this->get('/clientes', 'ClientesController@index')->name('clientes');
	$this->get('/cliente/novo', 'ClientesController@novoCliente')->name('novo-cliente');
	$this->post('/cliente/salvar', 'ClientesController@salvaCliente')->name('salva-cliente');
	$this->get('/cliente/ver/{id}', 'ClientesController@verCliente')->name('ver-cliente');
	$this->post('/cliente/editar', 'ClientesController@editarCliente')->name('editar-cliente');
	$this->get('/cliente/excluir', 'ClientesController@excluirCliente')->name('excluir-cliente');

	/*TIMES*/
	$this->get('/times', 'TimeController@index')->name('times');	
	$this->post('/time/salvar', 'TimeController@salvaTime')->name('salva-time');
	$this->get('/time/ver/{id}', 'TimeController@verTime')->name('ver-time');
	$this->post('/time/editar', 'TimeController@editarTime')->name('editar-time');
	$this->get('/time/excluir/{id}', 'TimeController@excluirTime')->name('excluir-time');

	/*TIME MEMBRO*/
	$this->post('/time-membro/novo', 'TimeMembroController@novoMembro')->name('novo-time-membro');
	$this->get('/time-membro/ver/{time}/{membro}', 'TimeMembroController@verMembro')->name('ver-time-membro');
	$this->post('/time-membro/editar', 'TimeMembroController@editarMembro')->name('editar-time-membro');
	$this->get('/time-membro/excluir/{id}', 'TimeMembroController@excluirMembro')->name('excluir-time-membro');

	/*USUARIO*/
	$this->get('/perfil', 'UsuarioController@perfil')->name('perfil');
	$this->post('/perfil/atualiza', 'UsuarioController@update')->name('perfil-atualizar');
});



//ACEITAR OU RECUSAR CONVITE

Route::get('/time-membro/aceitar/{id}', 'Admin\TimeMembroController@aceitarConvite')->name('aceitar-time-membro');
Route::get('/time-membro/recusar/{id}', 'Admin\TimeMembroController@recusarConvite')->name('recusar-time-membro');
Route::get('/email',function(){
	\Illuminate\Support\Facades\Mail::to('fredrumond@gmail.com')->send(new \App\Mail\BemVindo());

});
