# [CRÍTICO] Aceitar/recusar convite sem autenticação

## Severidade
Crítico / Segurança

## Descrição
`/time-membro/aceitar/{id}` e `/time-membro/recusar/{id}` são públicas. Qualquer pessoa com o ID do convite pode criar usuário e entrar no time (`TimeMembroController@aceitarConvite`).

## Critério de aceite
- Links assinados (signed URLs) ou token único de uso único
- Aceite exige autenticação quando o e-mail já tem conta
- Testes cobrindo abuso por ID enumerável
