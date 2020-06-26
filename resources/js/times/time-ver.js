 $(document).ready(function() {

    $(".ver-detalhes-membro-time").click(function() {
        let time_id = $(this).data("id");
        let membro_id = $(this).data("membro");       
        window.location = '/admin/time-membro/ver/'+time_id+'/'+membro_id;
    });

    $('#form-atualiza-time').submit( function(e) {
        e.preventDefault();         

        let form = $(this);
        let dados = form.serialize();

        alertify.set('notifier','position', 'top-right');


        if ($('#nome').val() == '') {
            alertify.warning('Preencha o nome!'); 
        }                

        if ($('#nome').val() != '') {
            $.ajax({
                url: ' /admin/time/editar',
                type: 'POST',
                dataType: 'json',
                data: dados,
            })
            .done(function(response) {                
                alertify.success('Time atualizado!'); 
                window.location = '/admin/time/ver/'+response.id;
            })
            .fail(function(error) {
                console.log('Foi encontrado um erro durante a execução. Entre em contato com a equipe de desenvolvimento!');
                console.log(error);
            })

        }
    });

    $('#form-atualiza-membro-time').submit( function(e) {
        e.preventDefault();         

        let form = $(this);
        let dados = form.serialize();

        alertify.set('notifier','position', 'top-right');

        $.ajax({
            url: ' /admin/time-membro/editar',
            type: 'POST',
            dataType: 'json',
            data: dados,
        })
        .done(function(response) {                
            alertify.success('Membro atualizado!'); 
            window.location = '/admin/time/ver/'+response.id;
        })
        .fail(function(error) {
         console.log('Foi encontrado um erro durante a execução. Entre em contato com a equipe de desenvolvimento!');
         console.log(error);
     })

        
    });

    $('.excluir-time').click(function(e) {
        e.preventDefault();

        let timeId = $(this).data('id')       
        alertify.confirm('Deseja realmente excluir o time?').set('onok', function(closeEvent){
            $.ajax({
                url: ' /admin/time/excluir/'+timeId,
                type: 'GET',
                dataType: 'json',                
            })
            .done(function(response) {
                window.location.replace("/admin/times");               
            })
            .fail(function(error) {
                console.log('Foi encontrado um erro durante a execução. Entre em contato com a equipe de desenvolvimento!');
                console.log(error);
            });

        });
    });

    $('.excluir-membro-time').click(function(e) {
        e.preventDefault();

        let membroId = $(this).data('id')       
        alertify.confirm('Deseja realmente excluir o membro do time?').set('onok', function(closeEvent){
            $.ajax({
                url: ' /admin/time-membro/excluir/'+membroId,
                type: 'GET',
                dataType: 'json',                
            })
            .done(function(response) {
                alertify.success('Membro removido!');
                window.location = '/admin/time/ver/'+response.id;              
            })
            .fail(function(error) {
               console.log('Foi encontrado um erro durante a execução. Entre em contato com a equipe de desenvolvimento!');
               console.log(error);
           });

        });
    });            
});

 $('.adicionar-membro-time').click(function(event) {
   $('#adicionarMembroTimeModal').modal('show');
});

 $('#form-membro-time').submit( function(e) {
    e.preventDefault();         

    let form = $(this);
    let dados = form.serialize()
    console.log(dados)
    alertify.set('notifier','position', 'top-right');

    if ($('#nome').val() == '') {
        alertify.warning('Preencha o nome!'); 
    } 

    if ($('#email').val() == '') {
        alertify.warning('Preencha o email!'); 
    }         

    if ($('#nome').val() != '' && $('#email').val() != '') {
        $.ajax({
            url: ' /admin/time-membro/novo',
            type: 'POST',
            dataType: 'json',
            data: dados,
        })
        .done(function(response) {
            console.log(response)
            $('#adicionarMembroTimeModal').modal('hide');
            alertify.success('Membro para o time convidado!');
            window.location = '/admin/time/ver/'+response.time;                
        })
        .fail(function(error) {
            console.log('Foi encontrado um erro durante a execução. Entre em contato com a equipe de desenvolvimento!');
            console.log(error);
        })

    }
}); 