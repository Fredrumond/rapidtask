@extends('layouts.admin')
@section('content')
<div class="row">
	<div class="col-12 times">
		<h4>{{$time->nome}}</h4>
       @if($time->usuario_id == Auth::user()->id)
       <div class="box-btn">
         <button class="btn btn-danger excluir-time" data-id="{{$time->id}}">Excluir</button>	
     </div>			
     <form id="form-atualiza-time">
         @csrf
         <div class="form-row">
            <input type="hidden" name="time_id" id="time_id" value="{{$time->id}}">
            <div class="form-group col-md-4">
               <label for="nome">Nome</label>
               <input type="text" class="form-control" name="nome" id="nome" value="{{$time->nome}}">
           </div>
       </div>
       <button type="submit" class="btn btn-primary">Salvar</button>
   </form>
   @endif
</div>

<div class="col-12 times">
    <h4>Membros time</h4>
    @if($time->usuario_id == Auth::user()->id)
    <div class="box-btn">
        <button class="btn btn-success adicionar-membro-time" data-id="{{$time->id}}">Adicionar membro</button>    
    </div>
    @endif
    <table class="table">
        <thead class="text-center">
            <tr>                    
                <th scope="col">Nome</th>                       
                <th scope="col">Nivel</th>                       
            </tr>
        </thead>
        <tbody>
            @foreach ($timeMembro as $tm)                             
            <tr class="text-center ver-detalhes-membro-time" data-id="{{$time->id}}" data-membro="{{$tm->membro[0]->id}}">
                <th>{{$tm->membro[0]->name}}</th>
                <td>{{$tm->nivel[0]->nome}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@if(count($timeMembroConvite) > 0)
<div class="col-12 times">
    <h4>Convites enviados</h4>        
    <table class="table">
        <thead class="text-center">
            <tr>                    
                <th scope="col">Nome</th>                       
                <th scope="col">Email</th>                       
                <th scope="col">Data envio</th>                       
            </tr>
        </thead>
        <tbody>
            @foreach ($timeMembroConvite as $tmc)                             
            <tr class="text-center">
                <th>{{$tmc->nome}}</th>
                <td>{{$tmc->email}}</td>
                <td>{{$tmc->created_at}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif
</div>
<div class="modal" id="adicionarMembroTimeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Adicionar membro</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form-membro-time">
                    @csrf                       
                    <input type="hidden" name="time" id="time" value="{{$time->id}}">
                    <label for="inputEmail4">Nome</label>
                    <input type="text" class="form-control" name="nome" id="nome">
                    <label for="inputEmail4">E-mail</label>
                    <input type="text" class="form-control" name="email" id="email">
                    <button type="submit" class="btn btn-primary">Enviar</button>
                    <button type ="button" class="btn btn-primary cancelar-memebro-time">Cancelar</button>
                </form>
            </div>
            
        </div>
    </div>
</div>
@endsection
@section('script')
<script type="text/javascript" src="{{ asset('js/time-ver.js') }}"></script>
@endsection