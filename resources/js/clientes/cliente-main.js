 $(document).ready(function() {

 	//VISUALIZAR TIME
 	$(".ver-detalhes-cliente").click(function() {
 		let id = $(this).data("id"); 		
 		window.location = '/admin/cliente/ver/'+id;
	 });
	 
	//VALIDATION
	$('#nome').blur(function() {
		$('#nome').removeClass("invalid");
		$('#nome').next().css({ "display": "none" })
	})

	$('#time_id').blur(function() {
		$('#time_id').removeClass("invalid");
		$('#time_id').next().css({ "display": "none" })
	})

 	//CADASTRA NOVO TIME
 	$('#form-cliente').submit( function(e) {
 		e.preventDefault();         

 		let form = $(this);
		let dados = form.serialize()
		 
		 httpRequest({
			method: 'POST',
			endPoint: '/admin/cliente/salvar',
			dataType: 'json', 
			data: dados, 
			redirect: {
				url: '/admin/cliente/ver',
				param: 'id'
			}
		});

 		// alertify.set('notifier','position', 'top-right');


 		// if ($('#nome').val() == '') {
 		// 	alertify.warning('Preencha o Nome!'); 
 		// } 

 		// if ($('#time_id').val() == '') {
 		// 	alertify.warning('Preencha o time!'); 
 		// }               

 		// if ($('#nome').val() != '' && $('#time_id').val() != '') {
 		// 	$.ajax({
 		// 		url: '/admin/cliente/salvar',
 		// 		type: 'POST',
 		// 		dataType: 'json',
 		// 		data: dados,
 		// 	})
 		// 	.done(function(response) {
 		// 		window.location = '/admin/cliente/ver/'+response.id;                
 		// 	})
 		// 	.fail(function() {
 		// 		console.log('Foi encontrado um erro durante a execução. Entre em contato com a equipe de desenvolvimento!');
 		// 		console.log(error);
 		// 	})
 		// }
 	});
 });