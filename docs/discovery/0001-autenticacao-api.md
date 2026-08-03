# Discovery: T001-A — Autenticação da API

## 1. Resumo

O RapidTask ainda não possui camada de API. Para viabilizar integrações externas, é necessário criar a infraestrutura de autenticação via token, permitindo que um usuário já cadastrado gere credenciais de acesso à API. O projeto não conta com Sanctum ou Passport instalados; o modelo `User` também não possui o trait `HasApiTokens`. A gestão do token (geração e revogação) ficará disponível tanto via painel web quanto via API.

## 2. Objetivo

Permitir que um usuário do RapidTask gere e revogue um token de acesso pessoal para consumir a API via autenticação Bearer — com gestão acessível pelo painel web.

## 3. Escopo

### Dentro

- Instalação e configuração do Laravel Sanctum
- Adição do trait `HasApiTokens` ao model `User`
- Criação do arquivo `routes/api.php`
- Endpoint para geração de token (`POST /api/tokens`) — gera novo token e revoga o anterior automaticamente
- Endpoint para revogação do token atual (`DELETE /api/tokens`)
- Middleware de autenticação `auth:sanctum` aplicado às rotas da API
- Página/seção no painel web para o usuário gerar e revogar seu token

### Fora

- Autenticação OAuth2 / Passport
- Escopos de permissão granulares por token (pode ser evolução futura)
- Múltiplos tokens simultâneos por usuário
- Endpoints de qualquer outra entidade além dos tokens

## 4. Premissas

- Feature flag: Não
- Token é vitalício — não expira; só é invalidado por revogação explícita ou pela geração de um novo token
- Um usuário pode ter no máximo **um token ativo** por vez; gerar novo revoga o anterior automaticamente
- A gestão do token ficará disponível no painel web (UI), além do endpoint de API
- O usuário já está cadastrado na plataforma web; não haverá cadastro via API nesta versão
- O arquivo `routes/api.php` ainda não existe no projeto

## 5. Dúvidas

Todas as dúvidas foram respondidas.

## 6. Informações ausentes

Nenhuma.

## 7. Status

**Pronto para Planejamento?** Sim

Escopo e premissas completamente definidos. Pode avançar para planejamento técnico.
