# Discovery: T001-B — CRUD de Tarefas via API

## 1. Resumo

Com a autenticação de API estabelecida (T001-A), o próximo passo é expor o recurso de tarefas para sistemas externos. O model `Tarefa` já existe no projeto com campos de título, descrição, situação, prioridade, datas, tempo estimado, projeto e usuário responsável. A API respeitará o isolamento por time, já implementado internamente via `BelongsToTeamViaProjeto`. O time será identificado via parâmetro explícito na requisição.

## 2. Objetivo

Disponibilizar endpoints REST de CRUD para tarefas, com autenticação Bearer, permitindo que sistemas externos criem, leiam, atualizem e excluam tarefas dentro do contexto de um time específico.

## 3. Escopo

### Dentro

- `GET /api/tarefas` — listagem simples de tarefas do time informado (sem filtros)
- `GET /api/tarefas/{id}` — detalhe de uma tarefa
- `POST /api/tarefas` — criação de tarefa
- `PUT /api/tarefas/{id}` — atualização completa de tarefa
- `DELETE /api/tarefas/{id}` — exclusão (soft delete, consistente com o model)
- Parâmetro `time_id` obrigatório nas requisições para identificar o contexto do time
- Validação de pertencimento do usuário autenticado ao time informado
- Documentação Swagger/OpenAPI dos endpoints

### Fora

- Filtros na listagem (situação, prioridade, projeto, usuário, etc.)
- `PATCH` (atualização parcial)
- Endpoints de comentários (`TarefaComentario`) e histórico (`TarefaHistorico`)
- Endpoints de projetos, clientes, times ou outras entidades
- Upload de arquivos via API
- Webhooks / eventos ao criar/atualizar tarefa
- Paginação avançada (pode ser evolução futura)

## 4. Premissas

- Feature flag: Não
- **T001-A (autenticação) é pré-requisito**; este discovery só entra em planejamento após T001-A concluído
- O `time_id` é passado como parâmetro na requisição (query string ou body, a definir no planejamento)
- O isolamento por time será validado a partir do `time_id` recebido — o usuário autenticado deve ser membro do time
- Soft delete na exclusão (alinhado ao comportamento do model `Tarefa`)
- A documentação Swagger será gerada junto com os endpoints (ex.: via `darkaonline/l5-swagger`)
- Listagem sem filtros nesta versão; retorna todas as tarefas do time

## 5. Dúvidas

- O `time_id` deve ir via query string (`GET /api/tarefas?time_id=1`) ou via header HTTP? — Impacta o contrato da API; decidir no planejamento técnico

## 6. Informações ausentes

- Definição se a resposta deve incluir relacionamentos aninhados (ex.: `situacao`, `prioridade`, `projeto` como objetos) ou apenas seus IDs — a definir no planejamento técnico

## 7. Status

**Pronto para Planejamento?** Sim

Escopo e objetivo claros. A dúvida sobre a forma de envio do `time_id` e o formato da resposta são decisões técnicas que podem ser resolvidas no planejamento sem bloquear o início do trabalho.
