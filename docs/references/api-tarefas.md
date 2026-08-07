# Referência: API de tarefas

Índice

- [1. Isolamento de time na API](#1-isolamento-de-time-na-api)
- [2. CRUD de tarefas via API](#2-crud-de-tarefas-via-api)
- [3. Documentação OpenAPI (Swagger)](#3-documentação-openapi-swagger)
- [4. Token de API da conta](#4-token-de-api-da-conta)

Decisão relacionada: [`docs/adr/0003-token-api-por-conta-e-time-id.md`](../adr/0003-token-api-por-conta-e-time-id.md)  
Discovery: [`docs/discovery/0006-api-token-por-conta-saas.md`](../discovery/0006-api-token-por-conta-saas.md)

---

## 1. Isolamento de time na API

| | |
|---|---|
| **Comando / entrada** | Bearer da **Conta** + `time_id` (query nos GET; body nas mutações) + middleware `api.team` (`EnsureApiTeam`) |
| **Quando** | Em toda rota API de domínio multi-tenant (`/api/tarefas`) |
| **Monitor** | Não |
| **Log** | N/A (middleware só responde 400/403; o service loga as ações) |
| **Código** | `app/Http/Middleware/EnsureApiTeam.php`, `app/Support/CurrentTeam.php` |

**Para que serve**

Na API não há sessão de time como na UI. O Bearer autentica a conta (tenant); o `time_id` informa o time operacional. O middleware valida se o time pertence à conta e ativa o contexto para scopes e policies. O recurso permanece `/tarefas` (sem aninhar sob `/times`).

**Como funciona**

1. A request chega autenticada com Bearer Sanctum da **Conta**.
2. `EnsureApiTeam` resolve `time_id` da query string (GET) ou do body (POST/PUT/DELETE).
3. Ausência ou valor inválido → `400` com mensagem JSON.
4. Time que não pertence à conta autenticada → `403`.
5. Caso válido → `CurrentTeam::setForRequest($timeId)` hidrata `current_time_id()` e `current_conta_id()`.
6. Ao fim da request, o override é limpo no `finally` do middleware.

**Rodar na mão**

```bash
# Exemplo: listar tarefas do time 1 (substitua TOKEN da conta)
curl -s -H "Authorization: Bearer TOKEN" \
  "http://localhost:8080/api/tarefas?time_id=1"
```

---

## 2. CRUD de tarefas via API

| | |
|---|---|
| **Comando / entrada** | `GET/POST /api/tarefas`, `GET/PUT/DELETE /api/tarefas/{tarefa_id}` |
| **Quando** | Sob demanda (integrações externas autenticadas) |
| **Monitor** | Não |
| **Log** | Canal padrão Laravel (`LOG_CHANNEL`); eventos `api_tarefa_*` / `api_tarefa_*_failed` |
| **Código** | `app/Http/Controllers/TarefaController.php`, `app/Services/TarefaService.php`, `routes/api.php` |

**Para que serve**

Permite que sistemas externos criem, listem, consultem, atualizem e excluam (soft delete) tarefas do time informado, com resposta JSON no formato `{ message, data }` e relacionamentos aninhados (`tipo`, `situacao`, `prioridade`, `projeto`, `usuario`).

**Como funciona**

1. Rotas sob `auth:sanctum` + `api.team`, todas em `/api/tarefas`.
2. Controller autoriza via `TarefaPolicy` (sujeito Conta) e delega ao `TarefaService`.
3. Service usa `TarefaEloquentRepository`, converte Model → Domain → DTO aninhado.
4. Create grava `usuario_id` do **owner da conta** e `status = 0`; `projeto_id` deve pertencer ao time do contexto.
5. Listagem retorna todas as tarefas do time (sem filtros/paginação nesta versão).
6. Delete usa soft delete e exige `time_id` no body; show/update/delete de outro time → `404` (scope) ou `403` (time de outra conta).
7. Logs de sucesso/erro incluem `conta_id`, `time_id`, `tarefa_id` e `action`.

**Rodar na mão**

```bash
# Criar tarefa (ajuste IDs e TOKEN da conta)
curl -s -X POST http://localhost:8080/api/tarefas \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"time_id":1,"titulo":"Via API","projeto_id":1,"tipo_id":1,"situacao_id":1,"prioridade_id":2}'

# Testes automatizados
docker compose exec app php artisan test tests/Feature/Api/TarefaTest.php
```

---

## 3. Documentação OpenAPI (Swagger)

| | |
|---|---|
| **Comando / entrada** | UI: `GET /api/documentation` · gerar: `php artisan l5-swagger:generate` |
| **Quando** | Sob demanda (gerar após mudar anotações; UI para consultar o contrato) |
| **Monitor** | Não |
| **Log** | N/A |
| **Código** | `app/OpenApi/`, anotações em `app/Http/Controllers/TarefaController.php`, `config/l5-swagger.php` |

**Para que serve**

Expõe o contrato REST (Bearer da Conta + recurso `/tarefas` + `time_id` em query/body) para quem integra com a API.

**Como funciona**

1. Atributos OpenAPI nos controllers e classes em `app/OpenApi/`.
2. `php artisan l5-swagger:generate` produz `storage/api-docs/api-docs.json`.
3. A UI do Swagger fica em `/api/documentation` (via `darkaonline/l5-swagger`).
4. No “Authorize”, informe o Bearer da conta; nos GET use `time_id` na query; nas mutações envie `time_id` no body.

**Rodar na mão**

```bash
docker compose exec app php artisan l5-swagger:generate
# Abrir no browser: http://localhost:8080/api/documentation
```

---

## 4. Token de API da conta

| | |
|---|---|
| **Comando / entrada** | UI: `contas/{conta}/editar` (form Volt) · API: `POST/DELETE /api/tokens` (Bearer da Conta) |
| **Quando** | Sob demanda (owner gera/revoga; integrações regeneram via API autenticada) |
| **Monitor** | Não |
| **Log** | Canal padrão Laravel; eventos `api_token_issued` / `api_token_revoked` e `*_failed` |
| **Código** | `resources/views/livewire/contas/manage-api-token-form.blade.php`, `app/Services/TokenService.php`, `app/Http/Controllers/TokenController.php` |

**Para que serve**

Emite e revoga o Bearer Sanctum da **Conta** (tenant SaaS). Um token ativo por conta; gerar outro revoga o anterior. A gestão principal fica nas configurações da conta (só o owner); o perfil do usuário não emite mais token.

**Como funciona**

1. Na web, o owner abre `contas/{conta}/editar` e usa o Volt `manage-api-token-form` (não-owner → `403`).
2. Gerar chama `TokenService::issue`: em transação revoga tokens da conta e cria um novo (`name = api`) no model `Conta` (`HasApiTokens`).
3. O valor em texto claro aparece só naquela resposta/tela; reload mostra apenas “há token ativo”, sem o secret.
4. Revogar chama `TokenService::revoke` e apaga os tokens da conta.
5. `POST /api/tokens` e `DELETE /api/tokens` fazem o mesmo fluxo via `TokenController`, autenticados como Conta (sem middleware `api.team`).
6. Cutover: a migration `2026_08_06_000001_revoke_user_personal_access_tokens` remove tokens antigos `tokenable_type = User`.

**Rodar na mão**

```bash
# UI: logar como owner → Conta → editar → Generate / Regenerate / Revoke Token

# Via API (precisa de um Bearer válido da conta)
curl -s -X POST http://localhost:8080/api/tokens \
  -H "Authorization: Bearer TOKEN"

curl -s -X DELETE http://localhost:8080/api/tokens \
  -H "Authorization: Bearer TOKEN"

# Testes
docker compose exec app php artisan test \
  tests/Feature/ContaApiTokenTest.php \
  tests/Feature/Api/TokenTest.php
```
