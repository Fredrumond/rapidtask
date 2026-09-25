# Token

Estado do [PR #54](https://github.com/Fredrumond/rapidtask/pull/54). O token de API autentica a conta. As regras de prazo e de cadastro seguem as da entrega anterior.

- [Um token ativo por conta](#um-token-ativo-por-conta)
- [Token pessoal deixa de valer](#token-pessoal-deixa-de-valer)
- [Só o dono gera e revoga o token](#só-o-dono-gera-e-revoga-o-token)
- [Token sem prazo de validade](#token-sem-prazo-de-validade)
- [Valor do token visível só na geração](#valor-do-token-visível-só-na-geração)
- [Sem cadastro pela API](#sem-cadastro-pela-api)

## Um token ativo por conta
- **Mudança:** alterada
- **Antes:** cada usuário tinha no máximo um token de API ativo; gerar outro revogava o anterior na hora
- **Agora:** cada conta tem no máximo um token de API ativo; gerar outro revoga o anterior na hora
- **Condição:** ao gerar o token nas configurações da conta
- **Impacto:** a integração que usava o token anterior da conta deixa de autenticar

## Token pessoal deixa de valer
- **Mudança:** criada
- **Antes:** o token pessoal do usuário autenticava a API
- **Agora:** os tokens pessoais deixam de autenticar de uma vez; não há período em que os dois modelos convivem
- **Condição:** a partir desta entrega
- **Impacto:** integrações que ainda usam o token do perfil precisam gerar o token da conta

## Só o dono gera e revoga o token
- **Mudança:** criada
- **Antes:** qualquer usuário gerava e revogava o próprio token no perfil
- **Agora:** só o dono da conta gera e revoga o token, nas configurações da conta; o perfil não oferece mais essa gestão
- **Condição:** na gestão do token de API
- **Impacto:** membro que não é dono não cria nem invalida a credencial da empresa

## Token sem prazo de validade
- **Mudança:** criada
- **Antes:** não existia
- **Agora:** o token não expira por tempo; só deixa de valer se for revogado ou substituído por um novo
- **Condição:** enquanto o token estiver ativo
- **Impacto:** quem integra não precisa renovar o token por prazo

## Valor do token visível só na geração
- **Mudança:** alterada
- **Antes:** o valor do token aparecia só no momento da geração no perfil; ao reabrir o perfil, via-se que havia token ativo, sem o valor
- **Agora:** o valor aparece só no momento da geração nas configurações da conta; ao reabrir a tela, o dono vê que há token ativo, sem o valor
- **Condição:** depois de gerar o token da conta
- **Impacto:** quem perde o valor precisa gerar outro, o que revoga o anterior

## Sem cadastro pela API
- **Mudança:** criada
- **Antes:** não existia
- **Agora:** só quem já tem conta na web gera token; a API não cadastra usuário
- **Condição:** em qualquer acesso à API nesta versão
- **Impacto:** quem ainda não tem conta precisa se cadastrar pelo painel antes de integrar
