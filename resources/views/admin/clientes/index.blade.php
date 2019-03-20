@extends('layouts.admin')
@section('content')
<div class="row ">
	<div class="col-12 clientes">
		<h4>Clientes</h4>
		<div class="box-btn">
			<a class="btn btn-success" href="{{ route('admin.novo-cliente') }}">Novo Cliente</a>
		</div>

		<div class="board-clientes">
			<table class="table">
				<thead class="text-center">
					<tr>
						<th scope="col">Ref</th>
						<th scope="col">Nome</th>
						<th scope="col">Email</th>
						<th scope="col">Telefone</th
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

    $(document).ready(function() {
         $(".ver-detalhes-cliente").click(function() {
                let id = $(this).data("id");
                
                window.location = '/admin/cliente/ver/'+id;
            });
    });

@endsection