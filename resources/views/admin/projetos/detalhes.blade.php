@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="panel-header">
			<div class="panel-title">
                <h4>{{$projeto->nome}} - {{$projeto->sigla}} / {{$projeto->cliente->nome}}</h4>
			</div>
			<div class="panel-action project-details">
                <div>
                    <i class="far fa-calendar-alt"></i>
                    @if(isset($projeto->dt_inicio) && isset($projeto->dt_prevista))
                    {{date('d/m/Y', strtotime($projeto->dt_inicio))}} - {{date('d/m/Y', strtotime($projeto->dt_prevista))}}
                    @else
                    <span>Sem data programada</span>
                    @endif
                </div>
                @if($atraso > 0)
                    <div>
                        <i class="far fa-clock"></i>
                        <span style="background: red; color: #fff;padding: 0px 3px;">{{$atraso}} dias atrasado</span>
                    </div>
                @endif
                <div>
                    <i class="fas fa-chart-line"></i>{{$progressoProjeto}}%
                </div>
                <div>
                    <i class="fas fa-cog config-projeto cursor" data-id="{{$projeto->id}}"></i>
                </div>
			</div>
		</div>
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
            <div class="box-btn">
               <a class="btn btn-success" href="{{ route('admin.nova-tarefa') }}">Nova Tarefa</a>
           </div>
            <div class="board">
                <div class="item">
                    <div class="title news">Novas</div>
                    @foreach ($tarefasNovas as $nova)
                        <div class="card-board cursor ver-detalhes-tarefa" data-id="{{$nova->id}}">
                            <div class="card-badge">
                            <?php 
                                if ($nova->prioridade_id == 1) {
                                    echo '<span class="badge badge-info">Baixo</span>';
                                }
                                if ($nova->prioridade_id == 2) {
                                    echo '<span class="badge badge-secondary">Normal</span>';
                                }
                                if ($nova->prioridade_id == 3) {
                                    echo '<span class="badge badge-warning">Urgente</span>';
                                }
                                if ($nova->prioridade_id == 4) {
                                    echo '<span class="badge badge-danger">Imediato</span>';
                                }
                            ?>         
                            </div>
                            <span>{{$nova->titulo}}</span>
                        </div>
                    @endforeach  
                </div>
                <div class="item">
                    <div class="title inProgress">Em andamento</div>
                    @foreach ($tarefasAndamento as $andamento) 
                        <div class="card-board cursor ver-detalhes-tarefa" data-id="{{$nova->id}}">
                            <div class="card-badge">
                            <?php 
                                if ($andamento->prioridade_id == 1) {
                                    echo '<span class="badge badge-info">Baixo</span>';
                                }
                                if ($andamento->prioridade_id == 2) {
                                    echo '<span class="badge badge-secondary">Normal</span>';
                                }
                                if ($andamento->prioridade_id == 3) {
                                    echo '<span class="badge badge-warning">Urgente</span>';
                                }
                                if ($andamento->prioridade_id == 4) {
                                    echo '<span class="badge badge-danger">Imediato</span>';
                                }
                            ?>         
                            </div>
                            <span>{{$andamento->titulo}}</span>
                        </div>
                    @endforeach  
                </div>
                <div class="item">
                    <div class="title waiting">Em espera</div>
                    @foreach ($tarefasEspera as $espera)
                        <div class="card-board cursor ver-detalhes-tarefa" data-id="{{$nova->id}}">
                            <div class="card-badge">
                            <?php 
                                if ($espera->prioridade_id == 1) {
                                    echo '<span class="badge badge-info">Baixo</span>';
                                }
                                if ($espera->prioridade_id == 2) {
                                    echo '<span class="badge badge-secondary">Normal</span>';
                                }
                                if ($espera->prioridade_id == 3) {
                                    echo '<span class="badge badge-warning">Urgente</span>';
                                }
                                if ($espera->prioridade_id == 4) {
                                    echo '<span class="badge badge-danger">Imediato</span>';
                                }
                            ?>         
                            </div>
                            <span>{{$espera->titulo}}</span>
                        </div>
                    @endforeach  
                </div>
                <div class="item">
                    <div class="title done">Finalizadas</div>
                    @foreach ($tarefasConcluida as $concluida)
                        <div class="card-board cursor ver-detalhes-tarefa" data-id="{{$nova->id}}">
                            <div class="card-badge">
                            <?php 
                                if ($concluida->prioridade_id == 1) {
                                    echo '<span class="badge badge-info">Baixo</span>';
                                }
                                if ($concluida->prioridade_id == 2) {
                                    echo '<span class="badge badge-secondary">Normal</span>';
                                }
                                if ($concluida->prioridade_id == 3) {
                                    echo '<span class="badge badge-warning">Urgente</span>';
                                }
                                if ($concluida->prioridade_id == 4) {
                                    echo '<span class="badge badge-danger">Imediato</span>';
                                }
                            ?>         
                            </div>
                            <span>{{$concluida->titulo}}</span>
                        </div>
                    @endforeach  
                </div>
            </div>
        </div>
        <div id="passo2" class="tab-pane fade">
            <a class="btn btn-success adicionar-arquivo-projeto">Adicionar arquivo</a>
            <div class="board-tarefas">
                <table class="table tabela-arquivos">
                    <thead class="text-center">
                        <tr>
                            <th scope="col">Nome</th>
                            <th scope="col">Descrição</th>
                            <th scope="col">Data</th>
                            <th scope="col">Acões</th>                        
                        </tr>
                    </thead>
                    <tbody class="tabela-arquivos-lista">
                        <tr class="text-center tarefa-arquivada">
                            <td><button class="btn btn-danger excluir-tarefa" data-id="{{$projeto->id}}">Excluir</button></td>                               
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>
        <div id="passo3" class="tab-pane fade">
            <h1>Anotações</h1>
            <div class="anotacoes-projeto">
                <div class="box-btn">
                    <button class="btn btn-success nova-anotacao">Inserir nota</button>   
                </div>
                <div class="box-anotacao">
                    <form id="form-anotacao">
                        @csrf
                        <label>Anotação</label>
                        <input type="hidden" name="projeto_id" id="projeto_id" value="{{$projeto->id}}">
                        <textarea class="form-control" rows="3" name="anotacao" id="anotacao"></textarea>
                        <button type="submit" class="btn btn-primary">Enviar</button>
                        <button type ="button" class="btn btn-primary cancelar-anotacao">Cancelar</button>
                    </form>
                </div>              
                <ol class="activity-feed timeline-anotacoes">                 
                </ol>
            </div>
        </div>
        <div id="passo4" class="tab-pane fade">
            <h1>Passo 4</h1>
        </div>
    </div>
