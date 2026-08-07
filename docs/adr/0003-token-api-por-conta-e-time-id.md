# ADR 0003 — Token de API por Conta e time_id na query/body

**Data:** 06/08/2026

**Supersede:** [ADR 0001 — Contexto de time na API via header](0001-contexto-de-time-na-api-via-header.md)

## Contexto

Com a conta como tenant SaaS externo ([ADR 0002](0002-conta-como-tenant-externo.md)), o contrato anterior (token pessoal do `User` + header `X-Time-Id`) não alinhava autenticação ao tenant. A API precisa autenticar a **conta**, isolar recursos entre contas e informar o time operacional sem body em GET, mantendo o recurso REST centrado em `/tarefas`.

## Opções consideradas

1. Manter token por usuário + header `X-Time-Id` (status quo / ADR 0001)
2. Token por conta + `time_id` apenas no body (incluindo GET)
3. Token por conta + aninhar GETs sob `/times/{time_id}/tarefas`
4. Token por conta + recurso `/tarefas` com `time_id` em query (GET) e body (mutações)

## Decisão

Adotar a opção **4**:

- Bearer Sanctum autentica o model `Conta` (`HasApiTokens`)
- Um token ativo por conta (gerar novo revoga o anterior)
- Gestão do token nas configurações da conta (owner)
- Recurso único: `/api/tarefas` e `/api/tarefas/{tarefa_id}`
- `GET` — `time_id` obrigatório na **query string** (sem body)
- `POST/PUT/DELETE` — `time_id` obrigatório no **body**
- Middleware `EnsureApiTeam` valida pertencimento `Time.conta_id === Conta.id` e hidrata `CurrentTeam::setForRequest()`
- Cutover direto: tokens antigos por `User` invalidados; sem dual-token / feature flag

## Justificativa

- Alinha autenticação ao tenant SaaS (conta)
- Mantém o recurso REST como `tarefas` (sem misturar path com `times`)
- GET sem body segue boa prática HTTP; query string é o filtro contextual padrão
- Mantém `current_time_id()` / scopes / policies com override request-scoped
- Isolamento cross-conta como critério de aceite explícito

## Consequências

### Benefícios

- Contrato coerente com multi-tenant conta e com o recurso `tarefas`
- Um ponto de emissão de credencial por conta (owner)
- Documentação Swagger e testes Feature refletem o mesmo contrato

### Riscos

- Clientes do modelo antigo quebram no deploy (cutover aceito)
- DELETE com body exige clientes que enviem JSON no delete

### Débitos técnicos

- Policies e `TeamScope` precisam tratar sujeito `Conta` além de `User`
- Criação via API atribui `usuario_id` ao owner da conta (não há usuário na autenticação do token)
