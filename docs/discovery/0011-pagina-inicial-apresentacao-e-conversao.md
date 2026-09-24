# Discovery: Página inicial para apresentar o RapidTask e converter visitantes

## 1. Resumo

O RapidTask precisa de uma página inicial pública que apresente a ferramenta e converta o visitante em usuário. A página reúne um call to action para experimentar, um carrossel com 4 depoimentos fictícios (somente texto e autor), uma seção com os pontos que a ferramenta oferece — com mudança entre os blocos — e um rodapé simples com formulário de dúvidas. A dúvida enviada fica registrada em uma tabela do produto. A consulta desses registros por uma equipe fica para o futuro e não entra nesta entrega. O call to action leva à criação de conta. Quem cria a conta continua entrando no painel. Trial de 7 dias e meio de pagamento ficam de fora nesta entrega.

## 2. Objetivo

Fazer o visitante entender o RapidTask e seguir para a criação de conta.

## 3. Escopo

### Dentro

- Página inicial pública que apresenta o RapidTask
- Call to action que leva à página de criação de conta
- Carrossel com 4 depoimentos fictícios, para desenvolvimento; cada um exibe somente texto e autor, e o visitante consegue ver todos ao percorrer
- Textos desta entrega:
  - «Passei a enxergar o andamento dos projetos sem cobrar atualização o tempo todo.» — Marina Costa
  - «Tarefas, comentários e clientes no mesmo lugar tiraram a bagunça da nossa rotina.» — Paulo Henrique
  - «O painel me mostra o que precisa de atenção antes da reunião começar.» — Aline Ferreira
  - «Conseguimos acompanhar o trabalho pela tela e pela API, sem planilha paralela.» — Ricardo Nunes
- Seção dos pontos da ferramenta, navegável pela mudança entre os blocos: autenticação; CRUD de tarefas, projetos, clientes e comentários, na interface web e na API; gerenciamento de tokens; dashboard
- Rodapé simples, com formulário de dúvidas: nome, e-mail, telefone e um campo aberto para a dúvida
- Registro da dúvida enviada em uma tabela do produto
- Navegação pela página de modo que o visitante veja todas as seções
- Manter o destino atual do cadastro: o painel

### Fora

- Trial de 7 dias
- Integração com meio de pagamento
- Criação do fluxo de conta em si (o cadastro já existe; esta demanda só direciona o visitante até ele)
- Alterar o redirecionamento depois do cadastro
- Foto, cargo ou outro dado além de texto e autor no depoimento
- Tela ou regra de acesso para uma equipe consultar as dúvidas registradas (fica para o futuro)

## Fluxo

1. O visitante abre a página inicial e percorre as seções: apresentação, pontos da ferramenta, depoimentos e rodapé.
2. Nos pontos da ferramenta, muda de um bloco para o outro.
3. Nos depoimentos, percorre o carrossel dos 4 itens fictícios, cada um só com texto e autor.
4. No rodapé, preenche nome, e-mail, telefone e a dúvida; ao enviar, o registro fica em uma tabela do produto.
5. No call to action, segue para a criação de conta.
6. Depois de criar a conta, entra no painel.

## 4. Premissas

- A página é para quem ainda não tem sessão.
- A criação de conta já existe no produto; o call to action apenas leva até ela.
- Depois do cadastro, o usuário continua indo para o painel. Esta demanda não muda esse destino.
- A entrada atual da aplicação ainda é a tela padrão do framework, sem apresentação do RapidTask.
- Os pontos a exibir são os que o produto já oferece, na ordem informada pelo stakeholder.
- “Navegável” na seção de pontos significa mudança entre os blocos, não menu de âncoras nem só a rolagem da página.
- O carrossel tem exatamente 4 depoimentos fictícios, só com texto e autor. Os nomes não são de clientes reais.
- A dúvida enviada é persistida no produto. No futuro uma equipe poderá ver esses registros; nesta demanda isso não se constrói.
- Stakeholder: Frederico Drumond.
- Trial e pagamento não entram nesta demanda.

## 5. Considerações de segurança

- Dados sensíveis: o formulário coleta nome, e-mail, telefone e o texto da dúvida, e esses dados ficam gravados em uma tabela do produto. Os autores do carrossel são fictícios, criados para desenvolvimento.
- Autenticação/Autorização: a página e o envio da dúvida são públicos. O call to action usa a criação de conta já existente. A consulta dos registros por uma equipe não entra nesta entrega.
- Exposição de APIs: nenhum endpoint novo informado para a página.
- Compliance: nome, e-mail e telefone gravados são dado pessoal (LGPD). Sem pagamento nesta demanda.
- Outras considerações: nenhuma identificada para esta entrega além da coleta e do registro da dúvida.

## 6. Dúvidas

- Nenhuma em aberto.

## 7. Informações ausentes

- Nenhuma.

## 8. Status

**Pronto para Planejamento?** Sim

Escopo, conteúdo do carrossel e destino da dúvida estão fechados. A consulta dos registros por uma equipe ficou explícita para o futuro e fora desta entrega.
