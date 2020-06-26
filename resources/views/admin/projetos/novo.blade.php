@extends('layouts.admin')
@section('content')
<div class="row ">
	<div class="col-12 projetos">
		<h4>Novo Projeto</h4>
		<form id="form-projeto">
			@csrf
			<div class="form-row">
				<div class="form-group col-md-3">
					<label for="inputEmail4">Nome</label>
					<input type="text" class="form-control" name="nome" id="nome">
				</div>
				<div class="form-group col-md-1">
					<label for="inputEmail4">Sigla</label>
					<input type="text" class="form-control" name="sigla" id="sigla">
				</div>
                <div class="form-group col-md-4">
                    <label for="inputState">Time</label>
                    <select class="form-control" name="time_id" id="time_id">                             
                        @foreach($timeMembro as $tm)
                        <option value="{{ $tm->time_id }}">{{ $tm->time[0]->nome }}</option>
                        @endforeach                                         
                    </select>
                </div>
                <div class="form-group col-md-4">
                   <label for="inputState">Cliente</label>
                   <select class="form-control" name="cliente_id" id="cliente_id">								
                      @foreach($clientes as $cliente)
                      <option value="{{ $cliente->id }}">{{ $cliente->nome }}</option>
                      @endforeach											
                  </select>
              </div>
          </div>
          <div class="form-group">
            <label for="descricao">Descrição</label>
            <textarea class="form-control" id="descricao" rows="3" name="descricao" id="descricao"></textarea>
        </div>						
        <button type="submit" class="btn btn-primary">Criar</button>
    </form>
</div>
</div>
@endsection
@section('script')
<script type="text/javascript" src="{{ asset('js/projeto-main.js') }}"></script>
@endsection