# Projeto

Estado do [PR #60](https://github.com/Fredrumond/rapidtask/pull/60). O painel de projetos continua como estava. Estas regras valem para a API.

- [Projeto na API só no time informado](#projeto-na-api-só-no-time-informado)
- [Listagem de projetos sem filtros](#listagem-de-projetos-sem-filtros)
- [Atualização do projeto é completa](#atualização-do-projeto-é-completa)
- [Exclusão de projeto é lógica](#exclusão-de-projeto-é-lógica)
- [Projeto precisa de nome, sigla e cliente](#projeto-precisa-de-nome-sigla-e-cliente)
- [Cliente do projeto no mesmo time](#cliente-do-projeto-no-mesmo-time)
- [Projeto de outra conta não aparece](#projeto-de-outra-conta-não-aparece)

## Projeto na API só no time informado
- **Mudança:** criada
- **Antes:** não existia
- **Agora:** listar e ver o projeto informam o time na consulta; criar, alterar e excluir informam o time no corpo. Sem ele, ou com valor inválido, a chamada é recusada. A lista é do time informado, não da conta inteira
- **Condição:** em qualquer operação de projeto pela API
- **Impacto:** a integração precisa informar o time em cada chamada

## Listagem de projetos sem filtros
- **Mudança:** criada
- **Antes:** não existia
- **Agora:** a listagem devolve todos os projetos do time, sem filtrar e sem paginar
- **Condição:** ao listar projetos pela API
- **Impacto:** quem integra recebe o conjunto inteiro do time e filtra do lado de fora, se precisar

## Atualização do projeto é completa
- **Mudança:** criada
- **Antes:** não existia
- **Agora:** alterar o projeto exige reenviar os dados completos; não há atualização parcial
- **Condição:** ao alterar um projeto pela API
- **Impacto:** a integração precisa enviar os dados do projeto mesmo quando muda um só campo

## Exclusão de projeto é lógica
- **Mudança:** criada
- **Antes:** não existia
- **Agora:** excluir o projeto pela API o oculta das consultas; o registro não é apagado de vez
- **Condição:** ao excluir um projeto do time informado
- **Impacto:** o projeto some da listagem e do detalhe, e o registro permanece guardado

## Projeto precisa de nome, sigla e cliente
- **Mudança:** criada
- **Antes:** não existia
- **Agora:** criar o projeto exige nome, sigla e cliente; descrição e datas podem ficar em branco
- **Condição:** ao criar um projeto pela API
- **Restrições:** nesta entrega a API não cadastra cliente; quem integra já precisa conhecer o cliente
- **Impacto:** não entra um projeto sem nome, sem sigla ou sem cliente

## Cliente do projeto no mesmo time
- **Mudança:** criada
- **Antes:** não existia
- **Agora:** o cliente do projeto precisa ser do mesmo time informado na chamada
- **Condição:** ao criar ou alterar um projeto pela API
- **Impacto:** não é possível vincular o projeto a um cliente de outro time

## Projeto de outra conta não aparece
- **Mudança:** criada
- **Antes:** não existia
- **Agora:** o token de uma conta não lista, vê, cria, altera nem exclui projetos de outra conta; time de outra conta é recusado
- **Condição:** quando o time ou o projeto não é da conta autenticada
- **Impacto:** a integração de uma empresa não alcança os projetos de outra
