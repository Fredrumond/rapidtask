@extends('layouts.admin')
@section('content')
<div class="row ">
	<div class="col-12 clientes">
		<h4>Novo cliente</h4>
		<form id="form-cliente">
			@csrf
			<div class="form-row">
				<div class="form-group col-md-4">
					<label for="nome">Nome</label>
					<input type="text" class="form-control" name="nome" id="nome">
				</div>
				<div class="form-group col-md-4">
					<label for="nome">E-mail</label>
					<input type="text" class="form-control" name="email" id="email">
				</div>
				<div class="form-group col-md-4">
					<label for="nome">Telefone</label>
					<input type="text" class="form-control" name="telefone" id="telefone">
				</div>				
			</div>					
			<button type="submit" class="btn btn-primary">Salvar</button>
		</form>
	</div>

</div>
@endsection
@section('script')

    $(document).ready(function() {
         $('#form-cliente').submit( function(e) {
                e.preventDefault();         

                let form = $(this);
                let dados = form.serialize()
                console.log(dados)
                alertify.set('notifier','position', 'top-right');


                if ($('#nome').val() == '') {
                    alertify.warning('Preencha o Nome!'); 
                }                

                if ($('#nome').val() != '') {
                    $.ajax({
                        url: '/admin/cliente/salvar',
                        type: 'POST',
                        dataType: 'json',
                        data: dados,
                    })
                    .done(function(response) {
                        if (response.status == '200') {
                            window.location.replace("/admin/clientes");
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