@extends('layouts.admin')
@section('content')
<div class="row ">
	<div class="col-12 projetos">
		<h4>Projetos</h4>
		<div class="box-btn">
			<a class="btn btn-success" href="{{ route('admin.novo-projeto') }}">Novo Projeto</a>
		</div>
		<div class="board-projetos">
			<table class="table">
				<thead class="text-center">
					<tr>
						<th scope="col">Ref</th>
						<th scope="col">Nome</th>
						<th scope="col">Cliente</th>						
					</tr>
				</thead>
				<tbody>
					@foreach ($projetos as $projeto)
					<tr class="text-center ver-detalhes-projeto" data-id="{{$projeto->id}}">
						<th scope="row">{{$projeto->id}}</th>
						<td>{{$projeto->nome}}</td>
						<td>{{$projeto->cliente}}</td>
					</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>
</div>
@endsection
@section('script')
<script type="text/javascript" src="{{ asset('js/projeto-main.js') }}"></script>
@endsection