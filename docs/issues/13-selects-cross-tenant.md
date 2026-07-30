# [MÉDIO] Vazamento cross-tenant em formulários (`Projetos::all` / `Clientes::all`)

Formulários de tarefa/projeto carregam todos os registros do sistema sem filtro de time (`TarefasController@verTarefa`, `ProjetosController@novoProjeto`).

## Critério de aceite
Listas de select escopadas ao time do usuário.
