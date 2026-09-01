# Status dos Endpoints — Referência de Implementação

Visão consolidada de todos os endpoints (API REST e rotas Web) do RapidTask: o que já existe e o que falta implementar.

Última atualização: 2026-09-01 — `routes/api.php`, `routes/web.php`, `docs/mvp-beta-checklist.md`, PRs #55 (comentários), #61 (domínio rico), #64 (CRUD clientes API), EAS-16 (anotações de projeto API).

---

## Legenda

| Símbolo | Significado |
|---------|-------------|
| ✅ | Implementado e estável |
| 🔲 | Falta implementar — **MVP beta (bloqueia `1.0.0-beta.1`)** |
| ⬜ | Falta implementar — backlog pós-beta |

---

## API REST (`/api`)

Auth: Bearer Sanctum por **Conta**. Tenant: `time_id` via query string (GET) ou body (mutações).  
Swagger UI disponível em `/api/documentation`.

### Autenticação / Tokens

| Status | Método | Path | Descrição |
|--------|--------|------|-----------|
| ✅ | `POST` | `/api/tokens` | Gerar token de acesso |
| ✅ | `DELETE` | `/api/tokens` | Revogar token ativo |

### Projetos

| Status | Método | Path | Descrição |
|--------|--------|------|-----------|
| ✅ | `GET` | `/api/projetos` | Listar projetos do time |
| ✅ | `POST` | `/api/projetos` | Criar projeto |
| ✅ | `GET` | `/api/projetos/{projeto_id}` | Detalhe do projeto |
| ✅ | `PUT` | `/api/projetos/{projeto_id}` | Atualizar projeto |
| ✅ | `DELETE` | `/api/projetos/{projeto_id}` | Excluir projeto |
| ⬜ | `GET` | `/api/projetos/{projeto_id}/arquivos` | Listar arquivos do projeto |
| ⬜ | `POST` | `/api/projetos/{projeto_id}/arquivos` | Upload de arquivo no projeto |
| ⬜ | `DELETE` | `/api/projetos/{projeto_id}/arquivos/{arquivo_id}` | Excluir arquivo do projeto |
| ✅ | `GET` | `/api/projetos/{projeto_id}/anotacoes` | Listar anotações do projeto |
| ✅ | `POST` | `/api/projetos/{projeto_id}/anotacoes` | Criar anotação no projeto |
| ✅ | `PUT` | `/api/projetos/{projeto_id}/anotacoes/{anotacao_id}` | Editar anotação |
| ✅ | `DELETE` | `/api/projetos/{projeto_id}/anotacoes/{anotacao_id}` | Excluir anotação |

### Tarefas

| Status | Método | Path | Descrição |
|--------|--------|------|-----------|
| ✅ | `GET` | `/api/tarefas` | Listar tarefas do time |
| ✅ | `POST` | `/api/tarefas` | Criar tarefa |
| ✅ | `GET` | `/api/tarefas/{tarefa_id}` | Detalhe da tarefa |
| ✅ | `PUT` | `/api/tarefas/{tarefa_id}` | Atualizar tarefa (substituição completa) |
| ✅ | `DELETE` | `/api/tarefas/{tarefa_id}` | Excluir tarefa |
| ⬜ | `PATCH` | `/api/tarefas/{tarefa_id}` | Atualização parcial da tarefa |
| ⬜ | `GET` | `/api/tarefas` + filtros | Filtros avançados (situação, projeto, prioridade) + paginação |

### Comentários de Tarefa

| Status | Método | Path | Descrição |
|--------|--------|------|-----------|
| ✅ | `GET` | `/api/tarefas/{tarefa_id}/comentarios` | Listar comentários |
| ✅ | `POST` | `/api/tarefas/{tarefa_id}/comentarios` | Criar comentário |
| ✅ | `PUT` | `/api/tarefas/{tarefa_id}/comentarios/{comentario_id}` | Editar comentário (somente autor) |
| ✅ | `DELETE` | `/api/tarefas/{tarefa_id}/comentarios/{comentario_id}` | Excluir comentário (somente autor) |

### Clientes

