<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RAPIDTASK</title>

    <!-- BOOTSTRAP -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <!-- FONTAWESOME -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
    <!-- ALERTFY-->
    <link rel="stylesheet" href="{{ asset('alertfy/css/alertify.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('alertfy/css/themes/default.min.css') }}" />
    <!-- STYLE --> 
    <link rel="stylesheet" href="/css/tema.css">
    <!-- <link rel="stylesheet" href="/css/style.css"> -->
    <link rel="stylesheet" href="/css/global.css">   
    <link rel="stylesheet" href="/css/main.css">   
</head>
<body>

    <div class="topbar">
        <nav class="navbar-custom">
            <div class="menu-custom">
                <ul class="navbar-left d-flex list-inline float-left mb-0">
                    <li class="active">
                        <a href="{{ route('admin.dashboard') }}" class="waves-effect">
                            <i class="fas fa-home"></i>                            
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li><a href="{{ route('admin.projetos') }}" class="waves-effect"><i class="fas fa-folder-open"></i><span> Projetos</span></a></li>
                    <li><a href="{{ route('admin.tarefas') }}" class="waves-effect"><i class="far fa-calendar-check"></i><span> Tarefas</span></a></li>
                    <li><a href="{{ route('admin.clientes') }}" class="waves-effect"><i class="fas fa-users"></i><span> Clientes</span></a></li>
                    <li><a href="{{ route('admin.times') }}" class="waves-effect"><i class="fas fa-bezier-curve"></i><span> Times</span></a></li>
                    <li><a href="#" class="waves-effect"><i class="fas fa-comment-dollar"></i><span> Orçamentos</span></a></li>
                </ul>
            </div>
            <ul class="navbar-right d-flex list-inline float-right mb-0">
                <li class="dropdown notification-list">
                    <a class="nav-link dropdown-toggle arrow-none waves-effect" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                        <i class="fas fa-bell noti-icon"></i>                        
                        <span class="badge badge-pill badge-danger noti-icon-badge">3</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg" style="">
                        <!-- item-->
                        <h6 class="dropdown-item-text">Notificações (258)</h6>
                        <div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: 443.594px;">
                            <div class="slimscroll notification-item-list" style="overflow: hidden; width: auto; height: 443.594px;">
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item notify-item active">
                                    <div class="notify-icon bg-success"><i class="mdi mdi-cart-outline"></i></div>
                                    <p class="notify-details">Your order is placed<span class="text-muted">Dummy text of the printing and typesetting industry.</span></p>
                                </a>
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item notify-item">
                                    <div class="notify-icon bg-warning"><i class="mdi mdi-message-text-outline"></i></div>
                                    <p class="notify-details">New Message received<span class="text-muted">You have 87 unread messages</span></p>
                                </a>
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item notify-item">
                                    <div class="notify-icon bg-info"><i class="mdi mdi-glass-cocktail"></i></div>
                                    <p class="notify-details">Your item is shipped<span class="text-muted">It is a long established fact that a reader will</span></p>
                                </a>
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item notify-item">
                                    <div class="notify-icon bg-primary"><i class="mdi mdi-cart-outline"></i></div>
                                    <p class="notify-details">Your order is placed<span class="text-muted">Dummy text of the printing and typesetting industry.</span></p>
                                </a>
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item notify-item">
                                    <div class="notify-icon bg-danger"><i class="mdi mdi-message-text-outline"></i></div>
                                    <p class="notify-details">New Message received<span class="text-muted">You have 87 unread messages</span></p>
                                </a>
                            </div>
                            <div class="slimScrollBar" style="background: rgb(158, 165, 171); width: 5px; position: absolute; top: 0px; opacity: 0.4; display: block; border-radius: 7px; z-index: 99; right: 1px;"></div>
                            <div class="slimScrollRail" style="width: 5px; height: 100%; position: absolute; top: 0px; display: none; border-radius: 7px; background: rgb(51, 51, 51); opacity: 0.2; z-index: 90; right: 1px;"></div>
                        </div>
                        <!-- All--><a href="javascript:void(0);" class="dropdown-item text-center text-primary">Ver mais <i class="fi-arrow-right"></i></a></div>                    
                    </li>

                    <li class="dropdown notification-list">
                        <div class="dropdown notification-list nav-pro-img">
                            <a class="dropdown-toggle nav-link arrow-none waves-effect nav-user" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                                <img src="https://s3-us-west-2.amazonaws.com/sebrae-canvas/profile/b4cc67a2150348879f3f980aef88307a/2016/11/01/f1ec5be8311944a89fc37914ad103121.jpg" alt="user" class="rounded-circle">
                            </a>
                            <div class="dropdown-menu dropdown-menu-right profile-dropdown">                           
                                <a class="dropdown-item" href="{{ route('admin.perfil') }}"><i class="mdi mdi-account-circle m-r-5"></i> Perfil</a> 
                                <a class="dropdown-item" href="#"><i class="mdi mdi-wallet m-r-5"></i>Configuraões</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                                    onclick="event.preventDefault();
                                    document.getElementById('logout-form').submit();">
                                    <i class="mdi mdi-power text-danger"></i> Sair
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        </div>
                    </li>                    
                </ul>

            </nav>        
        </div>
        
        <div class="content-page">
            <div class="content">
                <div class="container-fluid">
                   @yield('content')                
               </div>
           </div>
       </div>


       <script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
       <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
       <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
       <!-- <script type="text/javascript" src="{{ asset('js/main.js') }}"></script> -->
       <script src="{{ asset('alertfy/alertify.min.js') }}"></script>

       <script>

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        //VALIDAÇAO DE FORMULARIOS
        function validateForm (errors) {
            Object.keys(errors).forEach(function(item){
                $(`#${item}`).addClass("invalid");
                $(`#${item}`).next().css({ "display": "block" }).append(errors[item])
            });
        }

        //CHAMADA AJAX
        
        function httpRequest(params){
            const { method, endPoint, dataType, data, redirect } = params
            
            $.ajax({
			url: endPoint,
			type: method,
			dataType: dataType,
			data: data,
            })
            .done(function(response) {  
                if(redirect.param){
                    urlRedirect = `${redirect.url}/${response.id}`
                    window.location.replace(urlRedirect);
                } else {
                    window.location.replace(redirect.url);
                }                      
            })
            .fail(function(error) {
                if(error.status == 422){
                    validateForm(error.responseJSON.errors)
                }
            })
        }
    </script>


    @yield('script')


</body>
</html>
