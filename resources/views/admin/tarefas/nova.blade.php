<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
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
				<form>
					<div class="form-row">
						<div class="form-group col-md-6">
							<label for="inputEmail4">Titulo</label>
							<input type="text" class="form-control">
						</div>
						<div class="form-group col-md-2">
							<label for="inputState">Tipo</label>
							<select id="inputState" class="form-control">
								<option selected>Choose...</option>
								<option>...</option>
							</select>
						</div>
						<div class="form-group col-md-2">
							<label for="inputState">Situação</label>
							<select id="inputState" class="form-control">
								<option selected>Choose...</option>
								<option>...</option>
							</select>
						</div>
						<div class="form-group col-md-2">
							<label for="inputState">Prioridade</label>
							<select id="inputState" class="form-control">
								<option selected>Choose...</option>
								<option>...</option>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label for="exampleFormControlTextarea1">Descrição</label>
						<textarea class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
					</div>
					
					<div class="form-row">						
						<div class="form-group col-md-2">
							<label for="inputCity">Data Inicio</label>
							<input type="text" class="form-control" id="inputCity">
						</div>
						<div class="form-group col-md-2">
							<label for="inputCity">Data Prevista</label>
							<input type="text" class="form-control" id="inputCity">
						</div>
						<div class="form-group col-md-2">
							<label for="inputCity">Tempo Estimado</label>
							<input type="text" class="form-control" id="inputCity">
						</div>
					</div>					
					<button type="submit" class="btn btn-primary">Criar</button>
				</form>
			</div>
			
		</div>
	</div>
	





	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>
</html>