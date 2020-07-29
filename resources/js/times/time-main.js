 $(document).ready(function() {

 	$(".ver-detalhes-time").click(function() {
 		let id = $(this).data("id");
 		window.location = '/admin/time/ver/'+id;
 	});

 	$('.adicionar-novo-time').click(function(event) {
 		$('#adicionarNovoTimeModal').modal('show');
 	});

 	$('.cancelar-novo-time').click(function(event) {
 		$('#adicionarNovoTimeModal').modal('hide');
	 });
	 
	 //VALIDATION
	 $('#nome').blur(function() {
		$('#nome').removeClass("invalid");
		$('#nome').next().css({ "display": "none" })
	})

 	$('#form-novo-time').submit( function(e) {
 		e.preventDefault();         

 		let form = $(this);
		let dados = form.serialize()
		 
		httpRequest({
			method: 'POST',
			endPoint: '/admin/time/salvar',
			dataType: 'json', 
			data: dados, 
			redirect: {
				url: '/admin/times'
			}
		});

 	});

 });