# Discovery: Projetos — arquivos e anotações (UI + API)

## 1. Resumo

O domínio de projetos já tem CRUD web e API, mas o detalhe do projeto (`/projetos/{projeto}`) não oferece arquivos nem anotações. A regra de negócio é: membros do time anexam documentos ao projeto (listar, enviar, baixar) e registram anotações (listar, criar); edição e exclusão de anotação são somente do autor; exclusão de arquivo é somente do dono (quem enviou). Exclusões usam soft delete. Os dados ficam isolados por time e por conta. Model, policy, Form Request de upload e rota web de download já existem; falta a UI (bloqueia o beta) e os endpoints REST equivalentes (pós-beta).

Épico: [EAS-7](https://linear.app/easy-wrk/issue/EAS-7/projetos-arquivos-e-anotacoes). Atividades: [EAS-13](https://linear.app/easy-wrk/issue/EAS-13/ui-de-arquivos-no-detalhe-do-projeto), [EAS-14](https://linear.app/easy-wrk/issue/EAS-14/ui-de-anotacoes-no-detalhe-do-projeto), [EAS-15](https://linear.app/easy-wrk/issue/EAS-15/api-de-arquivos-de-projeto), [EAS-16](https://linear.app/easy-wrk/issue/EAS-16/api-de-anotacoes-de-projeto).

## 2. Objetivo

Completar o fluxo de projeto no produto: a UI de arquivos e anotações no detalhe do projeto para liberar `1.0.0-beta.1`, e depois expor o mesmo recurso na API autenticada, com isolamento cross-tenant coberto por testes em ambas as frentes.

## 3. Escopo

### Dentro

- **EAS-13 (bloqueia beta):** na página `/projetos/{projeto}`, seção de arquivos — listar, upload (reusar `StoreProjetoArquivoRequest`), download (`arquivos.download` já existente) e exclusão com confirmação **somente pelo dono** (soft delete); teste Feature de isolamento cross-tenant; marcar o item correspondente em `config/versoes.php` como `stable`
- **EAS-14 (bloqueia beta):** na mesma página, seção de anotações — listar, criar, editar e excluir **somente pelo autor** (padrão de comentários de tarefa; exclusão com soft delete); teste Feature de isolamento cross-tenant; marcar o item correspondente em `config/versoes.php` como `stable`
- **EAS-15 (pós-beta):** `GET/POST /api/projetos/{projeto_id}/arquivos` e `DELETE /api/projetos/{projeto_id}/arquivos/{arquivo_id}` (upload multipart; exclusão soft delete somente pelo dono); schemas OpenAPI; teste Feature de isolamento cross-tenant
- **EAS-16 (pós-beta):** `GET/POST /api/projetos/{projeto_id}/anotacoes` e `PUT/DELETE /api/projetos/{projeto_id}/anotacoes/{anotacao_id}` (editar/excluir somente pelo autor; exclusão soft delete); schemas OpenAPI; teste Feature de isolamento cross-tenant
- Soft delete em arquivos e anotações: as tabelas e models hoje não têm `deleted_at` / `SoftDeletes` — a entrega inclui esse comportamento

### Fora

- Feature flag / liberação gradual
- Endpoint de download via API (a rota web `GET /arquivos/{arquivo}/download` já existe; não está nas atividades)
- `PATCH` de anotação; filtros ou paginação nas listagens de API
- Arquivos ou anotações em tarefa (só no projeto)
- Histórico de projeto, preview de arquivo, versionamento de anexo ou tipos/tamanho além do que o Form Request já valida
- Alteração do CRUD de projeto (web ou API) além das seções/endpoints acima
- Release hygiene (tags `v1.0.0-alpha.5` / `v1.0.0-beta.1`) — pertence ao bloco A-3, não a este épico

## 4. Premissas

- Feature flag: Não — liberação direta
- Tabelas `projetos_arquivos` e `projetos_anotacoes` já existem (`projeto_id`, `usuario_id`, campos de conteúdo, timestamps); **sem** `deleted_at` hoje — a entrega passa a usar soft delete
- `ProjetoArquivo`, `ProjetoAnotacao`, policies e trait `BelongsToTeamViaProjeto` já existem; `StoreProjetoArquivoRequest` e `ArquivoDownloadController` já existem
- Factory de anotação existe; factory de arquivo não existe
- A âncora da UI é o show existente do projeto, sem rota extra obrigatória
- Qualquer membro do time lista e cria arquivos/anotações; lista e baixa arquivos do time
- Anotações: editar e excluir somente o autor (`usuario_id`), no padrão de comentários de tarefa
- Arquivos: excluir somente o dono (`usuario_id` de quem enviou)
- Upload web reutiliza as regras já definidas: tipos `pdf`, `png`, `jpg`, `jpeg`, `doc`, `docx`, `xls`, `xlsx`, `txt`; máximo 10 MB; `nome` e `descricao` obrigatórios
- API segue o contrato já usado (token de conta + `time_id`), no mesmo espírito de comentários de tarefa e CRUD de projetos
- UI entra no critério de saída de `1.0.0-beta.1`; API entra depois, sem bloquear o beta
- Referência de status: `docs/references/endpoints-status.md` (blocos A-1, A-2, C-4, C-5)

## 5. Considerações de segurança

- Dados sensíveis: arquivos enviados pelo time (documentos e imagens no disco `local`) e texto livre de anotações; conteúdo interno ao tenant, sem credenciais no recurso
- Autenticação/Autorização: Web com sessão Breeze e policies existentes; API com Sanctum (token de conta) e recorte por `time_id`. Ponto crítico: `ProjetoAnotacaoPolicy` e `ProjetoArquivoPolicy` autorizam `update`/`delete` por acesso ao time, não por autoria. A policy de anotação precisa restringir `update` e `delete` a `usuario_id == auth()->id()`. A policy de arquivo precisa restringir `delete` a `usuario_id == auth()->id()` (dono)
- Exposição de APIs: sete endpoints novos (listar/enviar/excluir arquivo; listar/criar/editar/excluir anotação); a rota web de download já existe e permanece
- Compliance: sem requisito adicional informado (LGPD, PCI ou similar) além do isolamento multi-tenant já exigido nas quatro atividades
- Outras considerações: `StoreProjetoArquivoRequest` restringe tipo e tamanho; exclusão por não-autor/não-dono e download/listagem cross-conta/time devem falhar (403/404), cobertos pelos testes Feature pedidos em cada atividade; exclusão é soft delete (registro permanece, some das listagens)

## 6. Dúvidas

Todas respondidas.

- Exclusão de arquivo e anotação: **soft delete** (incluir o comportamento; tabelas/models hoje não têm `SoftDeletes`)
- Anotações: editar e excluir **somente o autor**, no padrão de comentários de tarefa
- Arquivos: excluir **somente o dono** (quem enviou)

## 7. Informações ausentes

Nenhuma — contexto suficiente para planejamento.

## 8. Status

**Pronto para Planejamento?** Sim

Objetivo, as quatro atividades, ausência de feature flag, âncora da UI, soft delete e regras de autoria/dono estão fechados.
