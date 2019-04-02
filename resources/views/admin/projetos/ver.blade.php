@extends('layouts.admin')
@section('content')
<div class="row ">
	<div class="col-12 tarefas">
		<h4>{{$projeto->titulo}}</h4>
		<div class="box-btn">
			<button class="btn btn-danger excluir-projeto" data-id="{{$projeto->id}}">Excluir</button>	
		</div>	
		<form id="form-atualiza-projeto">
			@csrf
			<div class="form-row">
				<input type="hidden" name="projeto_id" id="projeto_id" value="{{$projeto->id}}">
				<div class="form-group col-md-4">
					<label for="inputEmail4">Nome</label>
					<input type="text" class="form-control" name="nome" id="nome" value="{{$projeto->nome}}">
				</div>
				<div class="form-group col-md-4">
					<label for="inputEmail4">Sigla</label>
					<input type="text" class="form-control" name="sigla" id="sigla" value="{{$projeto->sigla}}">
				</div>
				<div class="form-group col-md-4">
					<label for="inputState">Cliente</label>
					<select class="form-control" name="cliente_id" id="cliente_id">								
						@foreach($clientes as $cliente)						
						<option value="{{ $cliente->id }}" {{$projeto->cliente_id == $cliente->id ? 'selected="selected"' : '' }}>{{ $cliente->nome }}</option>
						@endforeach											
					</select>
				</div>
			</div>
			<div class="form-group">
				<label for="descricao">Descrição</label>
				<textarea class="form-control" id="descricao" rows="3" name="descricao" id="descricao">{{$projeto->descricao}}</textarea>
			</div>							
			<button type="submit" class="btn btn-primary">Salvar</button>
		</form>
	</div>

</div>
@endsection
@section('script')
<script>
    $(document).ready(function() {
         $('#form-atualiza-projeto').submit( function(e) {
                e.preventDefault();         

                let form = $(this);
                let dados = form.serialize()
                console.log(dados)
                alertify.set('notifier','position', 'top-right');


                if ($('#nome').val() == '') {
                    alertify.warning('Preencha o nome!'); 
                }

                 if ($('#sigla').val() == '') {
                    alertify.warning('Preencha a sigla!'); 
                }

                if ($('#cliente_id').val() == '') {
                    alertify.warning('Preencha o cliente!'); 
                }

                 if ($('#titulo').val() != '' && $('#cliente_id').val() != '' && $('#sigla').val() != '') {
                    $.ajax({
                        url: ' /admin/projeto/editar',
                        type: 'POST',
                        dataType: 'json',
                        data: dados,
                    })
                    .done(function(response) {
                        if (response.status == '200') {
                            window.location.replace("/admin/projetos");
                        }
                    })
                    .fail(function(error) {
                        console.log("error");
                    })

                }
            });

            $('.excluir-projeto').click(function(e) {
                e.preventDefault();

                let projetoId = $(this).data('id')       
                alertify.confirm('Deseja realmente excluir o projeto?').set('onok', function(closeEvent){
                    $.ajax({
                        url: ' /admin/projeto/excluir',
                        type: 'GET',
                        dataType: 'json',
                        data: {'projetoId':projetoId},
                    })
                    .done(function(response) {
                        if (response.status == '200') {
                            window.location.replace("/admin/projetos");
                        }
                    })
                    .fail(function(error) {
                        console.log("error");
                    });

                });
            });

            
    });
</script>
@endsection