 $(document).ready(function() {

  //VER DETALHES DO PROJETO
  $(".ver-detalhes-projeto").click(function() {      
    let id = $(this).data("id");      
    window.location = '/admin/projeto/detalhe/'+id;
  });

  //ATUALIZAR DADOS DO CLIENTE
  $('#form-atualiza-cliente').submit( function(e) {
    e.preventDefault();         

    let form = $(this);
    let dados = form.serialize()
    
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
        window.location.replace("/admin/clientes");          
      })
      .fail(function(error) {
        console.log('Foi encontrado um erro durante a execução. Entre em contato com a equipe de desenvolvimento!');
        console.log(error);
      })

    }
  });

  //EXCLUIR CLIENTE
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
        window.location.replace("/admin/clientes");          
      })
      .fail(function(error) {
       console.log('Foi encontrado um erro durante a execução. Entre em contato com a equipe de desenvolvimento!');
       console.log(error);
     });

    });
  });            
});