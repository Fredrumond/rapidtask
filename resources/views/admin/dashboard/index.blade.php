@extends('layouts.admin')
@section('content')

<div class="page-title-box">
	<div class="row align-items-center">
		<div class="col-sm-6">
			<h4 class="page-title">Dashboard</h4>
			<ol class="breadcrumb">
				<li class="breadcrumb-item active">Bem vindo ao RapidTask</li>
			</ol>
		</div>                        
	</div>
</div>

<div class="row">
	<div class="col-xl-3 col-md-6">
		<div class="card mini-stat bg-primary text-white">
			<div class="card-body">
				<div class="mb-4">
					<div class="float-left mini-stat-img mr-4"><img src="assets/images/services-icon/01.png" alt=""></div>
					<h5 class="font-16 text-uppercase mt-0 text-white-50">Tarefas</h5>
					<h4 class="font-500">{{$tarefasTotal[0]->total}}<i class="mdi mdi-arrow-up text-success ml-2"></i></h4>                    
				</div>
				<div class="pt-2">
					<div class="float-right"><a href="{{ route('admin.tarefas') }}" class="text-white-50"><i class="far fa-arrow-alt-circle-right"></i></a></div>
					<p class="text-white-50 mb-0">Desde o último mês</p>
				</div>
			</div>
		</div>
	</div>
	<div class="col-xl-3 col-md-6">
		<div class="card mini-stat bg-info text-white">
			<div class="card-body">
				<div class="mb-4">
					<div class="float-left mini-stat-img mr-4"><img src="assets/images/services-icon/01.png" alt=""></div>
					<h5 class="font-16 text-uppercase mt-0 text-white-50">Projetos</h5>
					<h4 class="font-500">{{$projetosTotal[0]->total}}<i class="mdi mdi-arrow-up text-success ml-2"></i></h4>                    
				</div>
				<div class="pt-2">
					<div class="float-right"><a href="{{ route('admin.projetos') }}" class="text-white-50"><i class="far fa-arrow-alt-circle-right"></i></a></div>
					<p class="text-white-50 mb-0">Desde o último mês</p>
				</div>
			</div>
		</div>
	</div>   
	<div class="col-xl-3 col-md-6">
		<div class="card mini-stat bg-warning text-white">
			<div class="card-body">
				<div class="mb-4">
					<div class="float-left mini-stat-img mr-4"><img src="assets/images/services-icon/01.png" alt=""></div>
					<h5 class="font-16 text-uppercase mt-0 text-white-50">Clientes</h5>
					<h4 class="font-500">{{$clientesTotal[0]->total}}<i class="mdi mdi-arrow-up text-success ml-2"></i></h4>                    
				</div>
				<div class="pt-2">
					<div class="float-right"><a href="{{ route('admin.clientes') }}" class="text-white-50"><i class="far fa-arrow-alt-circle-right"></i></a></div>
					<p class="text-white-50 mb-0">Desde o último mês</p>
				</div>
			</div>
		</div>
	</div>   
	<div class="col-xl-3 col-md-6">
		<div class="card mini-stat bg-success text-white">
			<div class="card-body">
				<div class="mb-4">
					<div class="float-left mini-stat-img mr-4"><img src="assets/images/services-icon/01.png" alt=""></div>
					<h5 class="font-16 text-uppercase mt-0 text-white-50">Orçamento</h5>
					<h4 class="font-500">1,685 <i class="mdi mdi-arrow-up text-success ml-2"></i></h4>                    
				</div>
				<div class="pt-2">
					<div class="float-right"><a href="#" class="text-white-50"><i class="mdi mdi-arrow-right h5"></i></a></div>
					<p class="text-white-50 mb-0">Desde o último mês</p>
				</div>
			</div>
		</div>
	</div>       
</div>

<div class="row">
	<div class="col-xl-8">
		<div class="card">
			<div class="card-body">
				<h4 class="mt-0 header-title mb-4">Tarefas</h4>
				<div class="table-responsive">
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
							<tr class="text-center cursor ver-detalhes-tarefa" data-id="{{$tarefa->id}}">
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
	</div>   
	<div class="col-xl-4">
		<div class="card">
			<div class="card-body">
				<h4 class="mt-0 header-title mb-4">Atividades</h4>
				<ol class="activity-feed">
					@foreach ($logs as $log)
					<li class="feed-item">
						<div class="feed-item-list">
							<span class="date">{{$log->data}}</span> 
							<span class="activity-text">{{$log->acao}} um(a) {{$log->tipo}}</span>
						</div>
					</li>
					@endforeach
					
				</ol>
				<div class="text-center"><a href="{{ route('admin.atividades') }}" class="btn btn-primary">Ver mais</a></div>
			</div>
		</div>
	</div>    
</div>

@endsection
@section('script')
<script type="text/javascript">
	$(document).ready(function() {
		$(".ver-detalhes-tarefa").click(function() {
			console.log("oi")
			let id = $(this).data("id");

			window.location = '/admin/tarefa/ver/'+id;
		});
	});
</script>
@endsection
