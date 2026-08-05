# Discovery: Conta SaaS — registro, onboarding e sessão

## 1. Resumo

Com a entidade `conta` criada (fatia 1 — `0003-conta-saas-modelo-de-dados.md`), esta fatia garante que o ciclo de vida do usuário — registro, login e navegação — esteja vinculado a uma conta. O registro passa a criar automaticamente a conta e associar o usuário como admin. A sessão passa a carregar `conta_id` como o tenant externo, complementando o `time_id` já existente. O fluxo de convite de time passa a bloquear convidados que já pertencem a outra conta.

## 2. Objetivo

Fechar o ciclo de criação de conta para novos usuários, garantir que a sessão web carregue `conta_id` de forma transparente e impedir que o convite de time quebre a fronteira de isolamento entre contas.

## 3. Escopo

### Dentro

- Atualizar o fluxo de registro (Breeze/Livewire): ao criar o usuário, cria automaticamente uma `conta` e registra o usuário como admin
- Criar suporte de sessão para `conta_id` (ex.: expandir `CurrentTeam` ou criar `CurrentConta`) armazenando `current_conta_id`
- Atualizar middleware `SetCurrentTeam`: resolver `conta_id` a partir do time ativo e gravá-lo na sessão
- Garantir que a troca de time (`switchTeam` no nav) atualize `conta_id` na sessão quando necessário
- Bloquear convite de time para e-mail que já pertence a outra conta na plataforma: retornar erro claro no fluxo de convite
- Tela mínima de configuração da conta (somente edição de `nome`), acessível ao admin/owner

### Fora

- Multi-conta por usuário / troca de conta na UI
- Convite de usuário que ainda não tem conta (comportamento atual — criar conta no aceite do convite — não é escopo desta fase)
- Recuperação de senha afetada por conta
- UI de exibição do nome da conta no header/nav (fatia 3)
- API / tokens por tenant

## 4. Premissas

- Feature flag: Não
- O registro cria exatamente uma conta por usuário novo
- Um usuário pertence a uma conta nesta etapa; múltiplas contas por usuário estão fora do escopo
- `conta_id` na sessão é derivado do time ativo (todos os times do usuário são da mesma conta)
- Se o e-mail convidado já está vinculado a qualquer conta da plataforma, o convite é barrado — independente de qual conta enviou o convite
- A tela de configuração de conta é mínima: apenas edição do `nome`

## 5. Dúvidas

Todas respondidas.

## 6. Informações ausentes

Nenhuma.

## 7. Status

**Pronto para Planejamento?** Sim

Todas as dúvidas foram respondidas. Escopo está claro: registro cria conta, sessão carrega `conta_id`, convite bloqueia cross-conta por e-mail, tela de configuração é mínima. Pré-requisito: fatia `0003` concluída.
