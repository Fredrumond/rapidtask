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
					<a href=""><li><i class="far fa-calendar-check"></i>Tarefas</li></a>
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
								<th scope="col">Autor</th>
								<th scope="col">Atualização</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<th scope="row">1</th>
								<td>Funcionalidade</td>
								<td><span class="badge badge-primary">Nova</span></td>
								<td><span class="badge badge-danger">Alta</span></td>
								<td>Fazer retorno API</td>
								<td>Fredrumond</td>
								<td>20/02/2019 23:08:46</td>
							</tr>
							<tr>
								<th scope="row">1</th>
								<td>Funcionalidade</td>
								<td><span class="badge badge-secondary">Nova</span></td>
								<td><span class="badge badge-danger">Alta</span></td>
								<td>Fazer retorno API</td>
								<td>Fredrumond</td>
								<td>20/02/2019 23:08:46</td>
							</tr>	
							<tr>
								<th scope="row">1</th>
								<td>Funcionalidade</td>
								<td><span class="badge badge-success">Nova</span></td>
								<td><span class="badge badge-danger">Alta</span></td>
								<td>Fazer retorno API</td>
								<td>Fredrumond</td>
								<td>20/02/2019 23:08:46</td>
							</tr>	
							<tr>
								<th scope="row">1</th>
								<td>Funcionalidade</td>
								<td><span class="badge badge-danger">Nova</span></td>
								<td><span class="badge badge-danger">Alta</span></td>
								<td>Fazer retorno API</td>
								<td>Fredrumond</td>
								<td>20/02/2019 23:08:46</td>
							</tr>	
							<tr>
								<th scope="row">1</th>
								<td>Funcionalidade</td>
								<td><span class="badge badge-warning">Nova</span></td>
								<td><span class="badge badge-danger">Alta</span></td>
								<td>Fazer retorno API</td>
								<td>Fredrumond</td>
								<td>20/02/2019 23:08:46</td>
							</tr>	
							<tr>
								<th scope="row">1</th>
								<td>Funcionalidade</td>
								<td><span class="badge badge-info">Nova</span></td>
								<td><span class="badge badge-danger">Alta</span></td>
								<td>Fazer retorno API</td>
								<td>Fredrumond</td>
								<td>20/02/2019 23:08:46</td>
							</tr>	
							<tr>
								<th scope="row">1</th>
								<td>Funcionalidade</td>
								<td><span class="badge badge-light">Nova</span></td>
								<td><span class="badge badge-danger">Alta</span></td>
								<td>Fazer retorno API</td>
								<td>Fredrumond</td>
								<td>20/02/2019 23:08:46</td>
							</tr>
							<tr>
								<th scope="row">1</th>
								<td>Funcionalidade</td>
								<td><span class="badge badge-dark">Nova</span></td>
								<td><span class="badge badge-danger">Alta</span></td>
								<td>Fazer retorno API</td>
								<td>Fredrumond</td>
								<td>20/02/2019 23:08:46</td>
							</tr>							
							
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
	</div>

	<!-- <div class="rodape">
		<div class="text-center">Todos os direitos reservados</div>
	</div> -->
	





	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>
</html>