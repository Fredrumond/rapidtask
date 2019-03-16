<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
	<!-- include the style -->
	<link rel="stylesheet" href="{{ asset('alertfy/css/alertify.min.css') }}" />

	<!-- include a theme -->
	<link rel="stylesheet" href="{{ asset('alertfy/css/themes/default.min.css') }}" />
	<link rel="stylesheet" href="/css/style.css">
</head>
<body>

	
	<div class="menu-topo">
		<div class="row">
			<div class="col-6 navegacao">
				<ul>
					<a href=""><li><i class="fas fa-home"></i>Home</li></a>
					<a href=""><li><i class="fas fa-folder-open"></i>Projetos</li></a>
					<a href="{{ route('admin.tarefas') }}"><li><i class="far fa-calendar-check"></i>Tarefas</li></a>
					<a href=""><li><i class="fas fa-comment-dollar"></i>Orçamentos</li></a>
					<a href=""><li><i class="far fa-file-alt"></i>Blog</li></a>
				</ul>
			</div>	
			<div class="col-6 navegacao-ususario text-right">
				<ul>
					<li>Frederico Drumond</li>
					<li><i class="fas fa-bell"></i></li>
					<li><i class="fas fa-cog"></i></li>
					<li><i class="fas fa-sign-out-alt"></i></li>
				</ul>
			</div>
		</div>		
	</div>
	<!-- <div class="menu-sub">
		<h2>Nome do projeto</h2>
	</div> -->

	<div class="container-fluid">
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
</div>

	<!-- <div class="rodape">
		<div class="text-center">Todos os direitos reservados</div>
	</div> -->


	<script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
	<script src="{{ asset('alertfy/alertify.min.js') }}"></script>


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
</body>
</html>