    $(document).ready(function() {

        $(".ver-detalhes-tarefa").click(function() {            
            let id = $(this).data("id");            
            window.location = '/admin/tarefa/ver/'+id;
        });

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
                    comentarios += '<li class="feed-item"><div class="feed-item-list"><span class="date">'+value.nome+'   '+value.data+'</span> <span class="activity-text">'+value.comentario+'</span><div class="acoes-comentario cursor"><i class="fas fa-edit editar-comentario" data-id="'+value.id+'"></i><i class="fas fa-trash remover-comentario" data-id="'+value.id+'"></i></div></div></li>';                    
                });
                $('.timeline-comentarios').html(comentarios);               
            })
            .fail(function(error) {
             console.log('Foi encontrado um erro durante a execução. Entre em contato com a equipe de desenvolvimento!');
             console.log(error);
         })
        }

        retornaComentarios();

        function retornaHistorico(){
            let tarefa_id = $('#tarefaId').val();
            $.ajax({
                url: '/admin/tarefa-historico',
                type: 'GET',
                dataType: 'json',
                data: {'tarefa_id' : tarefa_id}
            })
            .done(function(response) {                       
                var historico = "";
                $.each( response.historico, function( key, value ) {                      
                    historico += '<li class="feed-item"><div class="feed-item-list"><span class="date">'+value.nome+' alterou '+value.atividade+'   '+value.data+'</span> </div></li>';                    
                });
                $('.timeline-historico').html(historico);               
            })
            .fail(function(error) {
                console.log('Foi encontrado um erro durante a execução. Entre em contato com a equipe de desenvolvimento!');
                console.log(error);
            })
        }

        retornaHistorico();

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
                    if (response.redireciona == true) {
                        window.location = '/admin/tarefas';
                    } else {
                        window.location = '/admin/tarefa/ver/'+response.id;
                    }
                })
                .fail(function(error) {
                    console.log('Foi encontrado um erro durante a execução. Entre em contato com a equipe de desenvolvimento!');
                    console.log(error);
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
                    console.log('Foi encontrado um erro durante a execução. Entre em contato com a equipe de desenvolvimento!');
                    console.log(error);
                });

            });
        });

        $('#form-comentario').submit( function(e) {
            e.preventDefault();         

            let form = $(this);
            let dados = form.serialize()

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
                    console.log('Foi encontrado um erro durante a execução. Entre em contato com a equipe de desenvolvimento!');
                    console.log(error);
                })

            }
        });        

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
             console.log('Foi encontrado um erro durante a execução. Entre em contato com a equipe de desenvolvimento!');
             console.log(error);
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
                 console.log('Foi encontrado um erro durante a execução. Entre em contato com a equipe de desenvolvimento!');
                 console.log(error);
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
                    console.log('Foi encontrado um erro durante a execução. Entre em contato com a equipe de desenvolvimento!');
                    console.log(error);
                })

            }
        }); 

        //VALIDATION
        $('#titulo').blur(function() {
            $('#titulo').removeClass("invalid");
            $('#titulo').next().css({ "display": "none" })
        })

        $('#form-tarefa').submit( function(e) {
            e.preventDefault();         

            let form = $(this);
            let dados = form.serialize()
                $.ajax({
                    url: '/admin/tarefa/salvar',
                    type: 'POST',
                    dataType: 'json',
                    data: dados,
                })
                .done(function(response) {                  
                    window.location.replace("/admin/tarefas");
                })
                .fail(function(error) {
                    if(error.status == 422){
                        validateForm(error.responseJSON.errors)
                        
                    }
                })
        });

        $('.excluir-tarefa').click(function(e) {
            e.preventDefault();

            let tarefaId = $(this).data('id')       
            alertify.confirm('Deseja realmente excluir a tarefa?').set('onok', function(closeEvent){
                $.ajax({
                    url: ' /admin/tarefa/excluir',
                    type: 'GET',
                    dataType: 'json',
                    data: {'tarefaId':tarefaId},
                })
                .done(function(response) {
                    if (response.status == '200') {
                        window.location.replace("/admin/tarefa/arquivadas");
                    }
                })
                .fail(function(error) {
                    console.log("error");
                });

            });
        });

        $('.restaurar-tarefa').click(function(e) {
            e.preventDefault();

            let tarefaId = $(this).data('id')       
            alertify.confirm('Deseja realmente restaurar a tarefa?').set('onok', function(closeEvent){
                $.ajax({
                    url: ' /admin/tarefa/recuperar',
                    type: 'GET',
                    dataType: 'json',
                    data: {'tarefaId':tarefaId},
                })
                .done(function(response) {
                    if (response.status == '200') {
                        window.location.replace("/admin/tarefa/arquivadas");
                    }
                })
                .fail(function(error) {
                    console.log("error");
                });

            });
        });




    });