@extends('layouts.admin')
@section('content')
<div class="row">
	<div class="col-12 times">

		<h4>Editar membro</h4>
     <div class="box-btn">
        <button class="btn btn-danger excluir-membro-time" data-id="{{$timeMembro->id}}">Excluir</button>   
    </div>     


    <form id="form-atualiza-membro-time">
     @csrf
     <div class="form-row">
        <input type="hidden" name="membro_id" id="membro_id" value="{{$timeMembro->id}}">
        <input type="hidden" name="time_id" id=time_id" value="{{$timeMembro->time_id}}">
        <div class="form-group col-md-4">
             <label for="nome">Editar nível</label>
            <select class="form-control" name="nivel_id" id="nivel_id">                             
                @foreach($niveis as $nivel)                     
                <option value="{{ $nivel->id }}" {{$timeMembro->nivel_id == $nivel->id ? 'selected="selected"' : '' }}>{{ $nivel->nome }}</option>
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
<script type="text/javascript" src="{{ asset('js/time-ver.js') }}"></script>
@endsection