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

 	$('#form-novo-time').submit( function(e) {
 		e.preventDefault();         

 		let form = $(this);
 		let dados = form.serialize()
 		
 		alertify.set('notifier','position', 'top-right');


 		if ($('#nome').val() == '') {
 			alertify.warning('Preencha o Nome!'); 
 		}                

 		if ($('#nome').val() != '') {
 			$.ajax({
 				url: '/admin/time/salvar',
 				type: 'POST',
 				dataType: 'json',
 				data: dados,
 			})
 			.done(function(response) {                
 				window.location.replace("/admin/times");
 			})
 			.fail(function(error) {
 				console.log('Foi encontrado um erro durante a execução. Entre em contato com a equipe de desenvolvimento!');
 				console.log(error);
 			})

 		}

 	});

 });