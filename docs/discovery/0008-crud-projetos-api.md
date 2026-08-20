# Discovery: CRUD de projetos via API

## 1. Resumo

O RapidTask já gerencia projetos na interface web (criar, listar, visualizar e editar), com o projeto vinculado a um **cliente** do time, um responsável e datas opcionais. A API autenticada hoje cobre tarefas e comentários, mas não expõe projetos. Sem esse recurso, o consumidor autenticado não consegue montar o fluxo de integração (criar projeto → listar → exibir) nem alimentar flows de execução que dependem de projeto (por exemplo, criar tarefa com `projeto_id`). A regra de negócio desta demanda é: com **token de conta** e recorte por **`time_id`**, o consumidor realiza o CRUD de projetos no mesmo padrão da API já existente; a listagem devolve todos os projetos daquele time, sem filtros nem paginação. O produto ainda não está em produção; o uso esperado baseia-se nesse fluxo exemplo, não em métricas.

## 2. Objetivo

Permitir que um consumidor autenticado com token de conta crie, liste, consulte, atualize e exclua projetos via API, de forma alinhada ao contrato já usado no sistema, habilitando integração do cliente e uso em flows de execução.

## 3. Escopo

### Dentro

- CRUD completo de projetos via API: criar, listar, consultar (exibir), atualizar e excluir
- Autenticação com token de conta e recorte operacional por `time_id` (mesmo padrão da API de tarefas)
- Listagem sem filtros e sem paginação: todos os projetos do **time** informado
- Atualização por substituição completa (`PUT`); exclusão por soft delete
- Criação com `nome`, `sigla` e `cliente_id` obrigatórios; `descricao` e datas opcionais
- Demais operações no mesmo padrão de contrato da API atual (tarefas/comentários)
- Testes seguindo a estratégia já adotada no projeto

### Fora

- Filtros na listagem
- Paginação
- `PATCH` (atualização parcial)
- Listagem de todos os projetos da conta (cross-time)
- Feature flag / liberação gradual
- Endpoints de clientes, times, arquivos de projeto, anotações de projeto ou histórico de projeto
- Alteração do CRUD web de projetos (já existente)
- Webhooks / eventos ao criar ou alterar projeto

## 4. Premissas

- Feature flag: Não — liberação direta
- O model `Projeto` e o CRUD web já existem; esta demanda expõe o recurso na API, não cria o domínio do zero
- Autorização e verificação seguem o padrão do projeto e são consideradas cobertas pelo token de conta (mais o `time_id` já exigido pela API)
- Recorte da listagem e das demais operações: **time informado via `time_id`**, não a conta inteira
- A listagem, sem filtros/paginação, retorna todos os projetos daquele time
- Atualização é substituição completa (`PUT`), no padrão da API de tarefas
- Exclusão é soft delete; não há `PATCH` nesta entrega
- Criação exige `nome`, `sigla` e `cliente_id`; `descricao` e datas são opcionais
- O consumidor **já possui o `cliente_id`**; não há API de clientes nesta entrega e não faz parte desta demanda obter esse identificador
- Stakeholders de validação: equipe de desenvolvimento
- Testes mantêm o padrão atual da aplicação (incluindo isolamento, no mesmo espírito das demais rotas de API)
- Sistema ainda não está no ar; aceite baseado no fluxo exemplo (criar → listar → exibir), não em evidência de produção

## 5. Considerações de segurança

- Dados sensíveis: dados de projeto (nome, descrição, sigla, vínculo com cliente, datas, responsável); não há credenciais no recurso, mas o conteúdo é interno ao tenant e deve permanecer isolado por conta/time
- Autenticação/Autorização: Bearer com token de conta; a demanda afirma que autorização/verificação já está coberta por esse token, no padrão existente da API (incluindo contexto de time nas rotas de domínio)
- Exposição de APIs: novos endpoints de CRUD de projetos; não há mudança no modelo de token nem nos endpoints já existentes de tarefas/comentários/token
- Compliance: não informado requisito adicional (LGPD, PCI ou similar) além do isolamento multi-tenant já esperado no produto
- Outras considerações: nenhuma adicional identificada além de seguir o padrão de isolamento já adotado na API; não inventar controles novos nesta demanda

## 6. Dúvidas

Todas respondidas.

- Recorte: listagem e demais operações usam `time_id` (projetos do time informado, não da conta inteira)
- Atualização: substituição completa (`PUT`); exclusão: soft delete; sem `PATCH`

## 7. Informações ausentes

- Formato da resposta: relacionamentos aninhados (cliente, time, usuário) versus apenas IDs — não especificado; no CRUD de tarefas a API atual devolve relacionamentos aninhados

Payload de criação (`nome`, `sigla`, `cliente_id` obrigatórios; `descricao` e datas opcionais) e origem do `cliente_id` (consumidor já possui o id) foram fechados.

## 8. Status

**Pronto para Planejamento?** Sim

Objetivo, escopo, ausência de feature flag, recorte por `time_id`, `PUT`/soft delete (sem `PATCH`), payload de criação e premissa de que o consumidor já tem o `cliente_id` estão fechados. O formato da resposta (relacionamentos aninhados vs. IDs) pode ser alinhado no planejamento ao padrão da API de tarefas, sem bloquear o início.
