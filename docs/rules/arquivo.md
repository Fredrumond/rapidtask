# Arquivo

Estado do [PR #65](https://github.com/Fredrumond/rapidtask/pull/65) no detalhe do projeto e do [PR #68](https://github.com/Fredrumond/rapidtask/pull/68) na API. O download continua só no painel.

- [Membro do time anexa e acompanha](#membro-do-time-anexa-e-acompanha)
- [Só o dono exclui o arquivo](#só-o-dono-exclui-o-arquivo)
- [Exclusão de arquivo é lógica](#exclusão-de-arquivo-é-lógica)
- [Arquivo aceito tem tipo, tamanho, nome e descrição](#arquivo-aceito-tem-tipo-tamanho-nome-e-descrição)
- [Arquivo de outra conta ou outro time não aparece](#arquivo-de-outra-conta-ou-outro-time-não-aparece)
- [Download de arquivo continua no painel](#download-de-arquivo-continua-no-painel)

## Membro do time anexa e acompanha
- **Mudança:** alterada
- **Antes:** no detalhe do projeto, qualquer membro do time lista, envia e baixa os arquivos
- **Agora:** o mesmo vale no detalhe do projeto e na API, com o token da conta e o time informado. Na API, o envio fica em nome do dono da conta
- **Condição:** em um projeto do time, no painel ou pela API
- **Impacto:** o documento do projeto fica disponível para o time e para a integração

## Só o dono exclui o arquivo
- **Mudança:** alterada
- **Antes:** só quem enviou o arquivo excluía, no painel, com confirmação
- **Agora:** só o dono exclui, no painel e na API. Outro membro do time não exclui. Na API, o dono é o dono da conta
- **Condição:** ao excluir um arquivo do projeto
- **Impacto:** um colega do time vê e baixa o arquivo, mas não o remove

## Exclusão de arquivo é lógica
- **Mudança:** alterada
- **Antes:** excluir o arquivo o ocultava da lista do projeto e o download deixava de encontrá-lo
- **Agora:** o mesmo vale no painel e na API; a listagem deixa de mostrar o arquivo e o registro permanece guardado
- **Condição:** quando o dono confirma a exclusão
- **Impacto:** o arquivo some do projeto sem ser apagado de vez

## Arquivo aceito tem tipo, tamanho, nome e descrição
- **Mudança:** criada
- **Antes:** não existia
- **Agora:** o envio exige nome e descrição. O arquivo precisa ser pdf, png, jpg, jpeg, doc, docx, xls, xlsx ou txt, com no máximo 10 MB. Fora disso, o envio é recusado, no painel e na API
- **Condição:** ao enviar um arquivo do projeto
- **Impacto:** não entra anexo sem identificação, nem arquivo de outro tipo ou maior que o limite

## Arquivo de outra conta ou outro time não aparece
- **Mudança:** alterada
- **Antes:** outra conta não listava, não baixava e não excluía o arquivo
- **Agora:** outra conta não lista, não envia, não baixa e não exclui. Na API, projeto de outro time não é encontrado
- **Condição:** quando o projeto não é do time e da conta de quem acessa
- **Impacto:** o anexo fica dentro da empresa e do time

## Download de arquivo continua no painel
- **Mudança:** criada
- **Antes:** não existia
- **Agora:** baixar o arquivo continua só no painel; a API lista, envia e exclui, mas não entrega o arquivo
- **Condição:** ao baixar um arquivo do projeto
- **Impacto:** a integração não substitui o download feito por quem está no painel
