@extends('layouts.admin')
@section('content')
<div class="row ">
	<div class="col-12 tarefas">
		<h4>Relatorio Tarefas</h4>
		<div class="box-btn">
			<a class="btn btn-success" href="#">Botao</a>			
		</div>		
		<div class="board-tarefas">
			<table class="table">
				<thead class="text-center">
					<tr>
						<th scope="col"></th>
						<th scope="col">Novo</th>
						<th scope="col">Andamento</th>
						<th scope="col">Espera</th>
						<th scope="col">Finalizado</th>								
						<th scope="col">Baixa</th>
						<th scope="col">Normal</th>
						<th scope="col">Urgente</th>
						<th scope="col">Imediato</th>
					</tr>
				</thead>
				<tbody>	
					@foreach ($relatorio as $r)				
					<tr class="text-center">						
						<td>{{$r->TIPO}}</td>										
						<td>{{$r->NOVO}}</td>										
						<td>{{$r->ANDAMENTO}}</td>										
						<td>{{$r->ESPERA}}</td>										
						<td>{{$r->FINALIZADO}}</td>										
						<td>{{$r->BAIXA}}</td>										
						<td>{{$r->NORMAL}}</td>										
						<td>{{$r->URGENTE}}</td>										
						<td>{{$r->IMEDIATO}}</td>										
					</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>
</div>
@endsection
