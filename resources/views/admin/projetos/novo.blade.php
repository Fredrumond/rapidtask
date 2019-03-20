@extends('layouts.admin')
@section('content')
<div class="row ">
	<div class="col-12 projetos">
		<h4>Novo Projeto</h4>
		<form id="form-projeto">
			@csrf
			<div class="form-row">
				<div class="form-group col-md-4">
					<label for="inputEmail4">Nome</label>
					<input type="text" class="form-control" name="nome" id="nome">
				</div>
				<div class="form-group col-md-4">
					<label for="inputEmail4">Sigla</label>
					<input type="text" class="form-control" name="sigla" id="sigla">
				</div>
				<div class="form-group col-md-4">
					<label for="inputState">Cliente</label>
					<select class="form-control" name="cliente_id" id="cliente_id">								
						@foreach($clientes as $cliente)
						<option value="{{ $cliente->id }}">{{ $cliente->nome }}</option>
						@endforeach											
					</select>
				</div>
			</div>
			<div class="form-group">
				<label for="descricao">Descrição</label>
				<textarea class="form-control" id="descricao" rows="3" name="descricao" id="descricao"></textarea>
			</div>

						
			<button type="submit" class="btn btn-primary">Criar</button>
		</form>
	</div>

</div>
@endsection
@section('script')

    $(document).ready(function() {
         $('#form-projeto').submit( function(e) {
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
                        url: '/admin/projeto/salvar',
                        type: 'POST',
                        dataType: 'json',
                        data: dados,
                    })
                    .done(function(response) {
                        if (response.status == '200') {
                            window.location.replace("/admin/projetos");
                        }
                        console.log(response);
                    })
                    .fail(function() {
                        console.log("error");
                    })

                }

            });
    });

@endsection