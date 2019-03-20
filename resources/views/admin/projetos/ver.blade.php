@extends('layouts.admin')
@section('content')
<div class="row ">
	<div class="col-12 tarefas">
		<h4>{{$tarefa->titulo}}</h4>
		<div class="box-btn">
			<button class="btn btn-info arquivar-tarefa">Arquivar</button>	
		</div>	
		<form id="form-atualiza-tarefa">
			@csrf
			<div class="form-row">
				<input type="hidden" name="tarefa_id" id="tarefa_id" value="{{$tarefa->id}}">
				<div class="form-group col-md-6">
					<label for="inputEmail4">Titulo</label>
					<input type="text" class="form-control" name="titulo" id="titulo" value="{{$tarefa->titulo}}">
				</div>
				<div class="form-group col-md-2">
					<label for="inputState">Tipo</label>
					<select class="form-control" name="tipo_id">								
						@foreach($tipos as $tipo)
						<option value="{{ $tipo->id }}" {{$tarefa->tipo_id == $tipo->id ? 'selected="selected"' : '' }}>{{ $tipo->nome }}</option>

						@endforeach								
					</select>
				</div>
				<div class="form-group col-md-2">
					<label for="inputState">Situação</label>
					<select class="form-control" name="situacao_id" id="situacao_id">
						@foreach($situacoes as $situacao)
						<option value="{{ $situacao->id }}" {{$tarefa->situacao_id == $situacao->id ? 'selected="selected"' : '' }}>{{ $situacao->nome }}</option>
						@endforeach	
					</select>
				</div>
				<div class="form-group col-md-2">
					<label for="inputState">Prioridade</label>
					<select class="form-control" name="prioridade_id" id="prioridade_id">
						@foreach($prioridades as $prioridade)
						<option value="{{ $prioridade->id }}" {{$tarefa->prioridade_id == $prioridade->id ? 'selected="selected"' : '' }}>{{ $prioridade->nome }}</option>

						@endforeach									
					</select>
				</div>
			</div>
			<div class="form-group">
				<label for="descricao">Descrição</label>
				<textarea class="form-control" id="descricao" rows="3" name="descricao" id="descricao">	{{$tarefa->descricao}}</textarea>
			</div>

			<div class="form-row">						
				<div class="form-group col-md-2">
					<label for="inputCity">Data Inicio</label>
					<input type="date" class="form-control" name="dt_inicio" id="dt_inicio" value="{{$tarefa->dt_inicio}}">
				</div>
				<div class="form-group col-md-2">
					<label for="inputCity">Data Prevista</label>
					<input type="date" class="form-control" name="dt_prevista" id="dt_prevista" value="{{$tarefa->dt_prevista}}">
				</div>
				<div class="form-group col-md-2">
					<label for="inputCity">Data Conclusão</label>
					<input type="date" class="form-control" name="dt_fim" id="dt_fim" value="{{$tarefa->dt_fim}}">
				</div>
				<div class="form-group col-md-2">
					<label for="inputCity">Tempo Estimado</label>
					<input type="text" class="form-control" name="tempo_estimado" id="tempo_estimado" value="{{$tarefa->tempo_estimado}}">
				</div>
			</div>					
			<button type="submit" class="btn btn-primary">Salvar</button>
		</form>
	</div>

</div>
@endsection
@section('script')

    $(document).ready(function() {
         $('#form-atualiza-tarefa').submit( function(e) {
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
                        url: ' /admin/tarefa/editar',
                        type: 'POST',
                        dataType: 'json',
                        data: dados,
                    })
                    .done(function(response) {
                        if (response.status == '200') {
                            window.location.replace("/admin/tarefas");
                        }
                    })
                    .fail(function(error) {
                        console.log("error");
                    })

                }
            });

            $('.arquivar-tarefa').click(function(e) {
                e.preventDefault();

                let tarefaId = $('#tarefa_id').val();       
                alertify.confirm('Deseja realmente arquivar a tarefa?').set('onok', function(closeEvent){
                    $.ajax({
                        url: ' /admin/tarefa/arquivar',
                        type: 'GET',
                        dataType: 'json',
                        data: {'tarefaId':tarefaId},
                    })
                    .done(function(response) {
                        if (response.status == '200') {
                            window.location.replace("/admin/tarefas");
                        }
                    })
                    .fail(function(error) {
                        console.log("error");
                    });

                });
            });
    });

@endsection