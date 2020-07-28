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
		
		$.ajax({
			url: '/admin/projeto/salvar',
			type: 'POST',
			dataType: 'json',
			data: dados,
		})
		.done(function(response) {                       
			window.location = '/admin/projeto/detalhe/'+response.id;                       
		})
		.fail(function(error) {
			if(error.status == 422){
				validateForm(error.responseJSON.errors)
				// const obj = error.responseJSON.errors;
				// Object.keys(obj).forEach(function(item){
				// 	$(`#${item}`).addClass("invalid");
				// 	$(`#${item}`).next().css({ "display": "block" }).append(obj[item])
				// });
			}
		})
	});
});