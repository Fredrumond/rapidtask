$(document).ready(function() {
	$(".ver-detalhes-projeto").click(function() {
		console.log("oi")
		let id = $(this).data("id");                
		window.location = '/admin/projeto/detalhe/'+id;
	});

	//VALIDATION
	$('#nome').blur(function() {
		$('#nome').removeClass("invalid");
		$('#nome').next().css({ "display": "none" })
	})

	$('#sigla').blur(function() {
		$('#sigla').removeClass("invalid");
		$('#sigla').next().css({ "display": "none" })
	})

	$('#form-projeto').submit( function(e) {
		e.preventDefault();         

		let form = $(this);
		let dados = form.serialize()

		httpRequest({
			method: 'POST',
			endPoint: '/admin/projeto/salvar',
			dataType: 'json', 
			data: dados, 
			redirect: {
				url: '/admin/projeto/detalhe',
				param: 'id'
			}
		});
	});
});