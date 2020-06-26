@extends('layouts.admin')
@section('content')
<div class="row ">
	<div class="col-12 clientes">
		<h4>Novo cliente</h4>
		<form id="form-cliente">
			@csrf
			<div class="form-row">
				<div class="form-group col-md-3">
					<label for="nome">Nome</label>
					<input type="text" class="form-control" name="nome" id="nome">
				</div>
				<div class="form-group col-md-3">
					<label for="nome">E-mail</label>
					<input type="text" class="form-control" name="email" id="email">
				</div>
				<div class="form-group col-md-3">
					<label for="nome">Telefone</label>
					<input type="text" class="form-control" name="telefone" id="telefone">
				</div>
                <div class="form-group col-md-3">
                    <label for="inputState">Time</label>
                    <select class="form-control" name="time_id" id="time_id">                             
                        @foreach($timeMembro as $tm)
                        <option value="{{ $tm->time_id }}">{{ $tm->time[0]->nome }}</option>
                        @endforeach                                         
                    </select>
                </div>			
            </div>					
            <button type="submit" class="btn btn-primary">Salvar</button>
        </form>
    </div>
</div>
@endsection
@section('script')
<script type="text/javascript" src="{{ asset('js/cliente-main.js') }}"></script>
@endsection