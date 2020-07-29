@extends('layouts.admin')
@section('content')

<div class="row ">
	<div class="col-12 atividades">
		@panelHeader([
			'title' => 'Atividades'
			])
		@endpanelHeader
		<div class="board-atividades">
			<table class="table">
				<thead class="text-center">
					<tr>
                        <th scope="col">Ação</th>
                        <th scope="col">Tipo</th>
						<th scope="col">Data</th>						
					</tr>
				</thead>
				<tbody>
                @foreach ($logs as $log)
                    <tr class="text-center cursor">
                        <th scope="row">{{$log->acao}}</th>
                        <td>{{$log->tipo}}</td>
                        <td>{{$log->data}}</td>
                    </tr>
                @endforeach
				</tbody>
			</table>
		</div>
	</div>
</div>
@endsection