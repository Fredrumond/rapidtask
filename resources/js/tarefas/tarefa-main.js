    $(document).ready(function() {

        function error(erro){
            if(!alertify.errorAlert){

              alertify.dialog('errorAlert',function factory(){
                return{
                    build:function(){
                        var errorHeader = '<span class="fa fa-times-circle fa-2x" '
                        +    'style="vertical-align:middle;color:#e10000;">'
                        + '</span> Erro na aplicação';
                        this.setHeader(errorHeader);
                    }
                };
            },true,'alert');
          }

          alertify
          .errorAlert("Foi encontrado um erro inesperado na aplicação! <br/><br/><br/>" +
            "Reporte o erro para o administrador: <br/><br/><br/>"+ erro);
      }

      function retornaComentarios(){
        let tarefa_id = $('#tarefaId').val();
        $.ajax({
            url: '/admin/tarefa-comentarios',
            type: 'GET',
            dataType: 'json',
            data: {'tarefa_id' : tarefa_id}
        })
        .done(function(response) {              
            var comentarios = "";
            $.each( response.comentarios, function( key, value ) {                      
                comentarios += '<li><a href="#">'+value.nome+'</a><a href="#" class="float-right">'+value.data+'</a><p>'+value.comentario+'</p><div class="acoes-comentario"><i class="fas fa-edit editar-comentario" data-id="'+value.id+'"></i><i class="fas fa-trash remover-comentario" data-id="'+value.id+'"></i></div></li>';                    
            });
            $('.timeline-comentarios').html(comentarios);               
        })
        .fail(function(error) {
            console.log("error");
        })
    }

    $('.box-comentario').hide();

    $('.novo-comentario').click(function(event) {
        $('.box-comentario').show();
        $('.novo-comentario').hide();           
    });

    $('.cancelar-comentario').click(function(event) {
        $('.box-comentario').hide();
        $('.novo-comentario').show();
    });

    $('#form-atualiza-tarefa').submit( function(e) {
        e.preventDefault();         

        let form = $(this);
        let dados = form.serialize()
        console.log(dados)
        alertify.set('notifier','position', 'top-right');


        if ($('#titulo').val() == '') {
            alertify.warning('Preencha o titulo!'); 
        }

        if ($('#tipo_id').val() == '') {
            alertify.warning('Preencha o tipo!'); 
        }

        if ($('#situacao_id').val() == '') {
            alertify.warning('Preencha a situação!'); 
        }

        if ($('#prioridade_id').val() == '') {
            alertify.warning('Preencha a prioridade!'); 
        }

        if ($('#titulo').val() != '' && $('#tipo_id').val() != '' && $('#situacao_id').val() != '' && $('#prioridade_id').val() != '' ) {
            $.ajax({
                url: ' /admin/tarefa/editar',
                type: 'POST',
                dataType: 'json',
                data: dados,
            })
            .done(function(response) {
                if (response.status == '200') {
                    window.location.replace("/admin/tarefas");
                }
            })
            .fail(function(error) {
                console.log("error");
            })

        }
    });

    $('.arquivar-tarefa').click(function(e) {
        e.preventDefault();

        let tarefaId = $('#tarefa_id').val();       
        alertify.confirm('Deseja realmente arquivar a tarefa?').set('onok', function(closeEvent){
            $.ajax({
                url: ' /admin/tarefa/arquivar',
                type: 'GET',
                dataType: 'json',
                data: {'tarefaId':tarefaId},
            })
            .done(function(response) {
                if (response.status == '200') {
                    window.location.replace("/admin/tarefas");
                }
            })
            .fail(function(error) {
                console.log("error");
            });

        });
    });

    $('#form-comentario').submit( function(e) {
        e.preventDefault();         

        let form = $(this);
        let dados = form.serialize()
        console.log(dados)
        alertify.set('notifier','position', 'top-right');

        if($("#comentario").val().trim().length < 1){
            alertify.warning('Preencha o comentario!'); 
        }           

        if ($("#comentario").val().trim().length > 1) {
            $.ajax({
                url: ' /admin/tarefa-comentario/salvar',
                type: 'POST',
                dataType: 'json',
                data: dados,
            })
            .done(function(response) {
                if (response.status == '200') {                     
                    let id = $('#tarefaId').val();

                    $('.box-comentario').hide();
                    $('.novo-comentario').show();

                    $('#comentario').val('');
                    retornaComentarios();
                }
            })
            .fail(function(error) {
                console.log("error");
            })

        }
    });

    retornaComentarios();

    $(document).on('click', '.editar-comentario', function(){ 

        let comentarioId = $(this).data("id");

        $.ajax({
            url: '/admin/tarefa-comentario/ver',
            type: 'GET',
            dataType: 'json',
            data: {'comentarioId' : comentarioId}
        })
        .done(function(response) {
            $('#comentarioEditar').val(response.data.comentario);
            $('#comentarioId').val(response.data.id);
            $('#editarComentarioModal').modal('show');              
        })
        .fail(function(error) {
            console.log("error");
        })
    }); 

    $(document).on('click', '.remover-comentario', function(){ 

        let comentarioId = $(this).data("id");

        alertify.confirm('Deseja realmente excluir o comentario?').set('onok', function(closeEvent){
            $.ajax({
                url: ' /admin/tarefa-comentario/excluir',
                type: 'GET',
                dataType: 'json',
                data: {'comentarioId':comentarioId},
            })
            .done(function(response) {
                if (response.status == '200') {
                    retornaComentarios();
                }
            })
            .fail(function(error) {
                console.log("error");
            });

        });
    });

    $('.cancelar-comentario-editar').click(function(event) {
        $('#editarComentarioModal').modal('hide');  
    });

    $('#form-comentario-editar').submit( function(e) {
        e.preventDefault();         

        let form = $(this);
        let dados = form.serialize()
        console.log(dados)
        alertify.set('notifier','position', 'top-right');

        if($("#comentarioEditar").val().trim().length < 1){
            alertify.warning('Preencha o comentario!'); 
        }           

        if ($("#comentarioEditar").val().trim().length > 1) {
            $.ajax({
                url: ' /admin/tarefa-comentario/editar',
                type: 'POST',
                dataType: 'json',
                data: dados,
            })
            .done(function(response) {
                if (response.status == '200') {

                    $('#editarComentarioModal').modal('hide');  
                    retornaComentarios();
                }
            })
            .fail(function(error) {
                console.log("error");
            })

        }
    }); 




});