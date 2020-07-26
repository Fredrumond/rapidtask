@extends('layouts.admin')
@section('content')

@php
	$newCustomer = new StdClass;
	$newCustomer->class = 'success';
	$newCustomer->route = 'admin.novo-cliente';
	$newCustomer->title = 'Novo Cliente';
@endphp

<div class="row ">
	<div class="col-12 clientes">
		@panelHeader([
			'title' => 'Clientes',
			'actions' => [$newCustomer]
			])
		@endpanelHeader
		<div class="board-clientes">
			<table class="table">
				<thead class="text-center">
					<tr>
						<th scope="col">Ref</th>
						<th scope="col">Nome</th>
						<th scope="col">Email</th>
						<th scope="col">Telefone</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($clientes as $cliente)
					<tr class="text-center ver-detalhes-cliente" data-id="{{$cliente->id}}">
						<th scope="row">{{$cliente->id}}</th>
						<td>{{$cliente->nome}}</td>
						<td>{{$cliente->email}}</td>								
						<td>{{$cliente->telefone}}</td>								
					</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>
</div>
@endsection
@section('script')
<script type="text/javascript" src="{{ asset('js/cliente-main.js') }}"></script>
@endsection