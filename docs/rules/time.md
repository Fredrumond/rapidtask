# Time

Estado do [PR #53](https://github.com/Fredrumond/rapidtask/pull/53). A exclusão de time deixa de ser aberta a qualquer pessoa autenticada.

- [Só o admin exclui o time](#só-o-admin-exclui-o-time)
- [Exclusão de time é lógica](#exclusão-de-time-é-lógica)
- [Excluir o time ativo passa para outro](#excluir-o-time-ativo-passa-para-outro)

## Só o admin exclui o time
- **Mudança:** criada
- **Antes:** qualquer pessoa autenticada podia apagar qualquer time
- **Agora:** só o administrador daquele time exclui, e precisa confirmar; administrador de outra conta não exclui, mesmo com vínculo indevido
- **Condição:** ao excluir um time
- **Impacto:** membro comum não remove o time, e um time de outra empresa permanece intocado

## Exclusão de time é lógica
- **Mudança:** criada
- **Antes:** a exclusão removia o time em cascata
- **Agora:** o time deixa de aparecer nas consultas, sem apagar o registro de vez
- **Condição:** quando o administrador confirma a exclusão
- **Impacto:** o time some da operação e o histórico permanece guardado

## Excluir o time ativo passa para outro
- **Mudança:** criada
- **Antes:** não existia
- **Agora:** se o time excluído era o ativo, a sessão passa para outro time da mesma pessoa
- **Condição:** ao excluir o time que está em uso
- **Impacto:** a pessoa continua no painel, em outro time, sem ficar sem contexto
