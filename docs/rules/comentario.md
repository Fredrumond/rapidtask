# Comentário

Estado do [PR #55](https://github.com/Fredrumond/rapidtask/pull/55). Comentário de tarefa volta ao painel e à API, no mesmo isolamento da conta e do time. O [PR #56](https://github.com/Fredrumond/rapidtask/pull/56) passou a recusar comentário vazio.

- [Comentário precisa de texto](#comentário-precisa-de-texto)
- [Membro do time comenta e acompanha](#membro-do-time-comenta-e-acompanha)
- [Só o autor edita ou exclui o comentário](#só-o-autor-edita-ou-exclui-o-comentário)
- [Comentário mais recente no topo](#comentário-mais-recente-no-topo)
- [Exclusão de comentário é lógica](#exclusão-de-comentário-é-lógica)
- [Comentário de outra conta ou outro time não aparece](#comentário-de-outra-conta-ou-outro-time-não-aparece)

## Comentário precisa de texto
- **Mudança:** criada
- **Antes:** um comentário vazio ou só com espaços podia ser gravado
- **Agora:** criar ou editar um comentário sem texto é recusado, no painel e na API
- **Condição:** ao criar ou editar o comentário
- **Impacto:** a conversa da tarefa não ganha uma mensagem em branco

## Membro do time comenta e acompanha
- **Mudança:** criada
- **Antes:** a tela de comentários tinha se perdido na migração; não dava para comentar a tarefa no painel nem pela API
- **Agora:** qualquer membro do time cria e lista comentários da tarefa, na visualização da tarefa e pela API
- **Condição:** em uma tarefa do time ao qual a pessoa pertence, ou do time informado pela conta autenticada
- **Impacto:** a conversa da tarefa volta a ficar registrada para o time

## Só o autor edita ou exclui o comentário
- **Mudança:** criada
- **Antes:** editar ou excluir dependia só de pertencer ao time, não de ter escrito o comentário
- **Agora:** só quem escreveu o comentário edita ou exclui; outro membro do time vê e comenta, mas não altera o texto alheio
- **Condição:** ao editar ou excluir um comentário, no painel ou pela API
- **Impacto:** o histórico da conversa não é reescrito por outra pessoa do time

## Comentário mais recente no topo
- **Mudança:** criada
- **Antes:** não existia
- **Agora:** a lista mostra o comentário mais recente primeiro
- **Condição:** ao abrir os comentários da tarefa
- **Impacto:** quem acompanha a tarefa vê primeiro o que acabou de ser dito

## Exclusão de comentário é lógica
- **Mudança:** criada
- **Antes:** não existia
- **Agora:** excluir o comentário o oculta da lista; o registro não é apagado de vez
- **Condição:** quando o autor exclui o próprio comentário
- **Impacto:** o comentário some da conversa e o registro permanece guardado

## Comentário de outra conta ou outro time não aparece
- **Mudança:** criada
- **Antes:** não existia
- **Agora:** quem está em outra conta ou em outro time não lista, cria, edita nem exclui os comentários da tarefa
- **Condição:** quando a tarefa não é do time e da conta de quem acessa
- **Impacto:** a conversa da tarefa fica dentro da empresa e do time