</div>
</div>

<a href="/projetos/arquivos/694daae6b86a4436e213679afbce8220.pdf" target="_blank"></a>


<div class="modal" id="adicionarArquivoProjetoModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Adicionar arquivo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form-arquivo-projeto" method="post" enctype="multipart/form-data">
                    @csrf                       
                    <input type="hidden" name="projeto_id" id="projeto_id" value="{{$projeto->id}}">
                    <label for="inputEmail4">Nome</label>
                    <input type="text" class="form-control" name="nome" id="nome">
                    <label for="inputEmail4">Descricao</label>
                    <textarea class="form-control" rows="3" name="descricao" id="descricao"></textarea>
                    <label for="inputEmail4">Arquivo</label><br>
                    <input type="file" name="arquivo" id="arquivo"><br><br>
                    <button type="submit" class="btn btn-primary">Enviar</button>                    
                </form>
            </div>            
        </div>
    </div>
</div>

<div class="modal" id="editarAnotacaoModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar anotação</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form-anotacao-editar">
                    @csrf                       
                    <input type="hidden" name="anotacao_id" id="anotacao_id">
                    <textarea class="form-control" rows="3" name="anotacaoEditar" id="anotacaoEditar"></textarea>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                    <button type ="button" class="btn btn-primary cancelar-anotacao-editar">Cancelar</button>
                </form>
            </div>            
        </div>
    </div>
</div>
@endsection
@section('script')
<script type="text/javascript" src="{{ asset('js/projeto-detalhes.js') }}"></script>
@endsection