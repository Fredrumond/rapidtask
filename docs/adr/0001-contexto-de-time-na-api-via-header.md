# ADR 0001 — Contexto de time na API via header

**Status:** Superseded by [ADR 0003](0003-token-api-por-conta-e-time-id.md)

**Data:** 04/08/2026

## Contexto

A UI web resolve o tenant com sessão (`CurrentTeam` + middleware `SetCurrentTeam`). A API usa Sanctum Bearer e **não** carrega o middleware de sessão de time. Endpoints multi-tenant (ex.: CRUD de tarefas) precisam de um `time_id` explícito e compatível com `current_time_id()`, `TeamScope` e policies (`canAccessCurrentTeam`).

## Opções consideradas

1. **Query string / body** — `?time_id=1` ou campo no JSON  
2. **Header HTTP** — `X-Time-Id` obrigatório nas rotas de domínio  
3. **Time embutido no token Sanctum** — abilities/claims por time no token  
4. **Reutilizar só a sessão web na API** — dependência de cookie/sessão stateful

## Decisão

Usar o header **`X-Time-Id`** como contrato de tenant da API, validado pelo middleware **`EnsureApiTeam`** (membership via `belongsToTime`), e hidratar o contexto com **`CurrentTeam::setForRequest()`** (override request-scoped, prioridade sobre a sessão). Rotas de domínio API ficam atrás de `auth:sanctum` + `api.team`.

> **Nota:** Esta decisão foi substituída pelo ADR 0003 (token por Conta; recurso `/tarefas` com `time_id` na query/body).

## Justificativa

- Separa autenticação (Bearer) do tenant (header), permitindo o mesmo token em vários times do usuário.  
- Não mistura `time_id` com o payload de create/update.  
- Reaproveita scopes e policies existentes sem reescrever o isolamento.  
- Evita API stateful baseada em sessão.

## Consequências

### Benefícios

- Padrão único para novos endpoints API multi-tenant.  
- Isolamento alinhado ao domínio web (`current_time_id()` / `TeamScope`).  
- Contrato explícito e documentável (Swagger).

### Riscos

- Clientes que omitirem o header recebem 400; membership inválida, 403.  
- Override estático request-scoped exige limpeza ao fim da request (já feita no middleware).

### Débitos técnicos

- `CurrentTeam` concentra sessão web e override de API; evolução futura pode migrar para `Context`/container se o padrão crescer.

### Próximos passos (opcional)

- Documentar o padrão em `docs/arquitetura.md` / `docs/QUICK_START_GUIDE.md` para API.  
- Reutilizar `EnsureApiTeam` em todo recurso API com isolamento por time.
