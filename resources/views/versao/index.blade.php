<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>RAPIDTASK</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <style>

    body {
        background: #384670;
    }

    .versao-container {
        display: block;
        width: 600px;
        margin: 10% auto;
        max-width: 90%;
        background: #f4f5f7;
        padding: 50px;
    }

    .versao-container h1{
        font-size: 2em;
        font-weight: 600;
        color: #343f60;
        text-shadow: 1px 1px 0 #eee;
    }

    .versao-container p{
        margin: 15px 0;
    }

</style>
</head>
<body>   
    <div class="versao-container">
        <h1>Versões</h1>
        <span class="badge badge-info">Development</span>        
        <span class="badge badge-warning">Test</span>
        <span class="badge badge-danger">Bug</span>
        <span class="badge badge-success">Stable</span>
        <table class="table">
          <thead>
            <tr>
              <th scope="col">Versão</th>
              <th scope="col">Data</th>
              <th scope="col">Descrião</th>
          </tr>
      </thead>
      <tbody>
        <tr>
          <th>1.0.0-alpha.0</th>
          <td>29/03/2019</td>
          <td>
            <ul>
                <li>Trefas</li>
                <ul>
                    <li><span class="badge badge-success">Cadastrar</span></li>
                    <li><span class="badge badge-success">Visualizar</span></li>
                    <li><span class="badge badge-success">Editar</span></li>
                    <li><span class="badge badge-success">Excluir</span></li>
                    <li><span class="badge badge-success">Arquivar</span></li>                    
                    <li><span class="badge badge-info">Comentario tarefa</span></li>                    
                    <li><span class="badge badge-info">Historico tarefa</span></li>                    
                </ul>
                <li>Projetos</li>
                <ul>
                   <li><span class="badge badge-success">Cadastrar</span></li>
                   <li><span class="badge badge-info">Visualizar</span></li>
                   <li><span class="badge badge-success">Editar</span></li>                   
               </ul>
               <li>Clientes</li>
               <ul>
                 <li><span class="badge badge-success">Cadastrar</span></li>
                 <li><span class="badge badge-success">Visualizar</span></li>
                 <li><span class="badge badge-success">Editar</span></li>
                 <li><span class="badge badge-success">Excluir</span></li>                 
             </ul>
         </ul>
     </td>

 </tr>

</tbody>
</table> 
</div>

</body>
</html>

