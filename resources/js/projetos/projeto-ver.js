  $(document).ready(function() {

   $(".voltar-projeto").click(function() {
    console.log("oi")
    let id = $(this).data("id");
    window.location = '/admin/projeto/detalhe/'+id;
  });

   $('#form-atualiza-projeto').submit( function(e) {
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
        url: ' /admin/projeto/editar',
        type: 'POST',
        dataType: 'json',
        data: dados,
      })
      .done(function(response) {       
        window.location = '/admin/projeto/detalhe/'+response.id;
      })
      .fail(function(error) {
        console.log('Foi encontrado um erro durante a execução. Entre em contato com a equipe de desenvolvimento!');
        console.log(error);
      })

    }
  });

   $('.excluir-projeto').click(function(e) {
    e.preventDefault();

    let projetoId = $(this).data('id')       
    alertify.confirm('Deseja realmente excluir o projeto?').set('onok', function(closeEvent){
      $.ajax({
        url: ' /admin/projeto/excluir',
        type: 'GET',
        dataType: 'json',
        data: {'projetoId':projetoId},
      })
      .done(function(response) {                        
        window.location.replace("/admin/projetos");

      })
      .fail(function(error) {
        console.log('Foi encontrado um erro durante a execução. Entre em contato com a equipe de desenvolvimento!');
        console.log(error);
      });

    });
  });
 });