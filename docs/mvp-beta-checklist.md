# Checklist MVP → `1.0.0-beta.1`

Critério do beta: **feature-complete do escopo 1.0**, utilizável no dia a dia (estudo / early adopters), com bugs conhecidos aceitáveis e API estável no que já existe.

Versão atual documentada: `1.0.0-alpha.5`  
Branch em curso: `dev` (alinhada com `origin/dev`)  
Tag existente: `v1.0.0-alpha.0` (faltam `v1.0.0-alpha.5` e `v1.0.0-beta.1`)

Nomenclatura alvo:

| Release | Quando |
|---------|--------|
| `1.0.0-alpha.4` | Conta SaaS + CRUD API + token por conta (feito; PR #53/#54) |
| `1.0.0-alpha.5` | Comentários de tarefa UI + API (PR #55; changelog atualizado; falta tag) |
| `1.0.0-beta.1` | Itens da seção **Entra no MVP do beta** fechados |
| `1.0.0-rc.1` | Polimento + regressão sem gaps críticos |
| `1.0.0` | Uso geral / “versão estável” |

---

## Já pronto (não reabrir no beta)

Marcado como base; só regressão / ajuste pontual se quebrar.

### Plataforma
- [x] Laravel 13 + PHP 8.4, Livewire Volt, Tailwind/Vite, Docker, CI (Pest + Pint)
- [x] Auth Breeze (login, registro, e-mail, senha, perfil básico)
- [x] Isolamento multi-tenant por time (scopes + policies)
- [x] Issues de segurança do baseline da refatoração

### Domínio web
- [x] Clientes CRUD
- [x] Projetos CRUD + quadro por situação + exclusão (listagem)
- [x] Tarefas CRUD + arquivar + listagem de arquivadas + recuperar
- [x] Show dedicado de tarefa (`tarefas/show`) com timeline de comentários
- [x] Times: criar, visualizar, editar nome, convidar, excluir (soft delete)
- [x] Página `/versoes`

### API
- [x] Sanctum: token por Conta (configurações da conta; 1 ativo)
- [x] CRUD `/api/tarefas` com `time_id` (query GET / body mutações) + Swagger
- [x] CRUD `/api/tarefas/{id}/comentarios` (listar / criar / editar / excluir) + schemas OpenAPI
- [x] CRUD `/api/projetos` com `time_id` + schemas OpenAPI (PR / discovery 0008)
- [x] CRUD `/api/clientes` com `time_id` + schemas OpenAPI (PR #64)

### Conta SaaS
- [x] Schema `conta` + `time.conta_id` + backfill
- [x] Registro cria conta; sessão com `current_conta_id`
- [x] Convite bloqueia e-mail de outra conta
- [x] Editar nome da conta
- [x] Scopes/policies + nav com nome da conta + testes cross-conta
- [x] Merge `feature/conta-saas` (PR #53) e token por conta (PR #54) em `dev`

### Domínio rico (padronização interna — discovery 0009)
- [x] Tarefa, TarefaComentario, Token, Cliente, Projeto, Conta, Time e Convite com Domain + Service
- [x] Mutações Volt (e API, quando existe) passam pelo Service compartilhado
- [x] Testes unitários em `tests/Unit/Domain/` + Feature das mutações principais

### Tarefas — comentários (`1.0.0-alpha.5`)
Discovery `0007`. Policy ajustada (update/delete só pelo autor). UI Volt em `tarefas/show` + API + testes Feature (web e API). Changelog em `config/versoes.php` atualizado. Merge no `dev`: PR #55.

- [x] Listar comentários na show da tarefa (mais recentes no topo)
- [x] Criar comentário (membro do time)
- [x] Editar comentário (somente autor / policy)
- [x] Excluir comentário (somente autor / soft delete)
- [x] Teste Feature web: isolamento cross-time/conta + CRUD na show
- [x] Teste Feature API: auth, `time_id`, autoria e isolamento cross-conta
- [x] Item em `/versoes` (`1.0.0-alpha.5`)

---

## Entra no MVP do beta

Ordem sugerida. Cada item fecha com UI Volt (quando aplicável) + policy já existente + teste de isolamento mínimo.

### 0. Release hygiene (antes ou junto do beta)
- [x] Commit da fatia de comentários (UI + API + discovery 0007 + testes + `versoes.php` + checklist) — PR #55
- [x] Merge / push de `dev` alinhado com o código atual (`origin/dev`)
- [x] Atualizar `config/versoes.php`:
  - [x] Release `1.0.0-alpha.4` com Conta SaaS + CRUD API + token por conta
  - [x] Corrigir estados desatualizados: **editar time** e **excluir projeto** → `stable`
  - [x] Release `1.0.0-alpha.5` com comentários de tarefa (UI + API) como `stable`
- [ ] Tag git alinhada (`v1.0.0-alpha.5` / depois `v1.0.0-beta.1`)
- [ ] Suíte Pest verde no Docker/CI (web tenant + conta + API + comentários + arquivos + anotações)

### 1. Projetos — arquivos (model, policy, download e Form Request prontos; UI ausente)
- [ ] Listar arquivos no detalhe do projeto
- [ ] Upload (reusar `StoreProjetoArquivoRequest`)
- [ ] Download (rota `arquivos.download` já existe)
- [ ] Excluir arquivo
- [ ] Teste Feature: autorização de download/exclusão cross-tenant
- [ ] Item em `/versoes` marcado `stable`

### 2. Projetos — anotações (model/policy prontos; UI ausente)
- [ ] Listar anotações no detalhe do projeto
- [ ] Criar / editar / excluir anotação
- [ ] Teste Feature: isolamento cross-tenant
- [ ] Item em `/versoes` marcado `stable`

### 3. Critérios de saída do beta (`1.0.0-beta.1`)
- [ ] Fluxo ponta a ponta: registro → conta/time → cliente → projeto (com arquivo + anotação) → tarefa (com comentário) → token API → CRUD tarefa/comentário via API
- [ ] Dois usuários em contas distintas: nenhum vazamento de dados (comentários já cobertos; regressão após UIs de arquivo/anotação)
- [ ] Changelog em `/versoes` com release beta e itens acima como `stable`
- [ ] README / quick start coerentes com o fluxo atual (Docker + registro + time)

---

## Fica para depois (pós-beta / pós-`1.0.0`)

Não bloqueia `1.0.0-beta.1`. Pode entrar em beta.x só se sobrar capacidade e não atrasar o critério de saída.

### Produto / UI
- [ ] Histórico de tarefa (`TarefaHistorico`) — model existe; sem UI nem escrita automática completa no Volt
- [ ] Histórico de projeto
- [ ] Avatar no perfil (`UpdateAvatarRequest` e coluna existem; UI incompleta)
- [ ] Excluir tarefa arquivada de forma definitiva (legado marcava bug)
- [ ] Melhorias de UX (filtros avançados, dashboard rico, etc.)

### Conta / SaaS avançado
- [ ] Multi-conta por usuário + troca de conta na UI
- [ ] Slug / subdomínio / identificador de rota da conta
- [ ] Convite de usuário sem conta prévia com regras SaaS específicas
- [ ] Planos, limites e billing

### API
- [ ] Escopos granulares e múltiplos tokens por usuário
- [x] Tokens / contexto por tenant de conta (token Conta; `time_id` query/body)
- [x] Endpoints de comentários de tarefa
- [x] CRUD `/api/projetos`
- [x] CRUD `/api/clientes`
- [ ] Filtros na listagem de tarefas, `PATCH`, paginação avançada
- [ ] Endpoints de times, conta, arquivos e anotações
- [ ] Webhooks / eventos

### Ops / qualidade extra
- [ ] Soften de expiração de token (hoje vitalício até revogar)
- [ ] Observabilidade / rate limit de API além do padrão Laravel
- [ ] Deploy/prod runbook além do Docker local

---

## Decisão rápida: o que é “deve” vs “pode”

| Item | Beta MVP | Motivo |
|------|----------|--------|
| Conta SaaS + token por conta | Feito | PRs #53/#54 em `dev`; falta tag `v1.0.0-alpha.4`/`alpha.5` |
| Comentários de tarefa (UI + API) | Feito | PR #55; `alpha.5` no changelog; falta tag |
| CRUD API projetos e clientes | Feito | Discoveries 0008 / PR #64; não bloqueia o beta |
| Arquivos de projeto (UI) | Deve | Backend quase pronto; fluxo de projeto incompleto sem isso |
| Anotações de projeto (UI) | Deve | Idem |
| Histórico (tarefa/projeto) | Depois | Útil, não bloqueia operar o produto |
| Avatar | Depois | Cosmético |
| Expandir API (times, conta, arquivos, PATCH/filtros) | Depois | Fora do critério de saída do beta |
| Multi-conta / billing | Depois | Fora das fatias 0003–0005 |

---

## Definition of Done por fatia de UI (comentários / arquivos / anotações)

Para cada uma das três fatias do MVP:

1. Páginas Volt no padrão de `docs/arquitetura.md` (authorize + validate + flash + redirect)
2. Rotas em `routes/web.php` se precisar de rotas novas
3. Links a partir de `projetos/show` ou `tarefas/edit` / `tarefas/show` (sem menu extra obrigatório)
4. Policy existente respeitada; 404/403 cross-tenant
5. Teste Pest Feature no cenário de dois times/contas
6. Item correspondente em `config/versoes.php` marcado `stable`

**Comentários:** DoD completo e mergeado no `dev` (`1.0.0-alpha.5`, PR #55).  
**Arquivos / anotações:** ainda abertos (bloqueiam `1.0.0-beta.1`).
