# Discovery: Conta SaaS — propagação do tenant e isolamento de dados

## 1. Resumo

Com `conta_id` no schema e na sessão (fatias `0003` e `0004`), esta fatia fecha o critério de sucesso: nenhum usuário vê ou acessa dados de outra conta. Isso exige atualizar o trait `ScopedToMemberTeams`, as policies, o header/nav para exibir o nome da conta e os testes de isolamento cross-conta.

## 2. Objetivo

Garantir isolamento total entre contas no sistema web — queries, autorização, UI de navegação e testes de regressão — satisfazendo o critério verificável: "Empresa A e Empresa B no mesmo ambiente, nenhum usuário/conta vê ou acessa dados da outra."

## 3. Escopo

### Dentro

- Atualizar `ScopedToMemberTeams` (em `Time`): filtrar por `conta_id` da sessão AND memberships do usuário (critérios combinados, não substituídos)
- Atualizar `HandlesTeamAuthorization` (`canAccessTeam`): verificar que o time pertence à `conta_id` ativa na sessão antes de autorizar
- Exibir nome da conta ativa no header/nav (`navigation.blade.php`) — desktop e mobile
- Testes: estender `criarCenarioDoisTimes()` em `tests/Pest.php` para suportar duas contas distintas; adicionar cenário de isolamento cross-conta em `TenantIsolationTest`
- Garantir que route model binding continue retornando 404 para recursos de outra conta (regressão)

### Fora

- Multi-conta por usuário / troca de conta na UI
- API / tokens por tenant
- Planos e limites comerciais

## 4. Premissas

- Feature flag: Não
- Fatias `0003` e `0004` estão concluídas e `conta_id` está disponível na sessão
- A fronteira de isolamento é `conta_id` (externo); `time_id` continua como unidade operacional interna
- Entidades folha (Cliente, Projeto, Tarefa) **não** precisam de `conta_id` direto: o isolamento é garantido pela cadeia `conta → time → entidade`
- `ScopedToMemberTeams` combina os dois critérios via AND: times da conta ativa **e** dos quais o usuário é membro
- O critério de sucesso é verificável pelos testes de isolamento automatizados

## 5. Dúvidas

Todas respondidas.

## 6. Informações ausentes

Nenhuma.

## 7. Status

**Pronto para Planejamento?** Sim

Todas as dúvidas foram respondidas. Escopo está claro: scopes e policies ganham filtro por `conta_id`, nav exibe nome da conta, testes de isolamento cobrem o nível de conta. Pré-requisitos: fatias `0003` e `0004` concluídas.