| Status | Método | Path | Descrição |
|--------|--------|------|-----------|
| ✅ | `GET` | `/api/clientes` | Listar clientes do time |
| ✅ | `POST` | `/api/clientes` | Criar cliente |
| ✅ | `GET` | `/api/clientes/{cliente_id}` | Detalhe do cliente |
| ✅ | `PUT` | `/api/clientes/{cliente_id}` | Atualizar cliente |
| ✅ | `DELETE` | `/api/clientes/{cliente_id}` | Excluir cliente |

### Times

| Status | Método | Path | Descrição |
|--------|--------|------|-----------|
| ⬜ | `GET` | `/api/times` | Listar times da conta |
| ⬜ | `POST` | `/api/times` | Criar time |
| ⬜ | `GET` | `/api/times/{time_id}` | Detalhe do time |
| ⬜ | `PUT` | `/api/times/{time_id}` | Atualizar nome do time |
| ⬜ | `DELETE` | `/api/times/{time_id}` | Excluir time |

### Conta

| Status | Método | Path | Descrição |
|--------|--------|------|-----------|
| ⬜ | `GET` | `/api/conta` | Dados da conta ativa |
| ⬜ | `PUT` | `/api/conta` | Atualizar nome da conta (somente owner) |

---

## Web (Livewire Volt)

Auth: sessão Breeze. Tenant: `current_time_id` + `current_conta_id` via `SetCurrentTeam`.

### Autenticação (Breeze)

| Status | Método | Path | Descrição |
|--------|--------|------|-----------|
| ✅ | `GET` | `/register` | Cadastro (cria conta + time) |
| ✅ | `GET` | `/login` | Login |
| ✅ | `GET` | `/forgot-password` | Recuperar senha |
| ✅ | `GET` | `/reset-password/{token}` | Redefinir senha |
| ✅ | `GET` | `/verify-email` | Verificar e-mail |
| ✅ | `GET` | `/profile` | Perfil do usuário |

### Dashboard

| Status | Método | Path | Descrição |
|--------|--------|------|-----------|
| ✅ | `GET` | `/dashboard` | Dashboard principal |

### Clientes

| Status | Método | Path | Descrição |
|--------|--------|------|-----------|
| ✅ | `GET` | `/clientes` | Listar clientes |
| ✅ | `GET` | `/clientes/criar` | Formulário de criação |
| ✅ | `GET` | `/clientes/{cliente}/editar` | Formulário de edição |

### Projetos

| Status | Método | Path | Descrição |
|--------|--------|------|-----------|
| ✅ | `GET` | `/projetos` | Listar projetos |
| ✅ | `GET` | `/projetos/criar` | Formulário de criação |
| ✅ | `GET` | `/projetos/{projeto}` | Detalhe do projeto |
| ✅ | `GET` | `/projetos/{projeto}/editar` | Formulário de edição |
| 🔲 | — | `/projetos/{projeto}` (seção arquivos) | UI de arquivos dentro do show do projeto |
| 🔲 | — | `/projetos/{projeto}` (seção anotações) | UI de anotações dentro do show do projeto |

### Tarefas

| Status | Método | Path | Descrição |
|--------|--------|------|-----------|
| ✅ | `GET` | `/tarefas` | Listar tarefas |
| ✅ | `GET` | `/tarefas/criar` | Formulário de criação |
| ✅ | `GET` | `/tarefas/arquivadas` | Listar tarefas arquivadas |
| ✅ | `GET` | `/tarefas/{tarefa}` | Show da tarefa (+ comentários) |
| ✅ | `GET` | `/tarefas/{tarefa}/editar` | Formulário de edição |

### Times

| Status | Método | Path | Descrição |
|--------|--------|------|-----------|
| ✅ | `GET` | `/times` | Listar times |
| ✅ | `GET` | `/times/{time}` | Detalhe do time (membros, convites) |

### Conta

| Status | Método | Path | Descrição |
|--------|--------|------|-----------|
| ✅ | `GET` | `/contas/{conta}/editar` | Configurações da conta (nome + token API) |

### Convites

| Status | Método | Path | Descrição |
|--------|--------|------|-----------|
| ✅ | `GET` | `/convites/{convite}/aceitar` | Aceitar convite (URL assinada) |
| ✅ | `GET` | `/convites/{convite}/recusar` | Recusar convite (URL assinada) |

