# [ALTO] Concatenação de input do request em SQL

## Severidade
Alto / Segurança

## Descrição
Parâmetros de request concatenados em strings SQL:
- `TarefaComentarioController.php` — `tarefa_id`
- `ProjetosAnotacoesController.php` — `projeto_id`
- `TarefaHistoricoController.php` — `tarefa_id`

## Critério de aceite
- Bindings parametrizados ou Eloquent
- Sem concatenação de input em SQL
