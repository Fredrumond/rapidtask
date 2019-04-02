@extends('layouts.admin')
@section('content')
<div class="row ">
	<div class="col-12 tarefas">
		<h4>Tarefas Arquivadas</h4>						
		<div class="board-tarefas">
			<table class="table">
				<thead class="text-center">
					<tr>
						<th scope="col">Ref</th>
						<th scope="col">Tipo</th>
						<th scope="col">Situação</th>
						<th scope="col">Prioridade</th>
						<th scope="col">Titulo</th>								
						<th scope="col">Atualização</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($tarefas as $tarefa)
					<tr class="text-center tarefa-arquivada">
						<th scope="row">{{$tarefa->id}}</th>
						<td>{{$tarefa->tipo->nome}}</td>
						<td>
							<?php 
							if ($tarefa->situacao_id == 1) {
								echo '<span class="badge badge-primary">Nova</span>';
							}
							if ($tarefa->situacao_id == 2) {
								echo '<span class="badge badge-light">Andamento </span>';
							}
							if ($tarefa->situacao_id == 3) {
								echo '<span class="badge badge-warning">Em espera</span>';
							}
							if ($tarefa->situacao_id == 4) {
								echo '<span class="badge badge-success">Finalizado </span>';
							}
							?>									
						</td>
						<td>
							<?php 
							if ($tarefa->prioridade_id == 1) {
								echo '<span class="badge badge-info">Baixo</span>';
							}
							if ($tarefa->situacao_id == 2) {
								echo '<span class="badge badge-secondary">Normal</span>';
							}
							if ($tarefa->situacao_id == 3) {
								echo '<span class="badge badge-warning">Urgente</span>';
							}
							if ($tarefa->situacao_id == 4) {
								echo '<span class="badge badge-danger">Imediato</span>';
							}
							?>
						</td>
						<td>{{$tarefa->titulo}}</td>								
						<td>{{$tarefa->updated_at}}</td>
						<td><button class="btn btn-danger excluir-tarefa" data-id="{{$tarefa->id}}">Excluir</button></td>								
					</tr>
					@endforeach


				</tbody>
			</table>
		</div>
	</div>

</div>
@endsection
@section('script')
<script>
	$(document).ready(function() {
		$('.excluir-tarefa').click(function(e) {
			e.preventDefault();

			let tarefaId = $(this).data('id')       
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
		});
	});
</script>
@endsection