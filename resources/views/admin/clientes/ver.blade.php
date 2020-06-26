@extends('layouts.admin')
@section('content')
<div class="row ">
	<div class="col-12 clientes">    
		<h4>{{$cliente->nome}}</h4>
    @if($cliente->usuario_id == Auth::user()->id || $nivel[0]->nivel_id == 1)
    <div class="box-btn">
     <button class="btn btn-danger excluir-cliente" data-id="{{$cliente->id}}">Excluir</button>	
   </div>
   @endif			
   <form id="form-atualiza-cliente">
     @csrf
     <div class="form-row">
      <input type="hidden" name="cliente_id" id="cliente_id" value="{{$cliente->id}}">
      <div class="form-group col-md-4">
       <label for="nome">Nome</label>
       <input type="text" class="form-control" name="nome" id="nome" value="{{$cliente->nome}}">
     </div>
     <div class="form-group col-md-4">
       <label for="nome">E-mail</label>
       <input type="text" class="form-control" name="email" id="email" value="{{$cliente->email}}">
     </div>
     <div class="form-group col-md-4">
       <label for="nome">Telefone</label>
       <input type="text" class="form-control" name="telefone" id="telefone" value="{{$cliente->telefone}}">
     </div>
   </div>
   @if($cliente->usuario_id == Auth::user()->id || $nivel[0]->nivel_id == 1)
   <button type="submit" class="btn btn-primary">Salvar</button>
   @endif  
 </form>
</div>
</div>
<div class="row ">
  <div class="col-12 projetos">
    <h4>Projetos</h4>
    <div class="box-btn">
      <a class="btn btn-success" href="{{ route('admin.novo-projeto') }}">Novo Projeto</a>
    </div>
    <div class="board-projetos">
      <table class="table">
        <thead class="text-center">
          <tr>
            <th scope="col">Ref</th>
            <th scope="col">Nome</th>            
          </tr>
        </thead>
        <tbody>
          @foreach ($projetos as $projeto)
          <tr class="text-center ver-detalhes-projeto" data-id="{{$projeto->id}}">
            <th scope="row">{{$projeto->id}}</th>
            <td>{{$projeto->nome}}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection
@section('script')
<script type="text/javascript" src="{{ asset('js/cliente-main.js') }}"></script>
@endsection