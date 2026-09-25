# Cliente

Estado do [PR #58](https://github.com/Fredrumond/rapidtask/pull/58) no painel. O [PR #64](https://github.com/Fredrumond/rapidtask/pull/64) expõe o mesmo cliente na API, no time informado.

- [Cliente na API só no time informado](#cliente-na-api-só-no-time-informado)
- [Cliente precisa de nome](#cliente-precisa-de-nome)
- [Contato do cliente é opcional](#contato-do-cliente-é-opcional)
- [Exclusão de cliente é lógica](#exclusão-de-cliente-é-lógica)
- [Cliente de outro time não aparece](#cliente-de-outro-time-não-aparece)

## Cliente na API só no time informado
- **Mudança:** criada
- **Antes:** o cliente só existia no painel
- **Agora:** listar, ver, criar, alterar e excluir o cliente pela API exigem o token da conta e o time. A consulta informa o time na query; criar, alterar e excluir informam o time no corpo. Sem token, sem time ou com time de outra conta, a chamada é recusada. Nome continua obrigatório, contato continua opcional e a exclusão continua apenas ocultando o registro
- **Condição:** em qualquer operação de cliente pela API
- **Impacto:** a integração passa a cadastrar o cliente do time, no mesmo limite do painel

## Cliente precisa de nome
- **Mudança:** criada
- **Antes:** não existia
- **Agora:** criar ou editar um cliente sem nome é recusado
- **Condição:** ao salvar o cliente
- **Impacto:** não entra na lista um cliente sem nome

## Contato do cliente é opcional
- **Mudança:** criada
- **Antes:** não existia
- **Agora:** e-mail e telefone podem ficar em branco; o cliente pode ser cadastrado só com o nome
- **Condição:** ao criar ou editar o cliente
- **Impacto:** o contato não impede o cadastro

## Exclusão de cliente é lógica
- **Mudança:** criada
- **Antes:** não existia
- **Agora:** excluir o cliente o oculta das consultas; o registro não é apagado de vez
- **Condição:** ao excluir um cliente do time
- **Impacto:** o cliente some da lista e o registro permanece guardado

## Cliente de outro time não aparece
- **Mudança:** alterada
- **Antes:** o cliente de outro time não aparecia e não podia ser alterado no painel
- **Agora:** no painel e na API, o cliente de outro time não aparece e não pode ser alterado; o token de uma conta não alcança o cliente de outra
- **Condição:** quando o cliente não é do time e da conta de quem acessa
- **Impacto:** cada time só vê e edita os próprios clientes, também pela integração
