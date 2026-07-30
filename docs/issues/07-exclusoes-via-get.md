# [ALTO] Exclusões via GET vulneráveis a CSRF

## Severidade
Alto / Segurança

## Descrição
Várias rotas de exclusão usam GET (`/tarefa/excluir`, `/projeto/excluir`, `/cliente/excluir`, etc.). Um atacante pode forçar exclusão via `<img src="...">` se a vítima estiver logada.

## Critério de aceite
- Mutações via POST/DELETE com token CSRF
- Links de exclusão substituídos por forms