### Arquivos

| Status | Método | Path | Descrição |
|--------|--------|------|-----------|
| ✅ | `GET` | `/arquivos/{arquivo}/download` | Download de arquivo (`ArquivoDownloadController`) |

### Versões / Changelog

| Status | Método | Path | Descrição |
|--------|--------|------|-----------|
| ✅ | `GET` | `/versoes` | Changelog público do produto |

---

## Tarefas de Implementação

### Bloco A — MVP Beta (bloqueia `1.0.0-beta.1`)

> Model, Policy e rota de download já existem. Falta apenas a UI Volt.  
> Padrão: seguir `docs/arquitetura.md` § "Definition of Done" e § "Checklist nova entidade CRUD".

---

#### A-1 · Arquivos de Projeto — UI Web

**Contexto:** `ProjetoArquivo` (model + policy + `StoreProjetoArquivoRequest` + `ArquivoDownloadController`) prontos. Somente a interface Volt está ausente.

- [ ] Listar arquivos na página `/projetos/{projeto}` (seção dentro do show existente)
- [ ] Formulário de upload com `StoreProjetoArquivoRequest` já existente
- [ ] Botão de download linkando para `arquivos.download` já existente
- [ ] Ação de exclusão com `wire:confirm` (soft delete)
- [ ] Teste Feature: autorização de download/exclusão cross-tenant (conta A não acessa arquivo da conta B)
- [ ] Marcar item correspondente em `config/versoes.php` como `stable`

---

#### A-2 · Anotações de Projeto — UI Web

**Contexto:** `ProjetoAnotacao` (model + policy) prontos. UI e rotas ausentes.

- [ ] Listar anotações na página `/projetos/{projeto}` (seção dentro do show existente)
- [ ] Criar anotação (inline ou modal, sem rota extra se possível)
- [ ] Editar anotação (somente autor — verificar policy)
- [ ] Excluir anotação com `wire:confirm`
- [ ] Teste Feature: isolamento cross-tenant
- [ ] Marcar item correspondente em `config/versoes.php` como `stable`

---

#### A-3 · Release Hygiene (`1.0.0-beta.1`)

- [x] Commit da fatia de comentários (UI + API + discovery 0007 + testes + `versoes.php`) — PR #55
- [ ] Tag git `v1.0.0-alpha.5` e depois `v1.0.0-beta.1` após A-1 e A-2 (hoje só existe `v1.0.0-alpha.0`)
- [ ] Suíte Pest verde no Docker/CI (web tenant + conta + API + comentários + arquivos + anotações)
- [ ] Fluxo ponta-a-ponta: registro → conta/time → cliente → projeto (arquivo + anotação) → tarefa (comentário) → token API → CRUD via API

---

### Bloco B — Domínio Rico (padronização interna — discovery 0009) — **concluído**

> Não altera endpoints visíveis. Faz mutações web passarem pelo Service/Domain compartilhado.  
> Referência: `docs/discovery/0009-dominio-rico-projeto-conta-time-convite.md`. Entregue no PR #61.

- [x] **Projeto (Web):** criar, atualizar e excluir na Volt via `ProjetoService` / `ProjetoDomain`
- [x] **Conta:** criar no registro e renomear na tela de configuração via `ContaDomain`
- [x] **Time:** criar (vincula conta, adiciona admin) e excluir via `TimeDomain`
- [x] **Convite:** emitir, aceitar, recusar via `ConviteDomain` (preservar regras de status 0/1/2, bloqueio cross-conta, URL assinada)
- [x] Testes unitários das regras extraídas; preservar Feature tests existentes

---

### Bloco C — API Expandida (pós-beta)

> Não bloqueia `1.0.0-beta.1`. Entrar em beta.x se sobrar capacidade.  
> Padrão: seguir `docs/QUICK_START_GUIDE.md` (Controller → Service → Repository → Domain → DTO).

---

#### C-1 · CRUD de Clientes via API

