@extends('layouts.admin')
@section('content')

<div class="perfil">
    @panelHeader([
        'title' => 'Meu Perfil'
        ])
    @endpanelHeader
    <div class="left">
        <div class="form-box">
            <form id="form-atualiza-perfil" action="{{ route('admin.perfil-salvar')}}" method="POST">
                @csrf
                <label>
                    <span class="field">Nome</span>
                    <input type="hidden" name="usuario_id" id="usuario_id" value="{{$perfil->id}}">
                    <input type="text" class="form-control" name="name" id="name" value="{{$perfil->name}}">
                </label>
                <button class="button button-green">Atualizar</button>
            </form>
        </div>
    </div>
    <div class="right">
    <div class="box"></div>
    </div>
</div>



<div class="row">
	<div class="col-12 times">
		<h4>Meu Perfil</h4>
	</div>

    <div class="col-12 times">
        Informações
       <form id="form-atualiza-perfil" action="{{ route('admin.perfil-salvar')}}" method="POST">
            @csrf
            <div class="form-row">                
                <div class="form-group col-md-8">
                    <input type="hidden" name="usuario_id" id="usuario_id" value="{{$perfil->id}}">
                    <label for="inputEmail4">Nome</label>
                    <input type="text" class="form-control" name="name" id="name" value="{{$perfil->name}}">
                </div>                
            </div>    
            <button type="submit" class="btn btn-primary">Salvar</button>
        </form>
    </div>

    <div class="col-12 times">
        Avatar
      <form method="POST" action="{{ route('admin.perfil-avatar')}}"  role="form" enctype="multipart/form-data">
            @csrf
            <div class="form-row">                
                <div class="form-group col-md-8">
                    <input type="hidden" name="usuario_id" id="usuario_id" value="{{$perfil->id}}">
                    <input type="file" name="avatar">
                </div>                
            </div>    
            <button type="submit" class="btn btn-primary">Salvar</button>
        </form>
    </div>

     <div class="col-12 times">
        Redefinir senha
      <form method="POST" action="{{ route('admin.perfil-senha')}}"  role="form">
            @csrf
            <div class="form-row">                
                <div class="form-group col-md-8">
                    <input type="hidden" name="usuario_id" id="usuario_id" value="{{$perfil->id}}">
                    <label for="inputEmail4">Senha</label>
                    <input type="text" class="form-control" name="senha" id="senha">
                    <label for="inputEmail4">Repita a senha</label>
                    <input type="text" class="form-control" name="repita_senha" id="repita-senha">
                </div>                
            </div>    
            <button type="submit" class="btn btn-primary">Salvar</button>
        </form>
    </div>
</div>

@endsection
@section('script')
<script type="text/javascript">
    $(document).ready(function() {



    });
</script>
@endsection