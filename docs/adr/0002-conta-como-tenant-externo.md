# ADR 0002 — Conta como tenant externo (time operacional)

**Data:** 05/08/2026

## Contexto

O RapidTask isolava dados só por `time_id`. Para multi-empresa no mesmo deploy, é preciso um tenant SaaS acima dos times, sem reescrever toda a cadeia Cliente → Projeto → Tarefa. A API já resolve contexto via header `X-Time-Id` ([ADR 0001](0001-contexto-de-time-na-api-via-header.md)); a web passa a carregar também `current_conta_id` na sessão.

## Opções consideradas

1. **`conta_id` em toda entidade folha** — filtrar Cliente/Projeto/Tarefa direto por conta  
2. **Conta como tenant externo; time operacional** — `conta → time → entidade`; folhas sem `conta_id`  
3. **Substituir `time_id` por `conta_id`** — um único nível de isolamento, abandonando times como unidade de trabalho  
4. **Só membership de time, sem filtro de conta** — confiar que o usuário nunca entra em time de outra conta

## Decisão

Adotar a opção **2**: a **conta** é a fronteira externa de isolamento; o **time** permanece a unidade operacional. Sessão web guarda `current_time_id` e `current_conta_id` (conta derivada do time ativo). `TeamScope::applyMemberFilter` combina membership **e** `conta_id` da sessão; `canAccessTeam` exige membership e match de `current_conta_id` (sem conta na sessão → nega). Folhas continuam isoladas por `time_id` / cadeia de FKs.

## Justificativa

- Evita migração em massa nas tabelas folha e preserva o modelo mental já usado na UI.  
- Fecha o vazamento cross-conta mesmo se membership indevido existir.  
- Mantém compatibilidade com o contrato atual da API (time no header); a conta é hidratada de forma derivada em `CurrentTeam::setForRequest`.  
- Schema neutro para evolução futura (API por conta) sem retrabalho destrutivo.

## Consequências

### Benefícios

- Padrão claro para novas features: respeitar `conta → time → entidade`.  
- Isolamento verificável (`TenantIsolationTest` + sessão com time e conta).  
- UI mostra a conta ativa sem troca de conta nesta fase.

### Riscos

- Dois IDs de contexto na sessão aumentam chance de teste/rota sem `current_conta_id` (policy nega).  
- Folhas ainda podem vazar por `time_id` se membership cross-conta existir **e** a policy/scope de Time não for aplicado no caminho.

### Débitos técnicos

- API ainda não tem contrato explícito de conta (fora do escopo atual); depende do time informado.  
- Multi-conta por usuário / troca de conta na UI não implementados.

### Próximos passos (opcional)

- Ao expor novos endpoints API, reavaliar header/contexto de conta sem quebrar o ADR 0001.