- [x] `GET /api/clientes` — listar clientes do time
- [x] `POST /api/clientes` — criar cliente
- [x] `GET /api/clientes/{cliente_id}` — detalhe
- [x] `PUT /api/clientes/{cliente_id}` — atualizar
- [x] `DELETE /api/clientes/{cliente_id}` — excluir
- [x] Schemas OpenAPI / Swagger
- [x] Teste Feature: auth, `time_id`, isolamento cross-conta (PR #64)

---

#### C-2 · CRUD de Times via API

- [ ] `GET /api/times` — listar times da conta
- [ ] `POST /api/times` — criar time
- [ ] `GET /api/times/{time_id}` — detalhe
- [ ] `PUT /api/times/{time_id}` — atualizar nome
- [ ] `DELETE /api/times/{time_id}` — excluir
- [ ] Schemas OpenAPI / Swagger
- [ ] Teste Feature: somente admin; isolamento por conta

---

#### C-3 · Endpoint de Conta via API

- [ ] `GET /api/conta` — dados da conta autenticada
- [ ] `PUT /api/conta` — atualizar nome (somente owner)
- [ ] Schemas OpenAPI / Swagger

---

#### C-4 · Arquivos de Projeto via API

- [ ] `GET /api/projetos/{projeto_id}/arquivos`
- [ ] `POST /api/projetos/{projeto_id}/arquivos` (multipart/form-data)
- [ ] `DELETE /api/projetos/{projeto_id}/arquivos/{arquivo_id}`
- [ ] Schemas OpenAPI / Swagger
- [ ] Teste Feature: download/exclusão cross-tenant

---

#### C-5 · Anotações de Projeto via API

- [x] `GET /api/projetos/{projeto_id}/anotacoes`
- [x] `POST /api/projetos/{projeto_id}/anotacoes`
- [x] `PUT /api/projetos/{projeto_id}/anotacoes/{anotacao_id}`
- [x] `DELETE /api/projetos/{projeto_id}/anotacoes/{anotacao_id}`
- [x] Schemas OpenAPI / Swagger
- [x] Teste Feature: isolamento cross-tenant

---

#### C-6 · Melhorias nos Endpoints Existentes de Tarefas

- [ ] `PATCH /api/tarefas/{tarefa_id}` — atualização parcial (ex.: só mudar situação)
- [ ] Filtros na listagem `GET /api/tarefas`: `situacao`, `projeto_id`, `prioridade`, `tipo`
- [ ] Paginação avançada (cursor ou page-based) com metadados `links` / `meta`
- [ ] Atualizar schemas OpenAPI

---

### Bloco D — Infraestrutura de API (pós-beta, alta complexidade)

- [ ] Escopos granulares de token (leitura vs. escrita por recurso)
- [ ] Múltiplos tokens ativos por conta
- [ ] Expiração configurável de token
- [ ] Webhooks / eventos (ex.: tarefa atualizada, comentário criado)
- [ ] Rate limiting além do padrão Laravel
- [ ] Observabilidade (logs estruturados, rastreamento por `time_id`)

---

## Resumo por Contagem

| Bloco | Total | Implementado | Faltando |
|-------|-------|-------------|---------|
| API — Tokens | 2 | 2 ✅ | 0 |
| API — Projetos (CRUD base) | 5 | 5 ✅ | 0 |
| API — Projetos (arquivos + anotações) | 7 | 4 ✅ | 3 ⬜ |
| API — Tarefas (CRUD base) | 5 | 5 ✅ | 0 |
| API — Tarefas (PATCH + filtros) | 2 | 0 | 2 ⬜ |
| API — Comentários | 4 | 4 ✅ | 0 |
| API — Clientes | 5 | 5 ✅ | 0 |
| API — Times | 5 | 0 | 5 ⬜ |
| API — Conta | 2 | 0 | 2 ⬜ |
| Web — Auth / Breeze | 5 | 5 ✅ | 0 |
| Web — Clientes | 3 | 3 ✅ | 0 |
| Web — Projetos | 4 | 4 ✅ | 0 |
| Web — Projetos (arquivos + anotações UI) | 2 | 0 | 2 🔲 |
| Web — Tarefas | 5 | 5 ✅ | 0 |
| Web — Times | 2 | 2 ✅ | 0 |
| Web — Conta / Convites | 3 | 3 ✅ | 0 |
| **Total** | **61** | **52 ✅** | **9** |
