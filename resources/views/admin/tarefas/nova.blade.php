@extends('layouts.admin')
@section('content')
<div class="row ">
	<div class="col-12 tarefas">
		<h4>Nova tarefa</h4>
		<form id="form-tarefa">
			@csrf
			<div class="form-row">
				<div class="form-group col-md-3">
					<label for="inputEmail4">Titulo</label>
					<input type="text" class="form-control" name="titulo" id="titulo">
				</div>
				<div class="form-group col-md-3">
					<label for="inputState">Projeto</label>
					<select class="form-control" name="projeto_id" id="projeto_id">								
						@foreach($projetos as $projeto)
						<option value="{{ $projeto->id }}">{{ $projeto->nome }}</option>
						@endforeach											
					</select>
				</div>
				<div class="form-group col-md-2">
					<label for="inputState">Tipo</label>
					<select class="form-control" name="tipo_id" id="tipo_id">								
						@foreach($tipos as $tipo)
						<option value="{{ $tipo->id }}">{{ $tipo->nome }}</option>
						@endforeach											
					</select>
				</div>
				<div class="form-group col-md-2">
					<label for="inputState">Situação</label>
					<select class="form-control" name="situacao_id" id="situacao_id">
						@foreach($situacoes as $situacao)
						<option value="{{ $situacao->id }}">{{ $situacao->nome }}</option>
						@endforeach	
					</select>
				</div>
				<div class="form-group col-md-2">
					<label for="inputState">Prioridade</label>
					<select class="form-control" name="prioridade_id" id="prioridade_id">
						@foreach($prioridades as $prioridade)
						<option value="{{ $prioridade->id }}">{{ $prioridade->nome }}</option>
						@endforeach
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
					<input type="date" class="form-control" name="dt_inicio" id="dt_inicio">
				</div>
				<div class="form-group col-md-2">
					<label for="inputCity">Data Prevista</label>
					<input type="date" class="form-control" name="dt_prevista" id="dt_prevista">
				</div>
				<div class="form-group col-md-2">
					<label for="inputCity">Data Conclusão</label>
					<input type="date" class="form-control" name="dt_fim" id="dt_fim">
				</div>
				<div class="form-group col-md-2">
					<label for="inputCity">Tempo Estimado(Horas)</label>
					<input type="text" class="form-control" name="tempo_estimado" id="tempo_estimado">
				</div>
			</div>					
			<button type="submit" class="btn btn-primary">Criar</button>
		</form>
	</div>

</div>
@endsection
@section('script')
<script type="text/javascript" src="{{ asset('js/tarefa-main.js') }}"></script>
@endsection