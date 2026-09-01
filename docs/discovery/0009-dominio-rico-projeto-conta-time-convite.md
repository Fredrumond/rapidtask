# Discovery: Fechar domínio rico em Projeto, Conta, Time e Convite

## 1. Resumo

O RapidTask já consolidou domínio rico em Tarefa, TarefaComentario, Token e Cliente. **Projeto** já tem Domain, Service, API e testes unitários/API; as mutações da Volt (criar, editar, excluir) continuam no Model. **Conta, Time e Convite** permanecem anêmicos: registro, configuração da conta, criação/exclusão de time e ciclo de convite (emitir, aceitar, recusar) mutam Eloquent na Volt ou no controller. A regra desta demanda é manter o comportamento já existente do produto e fazer toda mutação dessas entidades passar pelo Domain compartilhado, sem feature flag.

## 2. Objetivo

Completar a padronização de domínio rico nas entidades ainda anêmicas, para que API e Web (quando existirem) usem a mesma fonte de regras, sem alterar o que o usuário já consegue fazer hoje.

## 3. Escopo

### Dentro

- **Projeto (fechamento Web):** criar, atualizar e excluir na Volt passando pelo Service/Domain já existentes; testes Feature das mutações principais
- **Conta:** criar no registro (`Conta de {nome do usuário}`, vinculada ao usuário); renomear na tela de configuração (somente o owner)
- **Time:** criar com nome obrigatório, vincular à conta do usuário (criando a conta se ainda não existir), adicionar o criador como admin (`nivel_id` 1) e tornar o time o contexto atual; excluir (admin), com troca do time atual se o excluído era o selecionado
- **Convite:** emitir (nome, e-mail, token, status pendente `0`, validade de 7 dias no link, e-mail enfileirado); recusar e-mail que já pertence a outra conta; aceitar (status `1`, membro com `nivel_id` 2, e-mail do usuário autenticado igual ao convite, usuário não pertence a outra conta, convite ainda pendente); recusar (status `2`, mesmas restrições de pendência e e-mail)
- Testes unitários das regras extraídas e preservação dos Feature tests já existentes desses fluxos

### Fora

- Tarefa, TarefaComentario, Token e Cliente (já finalizados)
- Lookups (Tipo, Situação, Prioridade, TimeNivel)
- Anotações e arquivos de projeto
- Nova API de Conta, Time ou Convite
- Reabrir ou alterar o contrato da API de projetos já entregue (`0008`)
- Novas regras de produto (por exemplo encerrar/reabrir projeto como máquina de estados — hoje só existe o campo `dt_fim` no formulário)
- Feature flag / liberação gradual
- Token de API por conta (já coberto em `0006`)

## 4. Premissas

- Feature flag: Não — cutover direto
- Fonte das regras: o que o produto **já faz** (Volt, controllers, testes Feature), não regras novas
- Ordem: fechar Projeto antes de Conta/Time/Convite
- A API de Projeto já orquestra Domain → Service; esta demanda não relança o CRUD de API
- Exclusão de Projeto e de Time continua sendo a exclusão técnica já usada (soft delete na persistência), sem novo comportamento de “arquivar”
- Isolamento por conta/time e políticas de autorização existentes permanecem a fronteira de quem pode mutar
- Discoveries `0003`–`0005` e `0008` documentam o modelo SaaS e a API de projetos; esta fatia é padronização de domínio, não mudança de produto
- Conta/Time/Convite são fluxos SaaS; as regras de convite (status 0/1/2, bloqueio cross-conta, URL assinada, nível 1 no create e 2 no aceite) já estão no código e nos testes atuais

## 5. Considerações de segurança

- Dados sensíveis: nome da conta e do time; e-mail e nome do convidado (PII); token de convite; dados de projeto já isolados por time. O plaintext do convite não deve vazar para UI/logs além do necessário para envio do e-mail
- Autenticação/Autorização: registro cria Conta para o próprio usuário; edição de Conta só pelo owner; Time usa Policy (view membro, update/delete admin); aceite/recusa de convite exige usuário autenticado, e-mail coincidente e convite pendente; Projeto mantém Policy e recorte de time já existentes
- Exposição de APIs: nenhum endpoint novo de Conta/Time/Convite nesta demanda; API de Projeto permanece a já exposta
- Compliance: isolamento multi-tenant (conta/time) e a regra “e-mail não pode pertencer a outra conta” já existem e devem ser preservados; não há requisito PCI informado
- Outras considerações: links de convite são URLs assinadas com expiração de 7 dias; aceite/recusa já recusam convite usado (HTTP 410). Não inventar controles novos além de manter esses invariantes no Domain

## 6. Dúvidas

- Nenhuma crítica em aberto. Recorte das entidades pendentes, ausência de feature flag e preservação do comportamento atual estão fechados.

## 7. Informações ausentes

- Nenhuma lacuna que impeça o planejamento. Domain/API de Projeto já existem; o planejamento deve partir do estado real do código.

## 8. Status

**Pronto para Planejamento?** Sim

Escopo das duas frentes restantes, objetivo (padronizar sem mudar o produto) e ausência de feature flag estão fechados. As regras de negócio já estão no produto e nos testes Feature atuais.
