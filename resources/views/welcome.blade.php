<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <style>

    body {
        background: #384670;
    }

    .manutencao {
        display: block;
        width: 600px;
        margin: 10% auto;
        max-width: 90%;
        background: #f4f5f7;
        padding: 50px;
    }

    .manutencao h1{
        font-size: 2em;
        font-weight: 600;
        color: #343f60;
        text-shadow: 1px 1px 0 #eee;
    }

    .manutencao p{
        margin: 15px 0;
    }

    .versao {
        bottom: 0;
        right: 20px;
        position: absolute;
        color: #fff;
        font-size: 16px;
    }

    .versao a {
        text-decoration: none;
        color: #fff;
    }

</style>
</head>
<body>   
    <div class="manutencao">
        <h1>Desculpe, estamos em manutenção!</h1>
        <p>Neste momento estamos trabalhando para melhorar ainda mais sua experiência em nosso site.</p>
        <p><b>Por favor, volte em algumas horas para conferir as novidades!</b></p>
        <em>Atenciosamente Rapidtask</em>
        <span class="versao"><a href="{{ route('versao') }}">1.0.0-alpha.0</a></span>
    </div>    
</body>
</html>
