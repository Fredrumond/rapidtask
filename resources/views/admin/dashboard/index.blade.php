@extends('layouts.admin')
@section('content')
<div class="row ">
	<div class="col-8 tarefas">
		<h4>Tarefas</h4>
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
					</tr>
					@endforeach


				</tbody>
			</table>
		</div>
	</div>
	<div class="col-4 atualizacoes">
		<h4 class="text-center">Atualizações</h4>
		<ul class="timeline">
			<li>
				<a target="_blank" href="https://www.totoprayogo.com/#">New Web Design</a>
				<a href="#" class="float-right">21 Fev, 2019</a>
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque scelerisque diam non nisi semper, et elementum lorem ornare. Maecenas placerat facilisis mollis. Duis sagittis ligula in sodales vehicula....</p>
			</li>
			<li>
				<a href="#">21 000 Job Seekers</a>
				<a href="#" class="float-right">21 Fev, 2019</a>
				<p>Curabitur purus sem, malesuada eu luctus eget, suscipit sed turpis. Nam pellentesque felis vitae justo accumsan, sed semper nisi sollicitudin...</p>
			</li>
			<li>
				<a href="#">Awesome Employers</a>
				<a href="#" class="float-right">21 Fev, 2019</a>
				<p>Fusce ullamcorper ligula sit amet quam accumsan aliquet. Sed nulla odio, tincidunt vitae nunc vitae, mollis pharetra velit. Sed nec tempor nibh...</p>
			</li>
			<li>
				<a href="#">Awesome Employers</a>
				<a href="#" class="float-right">21 Fev, 2019</a>
				<p>Fusce ullamcorper ligula sit amet quam accumsan aliquet. Sed nulla odio, tincidunt vitae nunc vitae, mollis pharetra velit. Sed nec tempor nibh...</p>
			</li>					
		</ul>
	</div>
</div>
@endsection
