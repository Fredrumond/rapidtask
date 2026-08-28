# Discovery: CRUD de clientes via API

## 1. Resumo

O RapidTask já gerencia clientes na interface web (listar, criar e editar), com domínio rico (`ClienteDomain` / `ClienteService`) e isolamento por time. A API autenticada cobre tarefas, comentários e projetos, mas não expõe clientes. Sem esse recurso, o consumidor autenticado não consegue obter ou criar o `cliente_id` necessário para o fluxo de integração (criar cliente → criar projeto → criar tarefa). A regra desta demanda é: com **token de conta** e recorte por **`time_id`**, o consumidor realiza o CRUD de clientes no mesmo padrão da API já existente; a listagem devolve todos os clientes daquele time, sem filtros nem paginação. Esta fatia é pós-beta (bloco C-1) e não bloqueia `1.0.0-beta.1`.

## 2. Objetivo

Permitir que um consumidor autenticado com token de conta crie, liste, consulte, atualize e exclua clientes via API, de forma alinhada ao contrato já usado no sistema e ao comportamento já existente na Web.

## 3. Escopo

### Dentro

- CRUD completo de clientes via API: `GET /api/clientes`, `POST /api/clientes`, `GET /api/clientes/{cliente_id}`, `PUT /api/clientes/{cliente_id}`, `DELETE /api/clientes/{cliente_id}`
- Autenticação com token de conta e recorte operacional por `time_id` (query string em GET; body nas mutações — mesmo padrão da API atual)
- Listagem sem filtros e sem paginação: todos os clientes do **time** informado
- Atualização por substituição completa (`PUT`); exclusão por soft delete
- Criação e atualização com `nome` obrigatório; `email` e `telefone` opcionais
- Schemas OpenAPI / Swagger
- Testes Feature: autenticação, `time_id` e isolamento cross-conta

### Fora

- Filtros na listagem
- Paginação
- `PATCH` (atualização parcial)
- Listagem de todos os clientes da conta (cross-time)
- Feature flag / liberação gradual
- Endpoints de times, conta, arquivos de projeto ou anotações de projeto
- Alteração do CRUD web de clientes (já existente)
- Novas regras de produto (ativar/inativar, bloqueio de exclusão com projetos vinculados, etc.)
- Webhooks / eventos ao criar ou alterar cliente
- Escopos granulares de token (bloco D)

## 4. Premissas

- Feature flag: Não — liberação direta
- O model `Cliente`, o CRUD web e o domínio rico já existem; esta demanda expõe o recurso na API, não cria o domínio do zero
- Autorização e verificação seguem o padrão do projeto (token de conta + `time_id` já exigido pela API)
- Recorte da listagem e das demais operações: **time informado via `time_id`**, não a conta inteira
- A listagem, sem filtros/paginação, retorna todos os clientes daquele time
- Atualização é substituição completa (`PUT`), no padrão da API de tarefas e projetos
- Exclusão é soft delete, como já ocorre na Web; não há `PATCH` nesta entrega
- Payload: `nome` obrigatório; `email` e `telefone` opcionais — iguais ao formulário web e a `StoreClienteRequest`
- Comportamento de exclusão com projetos vinculados permanece o da Web (soft delete do cliente); não se introduz regra nova
- Esta fatia fecha a lacuna deixada em `0008` (consumidor precisava já possuir o `cliente_id`)
- Demandas `0002` e `0008` documentam o contrato da API; esta fatia segue o mesmo recorte
- Não bloqueia `1.0.0-beta.1` (bloco C, pós-beta)

## 5. Considerações de segurança

- Dados sensíveis: nome, e-mail e telefone do cliente (PII). Conteúdo interno ao tenant; deve permanecer isolado por conta/time
- Autenticação/Autorização: Bearer com token de conta; recorte por `time_id`; membro do time informado (Policy de Cliente já restringe view/create/update/delete ao time)
- Exposição de APIs: cinco endpoints novos de CRUD de clientes; não há mudança no modelo de token nem nos endpoints já existentes
- Compliance: isolamento multi-tenant já esperado no produto; não há requisito adicional informado (LGPD formal, PCI ou similar)
- Outras considerações: nenhuma adicional identificada além de seguir o padrão de isolamento já adotado na API; não inventar controles novos nesta demanda

## 6. Dúvidas

Nenhuma crítica em aberto. Recorte por `time_id`, ausência de feature flag, `PUT`/soft delete (sem `PATCH`) e payload (`nome` obrigatório; `email`/`telefone` opcionais) estão fechados.

## 7. Informações ausentes

- Formato da resposta: relacionamentos aninhados (time, usuário) versus apenas IDs — não especificado; no CRUD de tarefas e projetos a API atual devolve relacionamentos aninhados

## 8. Status

**Pronto para Planejamento?** Sim

Objetivo, escopo, ausência de feature flag, recorte por `time_id`, `PUT`/soft delete (sem `PATCH`) e payload de criação/atualização estão fechados. O formato da resposta (relacionamentos aninhados vs. IDs) pode ser alinhado no planejamento ao padrão da API de tarefas e projetos, sem bloquear o início.
