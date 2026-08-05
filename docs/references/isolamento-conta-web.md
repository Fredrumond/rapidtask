# Referência: Isolamento de conta (web)

Índice

- [1. Contexto de conta na sessão](#1-contexto-de-conta-na-sessão)
- [2. Filtro de times por conta e membership](#2-filtro-de-times-por-conta-e-membership)
- [3. Autorização que exige conta ativa](#3-autorização-que-exige-conta-ativa)
- [4. Nome da conta na navegação](#4-nome-da-conta-na-navegação)

Discovery: [`0003`](../discovery/0003-conta-saas-modelo-de-dados.md) · [`0004`](../discovery/0004-conta-saas-registro-onboarding-sessao.md) · [`0005`](../discovery/0005-conta-saas-propagacao-tenant-isolamento.md)  
ADR: [`0002 — Conta como tenant externo`](../adr/0002-conta-como-tenant-externo.md)  
Arquitetura: [`docs/arquitetura.md`](../arquitetura.md) (seção multi-tenant)

A fronteira externa é `conta_id`; o `time_id` continua como unidade operacional. Entidades folha (Cliente, Projeto, Tarefa) isolam pela cadeia `conta → time → entidade`, sem `conta_id` na tabela.

---

## 1. Contexto de conta na sessão

| | |
|---|---|
| **Comando / entrada** | `CurrentTeam::set($timeId)` / middleware `SetCurrentTeam` / helpers `current_conta_id()`, `current_time_id()` |
| **Quando** | Login e navegação web autenticada; troca de time no nav |
| **Monitor** | Não |
| **Log** | N/A |
| **Código** | `app/Support/CurrentTeam.php`, `app/Support/helpers.php`, `app/Http/Middleware/SetCurrentTeam.php` |

**Para que serve**

Mantém na sessão o time ativo e a conta derivada dele, para scopes, policies e UI usarem o mesmo tenant sem o usuário escolher conta à parte.

**Como funciona**

1. Com usuário logado e sem `current_time_id`, `SetCurrentTeam` escolhe o primeiro time do usuário.
2. `CurrentTeam::set($timeId)` grava `current_time_id` e resolve `conta_id` do time (sem global scopes).
3. A sessão fica com `current_conta_id` alinhado ao time ativo.
4. `switchTeam()` no nav chama `CurrentTeam::set` de novo e atualiza a conta se o time mudar.
5. Helpers `current_time_id()` e `current_conta_id()` leem sessão (ou override de request na API).

**Rodar na mão**

```bash
# Com a app no ar: registre-se, abra o painel e inspecione a sessão
# (current_time_id / current_conta_id) após criar o primeiro time.
docker compose exec app php artisan tinker --execute="echo 'use o painel web autenticado';"
```

---

## 2. Filtro de times por conta e membership

| | |
|---|---|
| **Comando / entrada** | Global scope `ScopedToMemberTeams` → `TeamScope::applyMemberFilter` |
| **Quando** | Qualquer query Eloquent em `Time` com usuário autenticado |
| **Monitor** | Não |
| **Log** | N/A |
| **Código** | `app/Models/Concerns/ScopedToMemberTeams.php`, `app/Models/Concerns/TeamScope.php` |

**Para que serve**

Garante que listagens e bindings de time mostrem só times da conta ativa **e** dos quais o usuário é membro (AND), evitando vazamento cross-conta mesmo com membership indevido.

**Como funciona**

1. `Time` usa o trait `ScopedToMemberTeams`.
2. O scope exige membership (`whereHas('membros', usuario_id)`).
3. Se `current_conta_id()` existir, aplica também `where conta_id = current_conta_id`.
4. Não restringe ao time ativo: a listagem de times da conta precisa ver todos os memberships.
5. Folhas (Cliente/Projeto/Tarefa) seguem filtrando por `time_id` / membership; a conta entra pela cadeia e pelas policies.

**Rodar na mão**

```bash
docker compose exec app php artisan test --filter='applyMemberFilter'
```

---

## 3. Autorização que exige conta ativa

| | |
|---|---|
| **Comando / entrada** | `HandlesTeamAuthorization::canAccessTeam` / `canAccessCurrentTeam` (policies de Cliente, Projeto, Tarefa, Time, …) |
| **Quando** | `authorize` / Gate em ações web sobre recursos de time |
| **Monitor** | Não |
| **Log** | N/A (falha vira 403; sem log dedicado) |
| **Código** | `app/Policies/Concerns/HandlesTeamAuthorization.php` |

**Para que serve**

Complementa o scope: mesmo que o usuário seja membro do time, a ação só passa se o time pertencer à `current_conta_id` da sessão. Sem conta na sessão, o acesso é negado.

**Como funciona**

1. Policy chama `canAccessTeam($user, $timeId)`.
2. Sem membership → nega.
3. Sem `current_conta_id()` → nega (evita autorização ambígua).
4. Carrega `conta_id` do time sem global scopes e compara com a sessão.
5. `canAccessCurrentTeam` exige time **e** conta na sessão antes de delegar a `canAccessTeam`.

**Rodar na mão**

```bash
docker compose exec app php artisan test --filter=TenantIsolationTest
```

---

## 4. Nome da conta na navegação

| | |
|---|---|
| **Comando / entrada** | Layout autenticado / `resources/views/livewire/layout/navigation.blade.php` |
| **Quando** | Toda página web autenticada com layout app |
| **Monitor** | Não |
| **Log** | N/A |
| **Código** | `resources/views/livewire/layout/navigation.blade.php` |

**Para que serve**

Mostra qual conta está ativa (desktop e mobile), sem oferecer troca de conta na UI nesta fase.

**Como funciona**

1. Resolve `Conta` por `current_conta_id()`.
2. No desktop, exibe o nome perto do seletor de time e no dropdown do usuário.
3. No mobile, exibe o nome na área de perfil do menu responsivo.
4. Link “Conta” (edição do nome) continua disponível ao owner via `contas.edit`.

**Rodar na mão**

```bash
# Abra http://localhost:8080 autenticado e confira o nome da conta no header
# (desktop) e no menu hamburger (mobile).
docker compose exec app php artisan test --filter='navigation menu can be rendered'
```
