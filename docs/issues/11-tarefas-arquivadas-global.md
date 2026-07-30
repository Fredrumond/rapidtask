# [ALTO] `verTarefasArquivadas` lista tarefas de todos os times

## Severidade
Alto / Segurança / Privacidade

## Descrição
`TarefasController@verTarefasArquivadas` usa `Tarefas::where('status',1)->get()` sem filtro de time.

## Critério de aceite
- Listagem escopada ao time do usuário
- Cobertura no suite cross-tenant
