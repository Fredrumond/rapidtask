# Discovery: Comentários de tarefa (UI + API)

## 1. Resumo

O sistema possui infraestrutura de domínio completa para comentários de tarefa (`TarefaComentario` model, `TarefaComentarioPolicy`, `TarefaComentarioFactory`, soft delete), mas a interface visual foi perdida em algum ponto da migração. A regra de negócio é: qualquer membro do time pode criar e listar comentários de uma tarefa; apenas o autor pode editar ou excluir o próprio comentário. Os dados ficam isolados por time e por tenant (conta SaaS). A entrega inclui a UI Livewire Volt na tela de visualização da tarefa e endpoints REST na API.

## 2. Objetivo

Restaurar o CRUD de comentários com UI funcional na tela de visualização da tarefa, expor os mesmos endpoints via API e garantir isolamento de dados por time e conta SaaS em todos os fluxos.

## 3. Escopo

### Dentro

- Listar comentários da tarefa (timeline, mais recentes no topo)
- Criar comentário (qualquer membro do time)
- Editar comentário (somente o autor)
- Excluir comentário (somente o autor)
- UI via Livewire Volt na tela de **visualização** da tarefa
- Endpoints REST equivalentes na API (sob `auth:sanctum` + `api.team`)
- Testes básicos do padrão do sistema
- Teste de isolamento cross-time/conta (web e API)

### Fora

- @menções a usuários
- Anexos em comentários
- Notificações de novo comentário
- Histórico de edição de comentário
- Feature flag

## 4. Premissas

- Feature flag: **Não** — liberação direta
- A tabela `tarefa_comentario` existe na migration `0001_01_01_000050_create_tarefas_tables.php` (campos: `tarefa_id`, `usuario_id`, `comentario`, timestamps, soft delete); não é necessária nova migration
- O modelo `TarefaComentario` e o trait `BelongsToTeamViaTarefa` já existem e funcionam
- O middleware `api.team` já resolve o time a partir do token Sanctum — reutilizável nos novos endpoints
- A âncora da UI é a tela de **visualização** da tarefa (nova página `show`, já que a atual `edit.blade.php` é exclusiva para edição dos campos da tarefa)

## 5. Considerações de segurança

- **Dados sensíveis:** Comentários são texto livre associado a tarefas internas do time; sem PII explícito, mas o conteúdo deve permanecer isolado por tenant
- **Autenticação/Autorização:** Ponto crítico — a `TarefaComentarioPolicy` atual autoriza `update` e `delete` pelo acesso ao time, **não pelo autor**; a policy precisa ser ajustada para verificar `usuario_id == auth()->id()` nas ações de edição e exclusão antes da entrega
- **Exposição de APIs:** Novos endpoints sob o grupo `auth:sanctum` + `api.team` já existente — padrão alinhado com o CRUD de tarefas
- **Compliance:** Isolamento cross-tenant já presente via `BelongsToTeamViaTarefa`; deve ser coberto pelos testes de isolamento exigidos
- **Outras considerações:** Soft delete já está no model; certificar que exclusão via API também usa soft delete (não hard delete)

## 6. Dúvidas

Nenhuma pendente — todas as dúvidas levantadas no Discovery foram respondidas antes da persistência do documento.

## 7. Informações ausentes

Nenhuma — contexto suficiente para planejamento.

## 8. Status

**Pronto para Planejamento?** Sim

Escopo, regras de autorização, âncora da UI, migration e estratégia de entrega confirmados. A única ação pré-implementação é o ajuste na `TarefaComentarioPolicy` (update/delete por autoria, não apenas por acesso ao time), já mapeada nas considerações de segurança.
