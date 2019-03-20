@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="col-md-6 projeto-nome">
        <h1><i class="fas fa-book"></i>{{$projeto->nome}} - {{$projeto->sigla}} / {{$projeto->cliente->nome}}</h1>
    </div>
    <div class="col-md-6 projeto-dados">
        <ul>
            <li><i class="far fa-calendar-alt"></i>00/00/0000 - 00/00/000</li>
            <li><i class="fas fa-chart-line"></i>98%</li>
            <li><i class="fas fa-cog"></i></li>
        </ul>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#passo1">Tarefas</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" data-toggle="tab" href="#passo2">Arquivos</a>
          </li>
          <li class="nav-item">
              <a class="nav-link" data-toggle="tab" href="#passo3">Anotações</a>
          </li>
          <li class="nav-item">
              <a class="nav-link" data-toggle="tab" href="#passo4">Historico</a>
          </li>
      </ul>

      <div class="tab-content">
        <div id="passo1" class="tab-pane active">
            <div class="row container-fluid">
                <div class="card bg-primary" style="width: 20rem;">
                    <div class="card-header">Nova</div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">Permitir inserir data e tempo estimado projeto <span class="badge badge-info">Baixo</span></li>
                        <li class="list-group-item">Permitir inserir data e tempo estimado projeto <span class="badge badge-secondary">Normal</span></li>
                        <li class="list-group-item">Permitir inserir data e tempo estimado projeto <span class="badge badge-warning">Urgente</span></li>
                        <li class="list-group-item">Permitir inserir data e tempo estimado projeto <span class="badge badge-danger">Imediato</span></li>
                    </ul>                    
                </div>
                <div class="card bg-light" style="width: 20rem;">
                    <div class="card-header">Andamento</div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">Cras justo odio</li>
                        <li class="list-group-item">Dapibus ac facilisis in</li>
                        <li class="list-group-item">Vestibulum at eros</li>
                        <li class="list-group-item">Vestibulum at eros</li>
                        <li class="list-group-item">Vestibulum at eros</li>
                    </ul>                    
                </div>
                <div class="card bg-warning" style="width: 20rem;">
                    <div class="card-header">Em espera</div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">Cras justo odio</li>
                        <li class="list-group-item">Dapibus ac facilisis in</li>
                        <li class="list-group-item">Dapibus ac facilisis in</li>
                        <li class="list-group-item">Dapibus ac facilisis in</li>
                        <li class="list-group-item">Dapibus ac facilisis in</li>
                        <li class="list-group-item">Dapibus ac facilisis in</li>
                        <li class="list-group-item">Vestibulum at eros</li>
                    </ul>                    
                </div>
                <div class="card bg-success" style="width: 20rem;">
                    <div class="card-header">Finalizado</div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">Cras justo odio</li>
                        <li class="list-group-item">Dapibus ac facilisis in</li>
                        <li class="list-group-item">Vestibulum at eros</li>
                        <li class="list-group-item">Vestibulum at eros</li>
                        <li class="list-group-item">Vestibulum at eros</li>
                        <li class="list-group-item">Vestibulum at eros</li>
                        <li class="list-group-item">Vestibulum at eros</li>
                        <li class="list-group-item">Vestibulum at eros</li>
                        <li class="list-group-item">Vestibulum at eros</li>
                        <li class="list-group-item">Vestibulum at eros</li>
                        <li class="list-group-item">Vestibulum at eros</li>
                    </ul>                    
                </div>
            </div>
        </div>
        <div id="passo2" class="tab-pane fade">
            <h1>Passo 2</h1>
        </div>
        <div id="passo3" class="tab-pane fade">
            <h1>Passo 3</h1>
        </div>
        <div id="passo4" class="tab-pane fade">
            <h1>Passo 4</h1>
        </div>
    </div>
</div>
</div>

@endsection
@section('script')


@endsection