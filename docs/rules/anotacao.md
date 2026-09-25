# Anotação

Estado do [PR #66](https://github.com/Fredrumond/rapidtask/pull/66) no detalhe do projeto e do [PR #67](https://github.com/Fredrumond/rapidtask/pull/67) na API.

- [Membro do time anota e acompanha](#membro-do-time-anota-e-acompanha)
- [Só o autor edita ou exclui a anotação](#só-o-autor-edita-ou-exclui-a-anotação)
- [Exclusão de anotação é lógica](#exclusão-de-anotação-é-lógica)
- [Anotação de outra conta ou outro time não aparece](#anotação-de-outra-conta-ou-outro-time-não-aparece)

## Membro do time anota e acompanha
- **Mudança:** alterada
- **Antes:** no detalhe do projeto, qualquer membro do time lista e cria anotações
- **Agora:** o mesmo vale no detalhe do projeto e na API, com o token da conta e o time informado. Na API, a anotação fica em nome do dono da conta
- **Condição:** em um projeto do time, no painel ou pela API
- **Impacto:** a nota do projeto fica registrada para o time e para a integração

## Só o autor edita ou exclui a anotação
- **Mudança:** alterada
- **Antes:** só quem escreveu a anotação editava ou excluía, no painel
- **Agora:** só o autor edita ou exclui, no painel e na API. Outro membro do time vê e cria, mas não altera o texto alheio
- **Condição:** ao editar ou excluir uma anotação do projeto
- **Impacto:** a nota não é reescrita por outra pessoa do time

## Exclusão de anotação é lógica
- **Mudança:** alterada
- **Antes:** excluir a anotação a ocultava da lista e o registro permanecia guardado
- **Agora:** o mesmo vale no painel e na API; a listagem deixa de mostrar a anotação
- **Condição:** quando o autor exclui a própria anotação
- **Impacto:** a nota some do projeto sem ser apagada de vez

## Anotação de outra conta ou outro time não aparece
- **Mudança:** alterada
- **Antes:** outra conta não via nem alterava a anotação
- **Agora:** outra conta não lista, não cria, não edita e não exclui, no painel nem na API
- **Condição:** quando o projeto não é do time e da conta de quem acessa
- **Impacto:** a nota fica dentro da empresa e do time
