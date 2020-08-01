<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapidtask</title>
    <link rel="stylesheet" href="{{ asset('site/css/reset.css') }}" />
    <link rel="stylesheet" href="{{ asset('site/css/login.css') }}" />
</head>
<body>
    <div class='login'>
        <div class='left'>

        </div>
        <div class='right'>
            <div class='box'>
                <div class='header'>
                    <h1>Login</h1>
                </div>
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <label>
                        <span class="field">E-mail:</span>
                        <input type="email" name="email" placeholder="Informe seu e-mail" required="">
                        @if ($errors->has('email'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
                    </label>

                    <label>
                        <span class="field">Senha:</span>
                        <input type="password" name="password" placeholder="Informe sua senha" required="">
                        @if ($errors->has('password'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        @endif
                    </label>

                    <button class="">Entrar</button>
                </form>
                <div class='footer'></div>
            </div>
        </div>
    </div>
</body>
</html>