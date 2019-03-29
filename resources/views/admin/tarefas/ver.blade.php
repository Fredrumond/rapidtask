@extends('layouts.admin')
@section('content')
<div class="row">
	<div class="col-md-12">
		<h4>{{$tarefa->titulo}}</h4>
		<div class="box-btn">
			<button class="btn btn-info arquivar-tarefa">Arquivar</button>	
		</div>
	</div>	
</div>
<div class="row ">
	<div class="col-md-6 tarefas">
		
		<form id="form-atualiza-tarefa">
			@csrf
			<div class="form-row">
				<input type="hidden" name="tarefa_id" id="tarefa_id" value="{{$tarefa->id}}">
				<div class="form-group col-md-8">
					<label for="inputEmail4">Titulo</label>
					<input type="text" class="form-control" name="titulo" id="titulo" value="{{$tarefa->titulo}}">
				</div>
				<div class="form-group col-md-4">
					<label for="inputState">Projeto</label>
					<select class="form-control" name="projeto_id" id="projeto_id">								
						@foreach($projetos as $projeto)						
						<option value="{{ $projeto->id }}" {{$tarefa->projeto_id == $projeto->id ? 'selected="selected"' : '' }}>{{ $projeto->nome }}</option>
						@endforeach											
					</select>
				</div>
				<div class="form-group col-md-4">
					<label for="inputState">Tipo</label>
					<select class="form-control" name="tipo_id">								
						@foreach($tipos as $tipo)
						<option value="{{ $tipo->id }}" {{$tarefa->tipo_id == $tipo->id ? 'selected="selected"' : '' }}>{{ $tipo->nome }}</option>

						@endforeach								
					</select>
				</div>
				<div class="form-group col-md-4">
					<label for="inputState">Situação</label>
					<select class="form-control" name="situacao_id" id="situacao_id">
						@foreach($situacoes as $situacao)
						<option value="{{ $situacao->id }}" {{$tarefa->situacao_id == $situacao->id ? 'selected="selected"' : '' }}>{{ $situacao->nome }}</option>
						@endforeach	
					</select>
				</div>
				<div class="form-group col-md-4">
					<label for="inputState">Prioridade</label>
					<select class="form-control" name="prioridade_id" id="prioridade_id">
						@foreach($prioridades as $prioridade)
						<option value="{{ $prioridade->id }}" {{$tarefa->prioridade_id == $prioridade->id ? 'selected="selected"' : '' }}>{{ $prioridade->nome }}</option>

						@endforeach									
					</select>
				</div>
			</div>
			<div class="form-group">
				<label for="descricao">Descrição</label>
				<textarea class="form-control" id="descricao" rows="3" name="descricao" id="descricao">{{$tarefa->descricao}}</textarea>
			</div>

			<div class="form-row">						
				<div class="form-group col-md-4">
					<label for="inputCity">Data Inicio</label>
					<input type="date" class="form-control" name="dt_inicio" id="dt_inicio" value="{{$tarefa->dt_inicio}}">
				</div>
				<div class="form-group col-md-4">
					<label for="inputCity">Data Prevista</label>
					<input type="date" class="form-control" name="dt_prevista" id="dt_prevista" value="{{$tarefa->dt_prevista}}">
				</div>
				<div class="form-group col-md-4">
					<label for="inputCity">Data Conclusão</label>
					<input type="date" class="form-control" name="dt_fim" id="dt_fim" value="{{$tarefa->dt_fim}}">
				</div>
				<div class="form-group col-md-4">
					<label for="inputCity">Tempo Estimado</label>
					<input type="text" class="form-control" name="tempo_estimado" id="tempo_estimado" value="{{$tarefa->tempo_estimado}}">
				</div>
			</div>					
			<button type="submit" class="btn btn-primary">Salvar</button>
		</form>
	</div>
	<div class="col-md-6">
		<ul class="nav nav-tabs">
			<li class="nav-item">
				<a class="nav-link active" data-toggle="tab" href="#comentarios">Comentarios</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" data-toggle="tab" href="#historico">Historico</a>
			</li>

		</ul>

		<div class="tab-content">
			<div id="comentarios" class="tab-pane active">
				<div class="box-btn">
					<button class="btn btn-success novo-comentario">Novo</button>	
				</div>
				<div class="box-comentario">
					<form id="form-comentario">
						@csrf
						<label>Novo comentario</label>
						<input type="hidden" name="tarefaId" id="tarefaId" value="{{$tarefa->id}}">
						<textarea class="form-control" rows="3" name="comentario" id="comentario"></textarea>
						<button type="submit" class="btn btn-primary">Enviar</button>
						<button type ="button" class="btn btn-primary cancelar-comentario">Cancelar</button>
					</form>
				</div>
				<ul class="timeline-comentarios">					
				</ul>
			</div>
			<div id="historico" class="tab-pane fade">
				<h1>Historico</h1>

			</div>			
		</div>
	</div>

</div>

