@extends('layouts.admin')
@section('content')
<div class="row ">
	<div class="col-12 clientes">
		<h4>{{$cliente->nome}}</h4>
		<div class="box-btn">
			<button class="btn btn-danger excluir-cliente" data-id="{{$cliente->id}}">Excluir</button>	
		</div>			
		<form id="form-atualiza-cliente">
			@csrf
			<div class="form-row">
				<input type="hidden" name="cliente_id" id="cliente_id" value="{{$cliente->id}}">
				<div class="form-group col-md-4">
					<label for="nome">Nome</label>
					<input type="text" class="form-control" name="nome" id="nome" value="{{$cliente->nome}}">
				</div>
				<div class="form-group col-md-4">
					<label for="nome">E-mail</label>
					<input type="text" class="form-control" name="email" id="email" value="{{$cliente->email}}">
				</div>
				<div class="form-group col-md-4">
					<label for="nome">Telefone</label>
					<input type="text" class="form-control" name="telefone" id="telefone" value="{{$cliente->telefone}}">
				</div>
			</div>
			<button type="submit" class="btn btn-primary">Salvar</button>
		</form>
	</div>

</div>
@endsection
@section('script')

    $(document).ready(function() {
         $('#form-atualiza-cliente').submit( function(e) {
                e.preventDefault();         

                let form = $(this);
                let dados = form.serialize()
                console.log(dados)
                alertify.set('notifier','position', 'top-right');


                if ($('#nome').val() == '') {
                    alertify.warning('Preencha o nome!'); 
                }                

                if ($('#nome').val() != '') {
                    $.ajax({
                        url: ' /admin/cliente/editar',
                        type: 'POST',
                        dataType: 'json',
                        data: dados,
                    })
                    .done(function(response) {
                        if (response.status == '200') {
                            window.location.replace("/admin/clientes");
                        }
                    })
                    .fail(function(error) {
                        console.log("error");
                    })

                }
            });

             $('.excluir-cliente').click(function(e) {
                e.preventDefault();

                let clienteId = $(this).data('id')       
                alertify.confirm('Deseja realmente excluir o cliente?').set('onok', function(closeEvent){
                    $.ajax({
                        url: ' /admin/cliente/excluir',
                        type: 'GET',
                        dataType: 'json',
                        data: {'clienteId':clienteId},
                    })
                    .done(function(response) {
                        if (response.status == '200') {
                            window.location.replace("/admin/clientes");
                        }
                    })
                    .fail(function(error) {
                        console.log("error");
                    });

                });
            });            
    });

@endsection