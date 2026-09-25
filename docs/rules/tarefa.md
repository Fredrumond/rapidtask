# Tarefa

Estado do [PR #54](https://github.com/Fredrumond/rapidtask/pull/54) para o contrato de time na API. O [PR #56](https://github.com/Fredrumond/rapidtask/pull/56) passou a recusar situação, arquivamento, data e título inválidos, no painel e na API.

- [Situação da tarefa só muda por um caminho permitido](#situação-da-tarefa-só-muda-por-um-caminho-permitido)
- [Tarefa finalizada ganha data de fim](#tarefa-finalizada-ganha-data-de-fim)
- [Tarefa arquivada não se altera](#tarefa-arquivada-não-se-altera)
- [Início da tarefa não passa da data prevista](#início-da-tarefa-não-passa-da-data-prevista)
- [Tarefa precisa de título](#tarefa-precisa-de-título)
- [Time obrigatório na API de tarefas](#time-obrigatório-na-api-de-tarefas)
- [Time precisa ser da conta do token](#time-precisa-ser-da-conta-do-token)
- [Tarefa de outro time não aparece](#tarefa-de-outro-time-não-aparece)
- [Projeto da tarefa no mesmo time](#projeto-da-tarefa-no-mesmo-time)
- [Listagem sem filtros](#listagem-sem-filtros)
- [Exclusão de tarefa é lógica](#exclusão-de-tarefa-é-lógica)
- [Atualização da tarefa é completa](#atualização-da-tarefa-é-completa)

## Situação da tarefa só muda por um caminho permitido
- **Mudança:** criada
- **Antes:** a alteração era gravada mesmo quando a situação não podia mudar
- **Agora:** Nova pode ir para andamento, em espera ou finalizada; andamento pode ir para em espera, finalizada ou nova; em espera pode ir para andamento ou finalizada; finalizada não volta. Permanecer na mesma situação é permitido
- **Condição:** ao mudar a situação no painel ou pela API
- **Impacto:** uma tarefa concluída não reabre, e uma tarefa em espera não volta a ficar nova

## Tarefa finalizada ganha data de fim
- **Mudança:** criada
- **Antes:** uma tarefa podia ficar finalizada sem data de fim
- **Agora:** ao finalizar, se não houver data de fim, vale o dia da finalização
- **Condição:** quando a situação passa a finalizada
- **Impacto:** toda tarefa concluída tem uma data de encerramento

## Tarefa arquivada não se altera
- **Mudança:** criada
- **Antes:** uma tarefa arquivada ainda podia ser alterada
- **Agora:** tarefa arquivada não muda de dados nem de situação; dá para recuperar, e só então alterar de novo. Arquivar de novo uma já arquivada, ou recuperar uma que não está arquivada, é recusado
- **Condição:** enquanto a tarefa estiver arquivada
- **Impacto:** o arquivo fica estável até alguém recuperar a tarefa

## Início da tarefa não passa da data prevista
- **Mudança:** criada
- **Antes:** a data de início podia ficar depois da data prevista
- **Agora:** se as duas datas existem, o início não pode ser posterior à prevista
- **Condição:** ao criar ou reagendar a tarefa
- **Impacto:** o prazo não começa depois do dia em que a tarefa deveria terminar

## Tarefa precisa de título
- **Mudança:** criada
- **Antes:** um título vazio podia ser gravado
- **Agora:** a tarefa sem título é recusada
- **Condição:** ao criar ou renomear a tarefa
- **Impacto:** não entra na lista uma tarefa sem nome

## Time obrigatório na API de tarefas
- **Mudança:** alterada
- **Antes:** listar, ver, criar, alterar ou excluir tarefa pela API exigia o time no header; sem ele, ou com valor inválido, a chamada era recusada
- **Agora:** a consulta informa o time na query; criar, alterar e excluir informam o time no corpo. Sem ele, ou com valor inválido, a chamada é recusada
- **Condição:** em qualquer operação de tarefa pela API
- **Impacto:** a integração precisa informar o time em cada chamada, no lugar certo conforme a operação

## Time precisa ser da conta do token
- **Mudança:** alterada
- **Antes:** quem não pertencia ao time informado não acessava as tarefas daquele time
- **Agora:** o time informado precisa pertencer à conta autenticada pelo token; time de outra conta é recusado
- **Condição:** quando o time informado não é da conta do token
- **Impacto:** o token de uma empresa não opera tarefas de outra

## Tarefa de outro time não aparece
- **Mudança:** criada
- **Antes:** não existia
- **Agora:** ver, alterar ou excluir uma tarefa de outro time responde como se a tarefa não existisse
- **Condição:** quando a tarefa não pertence ao time informado na chamada
- **Impacto:** uma integração não descobre tarefas de times aos quais não tem acesso

## Projeto da tarefa no mesmo time
- **Mudança:** criada
- **Antes:** não existia
- **Agora:** a tarefa só pode ser criada ou alterada com um projeto ativo do mesmo time
- **Condição:** ao criar ou alterar uma tarefa pela API
- **Impacto:** não é possível vincular a tarefa a um projeto de outro time

## Listagem sem filtros
- **Mudança:** criada
- **Antes:** não existia
- **Agora:** a listagem devolve todas as tarefas do time, sem filtrar por situação, prioridade, projeto ou responsável
- **Condição:** ao listar tarefas pela API
- **Impacto:** quem integra recebe o conjunto inteiro do time e filtra do lado de fora, se precisar

## Exclusão de tarefa é lógica
- **Mudança:** criada
- **Antes:** não existia
- **Agora:** excluir a tarefa pela API a oculta das consultas; o registro não é apagado de vez
- **Condição:** ao excluir uma tarefa do time informado
- **Impacto:** a tarefa some da listagem e do detalhe, e o histórico permanece guardado

## Atualização da tarefa é completa
- **Mudança:** criada
- **Antes:** não existia
- **Agora:** alterar a tarefa exige reenviar os dados completos; não há atualização parcial
- **Condição:** ao alterar uma tarefa pela API
- **Impacto:** a integração precisa enviar título, projeto, tipo, situação e prioridade mesmo quando muda um só campo
