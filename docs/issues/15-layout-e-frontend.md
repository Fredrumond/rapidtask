# [BAIXO] Layout: bugs de markup, responsividade e assets

- `tarefas/arquivadas.blade.php`: badge de prioridade usa `situacao_id`
- `projetos/detalhes.blade.php`: kanban usa `$nova->id` nas 3 colunas
- Layout admin sem `<meta csrf-token>` (AJAX quebra)
- Menu sem toggler mobile; kanban 4 colunas fixas; tabelas sem `table-responsive`
- `tema.css` (~4004 linhas) sem source; Vue 2 morto; Mix + assets commitados

Será resolvido na Fase 4 (Livewire + Tailwind). Ver plano de refatoração.
