# [CRÍTICO] IDOR multi-tenant: usuário acessa/altera recursos de outro time por ID

## Severidade
Crítico / Segurança

## Descrição
O isolamento por time existe apenas nas listagens (join com `time_membro`). Operações por ID usam `find()`/`findOrFail()` sem verificar membership. Um usuário do time A lê, edita e apaga tarefa, projeto, cliente, comentário e time do time B trocando o ID na URL.

## Evidências
- `TarefasController@verTarefa` — `Tarefas::find($id)`
- `ProjetosController` — `Projetos::find(...)` em ver/editar/excluir
- `ClientesController` — `Clientes::find(...)` sem escopo
- `TimeController@excluirTime` — exclusão em cascata de qualquer time
- Comentários e anotações CRUD por ID sem verificar tarefa/time

## Critério de aceite
- Toda query de recurso filtrada pelo time do usuário autenticado
- Policy + route model binding escopado
- Testes Pest cross-tenant (403/404) em cada rota
