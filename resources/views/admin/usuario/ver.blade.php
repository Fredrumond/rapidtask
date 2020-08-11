@extends('layouts.admin')
@section('content')

<div class="perfil">
    @panelHeader([
        'title' => 'Meu Perfil'
        ])
    @endpanelHeader
    @if($errors->all())
        @foreach($errors->all() as $error)
            @message(['color' => 'red','msg' => $error])@endmessage
        @endforeach
    @endif
    @if(session()->exists('message'))
        @message(['color' => session()->get('color'),'msg' => session()->get('message')])@endmessage
    @endif
    <div class="left">
        <div class="form-box">
            <form id="form-atualiza-perfil" action="{{ route('admin.perfil-atualizar')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <label>
                    <span class="field">Nome</span>
                    <input type="hidden" name="usuario_id" id="usuario_id" value="{{$user->id}}">
                    <input type="text" name="name" id="name" value="{{$user->name}}">
                </label>
                <label>
                    <span class="field">Avatar</span>
                    <input type="file" name="avatar">
                </label>
                <label>
                    <span class="field">Senha</span>
                    <input type="password" name="senha" id="senha">
                </label>
                <label>
                    <span class="field">Repetir senha</span>
                    <input type="password" name="repita_senha" id="repita-senha">
                </label>
                <div class="actions">
                    <button class="button button-orange">Atualizar</button>
                </div>
                
            </form>
        </div>
    </div>
</div>

@endsection
@section('script')
<script type="text/javascript">
    $(document).ready(function() {



    });
</script>
@endsection