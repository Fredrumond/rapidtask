 $(document).ready(function() {
    $(".ver-detalhes-tarefa").click(function() {
        let id = $(this).data("id");
        
        window.location = '/admin/tarefa/ver/'+id;
    });

    $(".config-projeto").click(function(){
      let projetoId = $(this).data('id')
      window.location = '/admin/projeto/ver/'+projetoId;         
  });


    $('.adicionar-arquivo-projeto').click(function(event) {
       $('#adicionarArquivoProjetoModal').modal('show');
   });

    $('.box-anotacao').hide();

    $('.nova-anotacao').click(function(event) {
        $('.box-anotacao').show();
        $('.novo-anotacao').hide();           
    });

    $('.cancelar-anotacao').click(function(event) {
        $('.box-anotacao').hide();
        $('.novo-anotacao').show();
    });

    function retornaAnotacoes(){
        let projeto_id = $('#projeto_id').val();
        $.ajax({
            url: '/admin/projeto-anotacoes',
            type: 'GET',
            dataType: 'json',
            data: {'projeto_id' : projeto_id}
        })
        .done(function(response) {              
            var anotacoes = "";
            $.each( response.anotacoes, function( key, value ) {                      
                anotacoes += '<li class="feed-item"><div class="feed-item-list"><span class="date">'+value.nome+'   '+value.data+'</span> <span class="activity-text">'+value.anotacao+'</span><div class="acoes-anotacao cursor"><i class="fas fa-edit editar-anotacao" data-id="'+value.id+'"></i><i class="fas fa-trash remover-anotacao" data-id="'+value.id+'"></i></div></div></li>';                    
            });
            $('.timeline-anotacoes').html(anotacoes);               
        })
        .fail(function(error) {
            console.log('Foi encontrado um erro durante a execução. Entre em contato com a equipe de desenvolvimento!');
            console.log(error);
        })
    }

    retornaAnotacoes();

    $('#form-arquivo-projeto').submit( function(e) {
        e.preventDefault();

        var formData = new FormData(this);

        alertify.set('notifier','position', 'top-right');

        if ($('#nome').val() == '') {
            alertify.warning('Preencha o nome!'); 
        } 

        if ($('#arquivo').val() == '') {
            alertify.warning('Preencha o arquivo!'); 
        }  

        if($("#descricao").val().trim().length < 1){
            alertify.warning('Preencha a descrição'); 
        }  

        if ($('#nome').val() != '' && $('#arquivo').val() != '' && $('#descricao').val() != '') {

            $.ajax({
                url: ' /admin/projeto/arquivo/novo',
                type: 'POST',
                dataType: 'json',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
            })
            .done(function(response) {               
                alertify.success(response.msg);
                $('#adicionarArquivoProjetoModal').modal('hide');
                $('#nome').val('');
                $('#arquivo').val('');
                $('#descricao').val('');
                retornaArquivos();
            })
            .fail(function(error) {
                alertify.error(error.responseJSON.msg);                
            })
        }
    }); 

    function retornaArquivos(){
        let projeto_id = $('#projeto_id').val();
        $.ajax({
            url: '/admin/projeto/arquivos',
            type: 'GET',
            dataType: 'json',
            data: {'projeto_id' : projeto_id}
        })
        .done(function(response) {              
            var arquivos = "";
            $.each( response.arquivos, function( key, value ) {                      
                arquivos += '<tr class="text-center"><td>'+value.nome+'</td><td>'+value.descricao+'</td><td>'+value.created_at+'</td><td><a class="btn btn-info" href="/projetos/arquivos/'+value.src+'" target="_blank">Visualizar</a><button class="btn btn-danger excluir-arquivo" data-id="'+value.id+'">Excluir</button></td></tr>';                    
            });            
            $('.tabela-arquivos-lista').html(arquivos);               
        })
        .fail(function(error) {
            console.log('Foi encontrado um erro durante a execução. Entre em contato com a equipe de desenvolvimento!');
            console.log(error);
        })
    }

    retornaArquivos();

    $(document).on('click', '.excluir-arquivo', function(){ 

        let arquivo_id = $(this).data("id");

        alertify.confirm('Deseja realmente excluir o arquivo?').set('onok', function(closeEvent){
            $.ajax({
                url: ' /admin/projeto/arquivo/excluir',
                type: 'GET',
                dataType: 'json',
                data: {'arquivo_id':arquivo_id},
            })
            .done(function(response) {
                retornaArquivos();
            })
            .fail(function(error) {
               console.log('Foi encontrado um erro durante a execução. Entre em contato com a equipe de desenvolvimento!');
               console.log(error);
           });

        });
    });

    $('#form-anotacao').submit( function(e) {
        e.preventDefault();         

        let form = $(this);
        let dados = form.serialize()
        console.log(dados)
        alertify.set('notifier','position', 'top-right');

        if($("#anotacao").val().trim().length < 1){
            alertify.warning('Preencha a anotação!'); 
        }           

        if ($("#anotacao").val().trim().length > 1) {
            $.ajax({
                url: ' /admin/projeto-anotacao/salvar',
                type: 'POST',
                dataType: 'json',
                data: dados,
            })
            .done(function(response) {
                if (response.status == '200') {                     
                    let id = $('#anotacao_id').val();

                    $('.box-anotacao').hide();
                    $('.nova-anotacao').show();

                    $('#comentario').val('');
                    retornaAnotacoes();
                }
            })
            .fail(function(error) {
                console.log('Foi encontrado um erro durante a execução. Entre em contato com a equipe de desenvolvimento!');
                console.log(error);
            })

        }
    });        

    $(document).on('click', '.editar-anotacao', function(){ 

        let anotacao_id = $(this).data("id");

        $.ajax({
            url: '/admin/projeto-anotacao/ver',
            type: 'GET',
            dataType: 'json',
            data: {'anotacao_id' : anotacao_id}
        })
        .done(function(response) {
            $('#anotacaoEditar').val(response.data.anotacao);
            $('#anotacao_id').val(response.data.id);
            $('#editarAnotacaoModal').modal('show');              
        })
        .fail(function(error) {
            console.log('Foi encontrado um erro durante a execução. Entre em contato com a equipe de desenvolvimento!');
            console.log(error);
        })
    }); 

    $(document).on('click', '.remover-anotacao', function(){ 

        let anotacao_id = $(this).data("id");

        alertify.confirm('Deseja realmente excluir a anotação?').set('onok', function(closeEvent){
            $.ajax({
                url: ' /admin/projeto-anotacao/excluir',
                type: 'GET',
                dataType: 'json',
                data: {'anotacao_id':anotacao_id},
            })
            .done(function(response) {
                if (response.status == '200') {
                    retornaAnotacoes();
                }
            })
            .fail(function(error) {
                console.log('Foi encontrado um erro durante a execução. Entre em contato com a equipe de desenvolvimento!');
                console.log(error);
            });

        });
    });

    $('.cancelar-anotacao-editar').click(function(event) {
        $('#editarAnotacaoModal').modal('hide');  
    });

    $('#form-anotacao-editar').submit( function(e) {
        e.preventDefault();         

        let form = $(this);
        let dados = form.serialize()
        console.log(dados)
        alertify.set('notifier','position', 'top-right');

        if($("#anotacaoEditar").val().trim().length < 1){
            alertify.warning('Preencha a anotação!'); 
        }           

        if ($("#anotacaoEditar").val().trim().length > 1) {
            $.ajax({
                url: ' /admin/projeto-anotacao/editar',
                type: 'POST',
                dataType: 'json',
                data: dados,
            })
            .done(function(response) {
                if (response.status == '200') {

                    $('#editarAnotacaoModal').modal('hide');  
                    retornaAnotacoes();
                }
            })
            .fail(function(error) {
                console.log('Foi encontrado um erro durante a execução. Entre em contato com a equipe de desenvolvimento!');
                console.log(error);
            })

        }
    });
});