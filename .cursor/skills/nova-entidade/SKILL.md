---
name: nova-entidade
description: >-
  Cria uma entidade CRUD no RapidTask (migration, model, policy, factory,
  páginas Volt, rotas, menu e teste cross-tenant). Use when the user asks to
  add an entity, a CRUD, a new resource, or a new screen in this codebase.
---

# Nova entidade CRUD

Copie o código existente. Não invente controller de tela nem classe Livewire PHP.

## Escolha o molde

- Tabela com `time_id` → copie **Cliente** (`app/Models/Cliente.php`, `app/Policies/ClientePolicy.php`, `resources/views/livewire/pages/clientes/`).
- Filho de projeto, sem `time_id` → copie **Tarefa** (`BelongsToTeamViaProjeto`, `TarefaPolicy`).
- Filho de tarefa → `BelongsToTeamViaTarefa`.

Detalhe que não cabe aqui: `docs/arquitetura.md`. API (Controller → Service → Repository) só se o pedido for endpoint, molde Tarefa em `docs/QUICK_START_GUIDE.md`.

## Ordem

1. Migration com a FK de tenant (`time_id` ou pai), `usuario_id` e `softDeletes()` quando o molde tiver.
2. Model: `HasFactory`, trait de tenant, `$fillable`, `casts()`, relações.
3. Policy com `HandlesTeamAuthorization`. Sem registro no provider.
4. Factory com states úteis. Seeder só para lookup fixo.
5. Volt em `resources/views/livewire/pages/{entidade}/index|create|edit`:
   - `#[Layout('layouts.app')]`
   - `$this->authorize(...)` no `mount` e nas ações
   - `$this->validate()` na página
   - create grava `usuario_id` = `auth()->id()` e `time_id` = `current_time_id()` quando a coluna existe
   - título em `<x-slot name="header">`
   - redirect com `navigate: true`; exclusão com `wire:confirm`
6. `Volt::route` no grupo `auth` de `routes/web.php`.
7. Link no menu desktop e no mobile em `resources/views/livewire/layout/navigation.blade.php`.
8. Teste com `criarCenarioDoisTimes()`: a conta B não vê nem altera o recurso da conta A. Sessão com `current_time_id` e `current_conta_id`.

## Feche com

```bash
docker compose exec app vendor/bin/pint --dirty
docker compose exec app php artisan test --filter=NomeDaEntidade
```
