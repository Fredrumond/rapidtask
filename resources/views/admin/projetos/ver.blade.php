@extends('layouts.admin')
@section('content')
<div class="row ">
	<div class="col-12 tarefas">
		<h4>{{$projeto->titulo}}</h4>       
   <form id="form-atualiza-projeto">
     @csrf
     <div class="form-row">
      <input type="hidden" name="projeto_id" id="projeto_id" value="{{$projeto->id}}">
      <div class="form-group col-md-4">
       <label for="inputEmail4">Nome</label>
       <input type="text" class="form-control" name="nome" id="nome" value="{{$projeto->nome}}">
     </div>
     <div class="form-group col-md-4">
       <label for="inputEmail4">Sigla</label>
       <input type="text" class="form-control" name="sigla" id="sigla" value="{{$projeto->sigla}}">
     </div>
     <div class="form-group col-md-4">
       <label for="inputState">Cliente</label>
       <select class="form-control" name="cliente_id" id="cliente_id">								
        @foreach($clientes as $cliente)						
        <option value="{{ $cliente->id }}" {{$projeto->cliente_id == $cliente->id ? 'selected="selected"' : '' }}>{{ $cliente->nome }}</option>
        @endforeach											
      </select>
    </div>
  </div>
  <div class="form-group">
    <label for="descricao">Descrição</label>
    <textarea class="form-control" id="descricao" rows="3" name="descricao" id="descricao">{{$projeto->descricao}}</textarea>
  </div>
  <div class="form-row">                      
    <div class="form-group col-md-4">
      <label for="inputCity">Data Inicio</label>
      <input type="date" class="form-control" name="dt_inicio" id="dt_inicio" value="{{$projeto->dt_inicio}}">
    </div>
    <div class="form-group col-md-4">
      <label for="inputCity">Data Prevista</label>
      <input type="date" class="form-control" name="dt_prevista" id="dt_prevista" value="{{$projeto->dt_prevista}}">
    </div>
    <div class="form-group col-md-4">
      <label for="inputCity">Data Conclusão</label>
      <input type="date" class="form-control" name="dt_fim" id="dt_fim" value="{{$projeto->dt_fim}}">
    </div>                
  </div>
  @if($projeto->usuario_id == Auth::user()->id)
  <button class="btn btn-danger excluir-projeto" data-id="{{$projeto->id}}">Excluir</button> 
  @endif 						
  <button class="btn btn-warning voltar-projeto"  data-id="{{$projeto->id}}">Voltar</button>
  <button type="submit" class="btn btn-primary">Salvar</button> 
</form>
</div>
</div>
@endsection
@section('script')
<script type="text/javascript" src="{{ asset('js/projeto-ver.js') }}"></script>
@endsection