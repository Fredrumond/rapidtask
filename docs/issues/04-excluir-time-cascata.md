# [CRÍTICO] Qualquer usuário autenticado apaga qualquer time em cascata

## Severidade
Crítico / Segurança

## Descrição
`TimeController@excluirTime` usa `Time::find($id)` sem verificar membership/`nivel_id` e remove membros, projetos e dados relacionados.

## Critério de aceite
- Apenas admin do time (ou dono) pode excluir
- Soft delete + confirmação
- Policy `TimePolicy@delete`
