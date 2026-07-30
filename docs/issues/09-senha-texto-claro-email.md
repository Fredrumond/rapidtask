# [ALTO] Senha gerada enviada em texto claro no e-mail de boas-vindas

## Severidade
Alto / Segurança / Privacidade

## Descrição
`TimeMembroController` gera senha e envia no corpo do e-mail. E-mails transitam em texto plano e ficam no provedor.

## Critério de aceite
- Convite com link de definição de senha (token)
- Senha nunca enviada por e-mail
