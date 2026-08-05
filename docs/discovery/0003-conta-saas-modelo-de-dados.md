# Discovery: Conta SaaS — modelo de dados e hierarquia

## 1. Resumo

O RapidTask usa `time_id` como única fronteira de isolamento. Para suportar multi-empresa no mesmo deploy, é necessário introduzir uma entidade `conta` acima dos times, tornando-a o tenant real. Esta fatia cobre exclusivamente a camada de persistência: migration, model, relacionamentos e estratégia de migração dos dados existentes.

## 2. Objetivo

Criar a entidade `conta` no banco de dados, vincular `time` a ela e garantir que o schema seja compatível com a evolução futura da API, sem implementar ainda nenhuma mudança de comportamento em runtime.

## 3. Escopo

### Dentro

- Migration: tabela `conta` (`id`, `nome`, `usuario_id` como owner, `timestamps`, `softDeletes`)
- Migration: coluna `conta_id` FK em `time` (nullable na criação, NOT NULL após backfill)
- Model `Conta`: relações `hasManyTimes`, `belongsToUser` (owner)
- Atualizar model `Time`: relação `belongsToConta`
- Backfill de dados existentes: criar uma conta por `usuario_id` distinto e associar seus respectivos times
- `ContaFactory` com estados úteis

### Fora

- Fluxo de registro criando conta (fatia 2)
- Sessão / middleware carregando `conta_id` (fatia 2)
- Global scopes e policies usando `conta_id` como filtro (fatia 3)
- UI de onboarding e navegação por conta (fatia 3)
- Testes de isolamento cross-conta (fatia 3)
- Slug ou identificador de rota para conta
- API / tokens por tenant

## 4. Premissas

- Feature flag: Não
- Um `time` pertence a exatamente uma `conta`
- A `conta` tem um criador/owner (`usuario_id`)
- Campos da `conta` nesta etapa: somente `nome` e `usuario_id` — sem logo, slug ou subdomínio
- O backfill cria uma conta por `usuario_id` distinto e vincula os times do usuário a ela
- Um usuário pode ser owner de múltiplas contas (sem restrição de unicidade no schema)
- O schema deve ser neutro o suficiente para ser consumido futuramente pela API sem retrabalho destrutivo

## 5. Dúvidas

Todas respondidas.

## 6. Informações ausentes

Nenhuma.

## 7. Status

**Pronto para Planejamento?** Sim

Escopo restrito a schema, model e backfill. Todas as dúvidas foram respondidas; campos mínimos confirmados (`nome` + `usuario_id`); estratégia de backfill definida (uma conta por `usuario_id` distinto).
