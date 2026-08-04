# Referência: API de tarefas

Índice

- [1. Isolamento de time na API](#1-isolamento-de-time-na-api)
- [2. CRUD de tarefas via API](#2-crud-de-tarefas-via-api)
- [3. Documentação OpenAPI (Swagger)](#3-documentação-openapi-swagger)

Decisão relacionada: [`docs/adr/0001-contexto-de-time-na-api-via-header.md`](../adr/0001-contexto-de-time-na-api-via-header.md)  
Discovery: [`docs/discovery/0002-crud-tarefas-api.md`](../discovery/0002-crud-tarefas-api.md)

---

## 1. Isolamento de time na API

| | |
|---|---|
| **Comando / entrada** | Header `X-Time-Id` + middleware `api.team` (`EnsureApiTeam`) |
| **Quando** | Em toda rota API de domínio multi-tenant (hoje: `/api/tarefas/*`) |
| **Monitor** | Não |
| **Log** | N/A (middleware só responde 400/403; o service loga as ações) |
| **Código** | `app/Http/Middleware/EnsureApiTeam.php`, `app/Support/CurrentTeam.php` |

**Para que serve**

Na API não há sessão de time como na UI. O header informa qual time o cliente quer usar; o middleware valida se o usuário autenticado é membro e ativa o contexto para scopes e policies.

**Como funciona**

1. A request chega autenticada com Bearer Sanctum.
2. `EnsureApiTeam` lê o header `X-Time-Id`.
3. Ausência ou valor inválido → `400` com mensagem JSON.
4. Usuário sem membership no time → `403`.
5. Caso válido → `CurrentTeam::setForRequest($timeId)` para `current_time_id()` e `TeamScope`.
6. Ao fim da request, o override é limpo no `finally` do middleware.

**Rodar na mão**

```bash
# Exemplo: listar tarefas do time 1 (substitua TOKEN)
curl -s -H "Authorization: Bearer TOKEN" -H "X-Time-Id: 1" \
  http://localhost:8080/api/tarefas
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

1. Rotas sob `auth:sanctum` + `api.team`.
2. Controller autoriza via `TarefaPolicy` e delega ao `TarefaService`.
3. Service usa `TarefaEloquentRepository`, converte Model → Domain → DTO aninhado.
4. Create grava `usuario_id` do autenticado e `status = 0`; `projeto_id` deve pertencer ao time do header.
5. Listagem retorna todas as tarefas do time (sem filtros/paginação nesta versão).
6. Delete usa soft delete; show/update/delete de outro time → `404` (scope).
7. Logs de sucesso/erro incluem `user_id`, `time_id`, `tarefa_id` e `action`.

**Rodar na mão**

```bash
# Criar tarefa (ajuste IDs e TOKEN)
curl -s -X POST http://localhost:8080/api/tarefas \
  -H "Authorization: Bearer TOKEN" \
  -H "X-Time-Id: 1" \
  -H "Content-Type: application/json" \
  -d '{"titulo":"Via API","projeto_id":1,"tipo_id":1,"situacao_id":1,"prioridade_id":2}'

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

Expõe o contrato REST (Bearer + `X-Time-Id` + schemas de tarefa) para quem integra com a API.

**Como funciona**

1. Atributos OpenAPI nos controllers e classes em `app/OpenApi/`.
2. `php artisan l5-swagger:generate` produz `storage/api-docs/api-docs.json`.
3. A UI do Swagger fica em `/api/documentation` (via `darkaonline/l5-swagger`).
4. No “Authorize”, informe o Bearer; nas operações de tarefas, envie também `X-Time-Id`.

**Rodar na mão**

```bash
docker compose exec app php artisan l5-swagger:generate
# Abrir no browser: http://localhost:8080/api/documentation
```
