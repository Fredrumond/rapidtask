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
				<h4>Nova tarefa</h4>
				<form id="form-tarefa">
					@csrf
					<div class="form-row">
						<div class="form-group col-md-6">
							<label for="inputEmail4">Titulo</label>
							<input type="text" class="form-control" name="titulo" id="titulo">
						</div>
						<div class="form-group col-md-2">
							<label for="inputState">Tipo</label>
							<select class="form-control" name="tipo_id" id="tipo_id">
								<option></option>
								<option value="1">1</option>								
							</select>
						</div>
						<div class="form-group col-md-2">
							<label for="inputState">Situação</label>
							<select class="form-control" name="situacao_id" id="situacao_id">
								<option value="1">1</option>
							</select>
						</div>
						<div class="form-group col-md-2">
							<label for="inputState">Prioridade</label>
							<select class="form-control" name="prioridade_id" id="prioridade_id">
								<option value="1">1</option>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label for="descricao">Descrição</label>
						<textarea class="form-control" id="descricao" rows="3" name="descricao" id="descricao"></textarea>
					</div>
					
					<div class="form-row">						
						<div class="form-group col-md-2">
							<label for="inputCity">Data Inicio</label>
							<input type="text" class="form-control" name="dt_inicio" id="dt_inicio">
						</div>
						<div class="form-group col-md-2">
							<label for="inputCity">Data Prevista</label>
							<input type="text" class="form-control" name="dt_prevista" id="dt_prevista">
						</div>
						<div class="form-group col-md-2">
							<label for="inputCity">Tempo Estimado</label>
							<input type="text" class="form-control" name="tempo_estimado" id="tempo_estimado">
						</div>
					</div>					
					<button type="submit" class="btn btn-primary">Criar</button>
				</form>
			</div>
			
		</div>
	</div>
	





	<script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
	<script src="{{ asset('alertfy/alertify.min.js') }}"></script>


	<script>

		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});

		$('#form-tarefa').submit( function(e) {
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
					url: '/admin/tarefa/salvar',
					type: 'POST',
					dataType: 'json',
					data: dados,
				})
				.done(function(response) {
					if (response.status == '200') {
						window.location.replace("/admin/tarefas");
					}
					console.log(response);
				})
				.fail(function() {
					console.log("error");
				})

			}

			




		});
		
		
	</script>
</body>
</html>