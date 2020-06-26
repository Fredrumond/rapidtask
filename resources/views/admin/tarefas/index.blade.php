@extends('layouts.admin')
@section('content')
<div class="row ">
	<div class="col-12 tarefas">
		<h4>Tarefas</h4>
		<div class="box-btn">
			<a class="btn btn-success" href="{{ route('admin.nova-tarefa') }}">Nova Tarefa</a>
			<a class="btn btn-info" href="{{ route('admin.arquivadas-tarefa') }}">Arquivadas</a></td>
			<a class="btn btn-secondary" href="{{ route('admin.relatorio-tarefa') }}">Relatorio</a></td>
		</div>

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
					
					<tr class="text-center ver-detalhes-tarefa" data-id="{{$tarefa->id}}">
						<th scope="row">#{{$tarefa->sigla}}-{{$tarefa->id}}</th>
						<td>{{$tarefa->tipo}}</td>
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
							if ($tarefa->prioridade_id == 2) {
								echo '<span class="badge badge-secondary">Normal</span>';
							}
							if ($tarefa->prioridade_id == 3) {
								echo '<span class="badge badge-warning">Urgente</span>';
							}
							if ($tarefa->prioridade_id == 4) {
								echo '<span class="badge badge-danger">Imediato</span>';
							}
							?>
						</td>
						<td>{{$tarefa->titulo}}</td>								
						<td>{{$tarefa->updated_at}}</td>								
					</tr>
					@endforeach


				</tbody>
			</table>
		</div>
	</div>

</div>
@endsection
@section('script')
<script type="text/javascript" src="{{ asset('js/tarefa-main.js') }}"></script>
@endsection