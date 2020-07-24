@extends('layouts.admin')
@section('content')
<div class="row ">
	<div class="col-12 times">
		<div class="panel-header">
			<div class="panel-title">
				<h4>Times</h4>
			</div>
			<div class="panel-action">
				<button class="btn btn-success adicionar-novo-time">Novo Time</button>
			</div>
		</div>
		<div class="board-times">
			<table class="table">
				<thead class="text-center">
					<tr>
						<th scope="col">Ref</th>
						<th scope="col">Nome</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($times as $time)
					<tr class="text-center ver-detalhes-time" data-id="{{$time->id}}">
						<th scope="row">{{$time->id}}</th>
						<td>{{$time->nome}}</td>											
					</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>
</div>
<div class="modal" id="adicionarNovoTimeModal" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Novo Time</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form id="form-novo-time">					                  
					@csrf
					<div class="form-row">
						<div class="form-group col-md-12">
							<label for="nome">Nome</label>
							<input type="text" class="form-control" name="nome" id="nome">
						</div>								
					</div>
					<button type="submit" class="btn btn-primary">Enviar</button>
					<button type ="button" class="btn btn-primary cancelar-novo-time">Cancelar</button>
				</form>
			</div>

		</div>
	</div>
</div>
@endsection
@section('script')
<script type="text/javascript" src="{{ asset('js/time-main.js') }}"></script>
@endsection