# Discovery: API — token por conta (tenant SaaS)

## 1. Resumo

Com a conta como tenant externo (fatias `0003`–`0005`), a autenticação da API ainda segue o modelo anterior: token pessoal do usuário + header `X-Time-Id`. A demanda alinha o contrato da API ao SaaS: o token passa a ser da **conta**, identifica o tenant, deixa de usar `X-Time-Id`, e o request informa o **time** (`time_id`) no body — com isolamento absoluto entre contas. A geração de token sai do perfil do usuário e passa às configurações de conta, restrita ao admin/owner.

## 2. Objetivo

Permitir que o admin/owner gere e revogue token nas configurações da conta, autenticar a API com esse token (Bearer) e garantir que um token da conta A nunca acesse ou manipule recursos da conta B, com cutover direto (sem convivência de modelos).

## 3. Escopo

### Dentro

- Geração e revogação de token de API nas **configurações de conta**, restritas ao **admin/owner**
- Remoção da geração/gestão de token no **perfil do usuário**
- Token identifica a **conta** (tenant), não o usuário individual
- Remoção do uso de `X-Time-Id` no contrato da API
- `time_id` obrigatório no **body** do request; no `GET /api/tarefas`, `time_id` no body filtra as tarefas daquele time
- Isolamento: token da conta A não acessa/manipula recursos da conta B (critério de aceite)
- Invalidação em massa dos tokens emitidos no modelo antigo (por usuário), sem período de convivência
- Atualização dos consumidores internos de teste e da documentação da API (Swagger) ao novo contrato
- Testes no padrão já usado na aplicação (incluindo cenário de isolamento cross-conta na API)

### Fora

- Feature flag / dual-token / período de migração gradual
- Escopos granulares de permissão por token
- Múltiplos tokens simultâneos por conta (salvo se já for decisão explícita no planejamento)
- Expansão de endpoints além do CRUD de tarefas já existente
- Multi-conta por usuário / troca de conta
- Planos, limites comerciais ou billing

## 4. Premissas

- Feature flag: Não — cutover direto, sem dual-token
- Somente o **admin/owner** da conta pode gerar e revogar o token
- A UI de token no perfil do usuário é removida nesta entrega
- `time_id` é enviado no **body** das requisições (incluindo GET de listagem)
- A API hoje é usada apenas internamente para testes; tokens por usuário já emitidos podem ser invalidados de uma vez
- A fronteira de isolamento da API é a **conta**; `time_id` continua como unidade operacional dentro da conta
- O time informado no request deve pertencer à conta identificada pelo token
- Continuidade do CRUD `/api/tarefas` já existente, apenas com autenticação/escopo alinhados ao tenant conta
- Stakeholders de validação: PM e equipe de desenvolvimento
- Testes seguem a prática já adotada na aplicação (Pest / Feature, isolamento)

## 5. Considerações de segurança

- Dados sensíveis: tokens de API (credenciais de acesso); exposição do token na UI de configurações deve seguir o padrão seguro já usado (ex.: mostrar valor só na criação)
- Autenticação/Autorização: Bearer token autentica a **conta**; somente admin/owner gera/revoga; autorização de recursos deve garantir pertencimento à conta do token e ao `time_id` do body
- Exposição de APIs: contrato dos endpoints de tarefas e de gestão de token muda (fim de `X-Time-Id` e de token no perfil; origem do token nas configurações de conta; `time_id` no body)
- Compliance: não informado requisito adicional (LGPD/PCI) além do isolamento multi-tenant já esperado no produto
- Outras considerações: isolamento cross-conta é **requisito de aceite** explícito; tokens antigos por usuário devem deixar de funcionar após o cutover

## 6. Dúvidas

Todas respondidas.

## 7. Informações ausentes

- Decisão explícita sobre quantidade de tokens ativos por conta (um vs. múltiplos) — pode ser fechada no planejamento se a premissa padrão for “um ativo”, como no modelo antigo por usuário

## 8. Status

**Pronto para Planejamento?** Sim

Objetivo, escopo, cutover sem feature flag, aceite de isolamento e decisões de UI/contrato (`admin/owner`, `time_id` no body, remoção do token no perfil) estão fechados. Relaciona-se às fatias `0001`/`0002` (API) e `0003`–`0005` (conta SaaS); `0005` deixava “API / tokens por tenant” fora de escopo — esta demanda fecha essa lacuna.
