<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <title>RAPIDTASK</title>

    <!-- BOOTSTRAP -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <!-- FONTAWESOME -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
    <!-- ALERTFY-->
    <link rel="stylesheet" href="{{ asset('alertfy/css/alertify.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('alertfy/css/themes/default.min.css') }}" />
    <!-- STYLE -->
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <div class="menu-topo">
        <div class="row">
            <div class="col-6 navegacao">
                <ul>
                    <a href="{{ route('admin.dashboard') }}"><li><i class="fas fa-home"></i>Home</li></a>
                    <a href="{{ route('admin.projetos') }}"><li><i class="fas fa-folder-open"></i>Projetos</li></a>
                    <a href="{{ route('admin.tarefas') }}"><li><i class="far fa-calendar-check"></i>Tarefas</li></a>
                    <a href=""><li><i class="fas fa-comment-dollar"></i>Orçamentos</li></a>
                    <a href=""><li><i class="far fa-file-alt"></i>Blog</li></a>
                </ul>
            </div>  
            <div class="col-6 navegacao-ususario text-right">
                <ul>
                    <li>Frederico Drumond</li>
                    <li><i class="fas fa-bell"></i></li>
                    <li><i class="fas fa-cog"></i></li>
                    <li><i class="fas fa-sign-out-alt"></i></li>
                </ul>
            </div>
        </div>      
    </div>

    <div class="container-fluid">
        @yield('content')
    </div>

    <script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script src="{{ asset('alertfy/alertify.min.js') }}"></script>

    <script>

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>

    <script>
        @yield('script')
    </script>
    
</body>
</html>
