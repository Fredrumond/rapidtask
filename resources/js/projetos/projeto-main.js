$(document).ready(function() {
	$(".ver-detalhes-projeto").click(function() {
		console.log("oi")
		let id = $(this).data("id");                
		window.location = '/admin/projeto/detalhe/'+id;
	});

	$('#form-projeto').submit( function(e) {
		e.preventDefault();         

		let form = $(this);
		let dados = form.serialize()

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

		if ($('#time_id').val() == '') {
			alertify.warning('Preencha o time!'); 
		}

		if ($('#titulo').val() != '' && $('#cliente_id').val() != '' && $('#sigla').val() != '' && $('#time_id').val() != '') {
			$.ajax({
				url: '/admin/projeto/salvar',
				type: 'POST',
				dataType: 'json',
				data: dados,
			})
			.done(function(response) {                       
				window.location = '/admin/projeto/detalhe/'+response.id;                       
			})
			.fail(function() {
				console.log('Foi encontrado um erro durante a execução. Entre em contato com a equipe de desenvolvimento!');
				console.log(error);
			})
		}
	});
});