<div class="modal" id="editarComentarioModal" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Editar comentario</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<p>Modal body text goes here.</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
				<button type="button" class="btn btn-primary">Salvar</button>
			</div>
		</div>
	</div>
</div>
@endsection
@section('script')
<script>
	$(document).ready(function() {

		function retornaComentarios(){
			let tarefa_id = $('#tarefaId').val();
			$.ajax({
				url: '/admin/tarefa-comentarios',
				type: 'GET',
				dataType: 'json',
				data: {'tarefa_id' : tarefa_id}
			})
			.done(function(response) {
				console.log(response)
				var comentarios = "";
				$.each( response.comentarios, function( key, value ) {						
						comentarios += '<li><a href="#">'+value.nome+'</a><a href="#" class="float-right">'+value.data+'</a><p>'+value.comentario+'</p><div class="acoes-comentario"><i class="fas fa-edit editar-comentario" data-id="'+value.id+'"></i><i class="fas fa-trash remover-comentario" data-id="'+value.id+'"></i></div></li>';

					
				});
				$('.timeline-comentarios').html(comentarios);
				console.log(comentarios)
			})
			.fail(function(error) {
				console.log("error");
			})
		}

		$('.box-comentario').hide();

		$('.novo-comentario').click(function(event) {
			$('.box-comentario').show();
			$('.novo-comentario').hide();			
		});

		$('.cancelar-comentario').click(function(event) {
			$('.box-comentario').hide();
			$('.novo-comentario').show();
		});


		$('#form-atualiza-tarefa').submit( function(e) {
			e.preventDefault();         

			let form = $(this);
			let dados = form.serialize()
			console.log(dados)
			alertify.set('notifier','position', 'top-right');


			if ($('#titulo').val() == '') {
				alertify.warning('Preencha o titulo!'); 
			}

			if ($('#tipo_id').val() == '') {
				alertify.warning('Preencha o tipo!'); 
			}

			if ($('#situacao_id').val() == '') {
				alertify.warning('Preencha a situação!'); 
			}

			if ($('#prioridade_id').val() == '') {
				alertify.warning('Preencha a prioridade!'); 
			}

			if ($('#titulo').val() != '' && $('#tipo_id').val() != '' && $('#situacao_id').val() != '' && $('#prioridade_id').val() != '' ) {
				$.ajax({
					url: ' /admin/tarefa/editar',
					type: 'POST',
					dataType: 'json',
					data: dados,
				})
				.done(function(response) {
					if (response.status == '200') {
						window.location.replace("/admin/tarefas");
					}
				})
				.fail(function(error) {
					console.log("error");
				})

			}
		});

		$('.arquivar-tarefa').click(function(e) {
			e.preventDefault();

			let tarefaId = $('#tarefa_id').val();       
			alertify.confirm('Deseja realmente arquivar a tarefa?').set('onok', function(closeEvent){
				$.ajax({
					url: ' /admin/tarefa/arquivar',
					type: 'GET',
					dataType: 'json',
					data: {'tarefaId':tarefaId},
				})
				.done(function(response) {
					if (response.status == '200') {
						window.location.replace("/admin/tarefas");
					}
				})
				.fail(function(error) {
					console.log("error");
				});

			});
		});

		$('#form-comentario').submit( function(e) {
			e.preventDefault();         

			let form = $(this);
			let dados = form.serialize()
			console.log(dados)
			alertify.set('notifier','position', 'top-right');

			if($("#comentario").val().trim().length < 1){
				alertify.warning('Preencha o comentario!'); 
			}			

			if ($("#comentario").val().trim().length > 1) {
				$.ajax({
					url: ' /admin/tarefa-comentario/salvar',
					type: 'POST',
					dataType: 'json',
					data: dados,
				})
				.done(function(response) {
					if (response.status == '200') {						
						let id = $('#tarefaId').val();
						console.log(id)
						//window.location = '/admin/tarefa/ver/'+id;
						retornaComentarios();
					}
				})
				.fail(function(error) {
					console.log("error");
				})

			}
		});

		retornaComentarios();



		$('.teste').click(function(event) {
			/* Act on the event */
			e.preventDefault();     
			console.log(	'')
		});

		$(document).on('click', '.editar-comentario', function(){ 
			
			let id = $(this).data("id");
			$('#editarComentarioModal').modal('show');
			console.log("OI EDIÇÃO")
			console.log(id)
		}); 

		$(document).on('click', '.remover-comentario', function(){ 
			
			let id = $(this).data("id");

			alertify.confirm('Deseja realmente excluir a tarefa?').set('onok', function(closeEvent){
				$.ajax({
					url: ' /admin/tarefa/excluir',
					type: 'GET',
					dataType: 'json',
					data: {'tarefaId':tarefaId},
				})
				.done(function(response) {
					if (response.status == '200') {
						window.location.replace("/admin/tarefa/arquivadas");
					}
				})
				.fail(function(error) {
					console.log("error");
				});

			});
			
			console.log("OI REMOÃÇÃO")
			console.log(id)
		}); 

		


	});
</script>
@endsection