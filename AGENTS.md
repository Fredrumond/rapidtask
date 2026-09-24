# RapidTask

## Visão geral

Plataforma web de gerenciamento de projetos e tarefas, em Laravel 13 e PHP 8.4. O domínio segue conta → time → clientes → projetos → tarefas (comentários, arquivos e anotações). A conta é a fronteira de isolamento; o time ativo é a unidade operacional de query e de UI.

Leia antes de alterar código:

- Arquitetura web e checklist de entidade nova: `docs/arquitetura.md`
- Isolamento por conta (sessão, scopes, policies, nav): `docs/references/isolamento-conta-web.md`
- Padrões da API: `docs/QUICK_START_GUIDE.md`
- Contrato atual do token: `docs/adr/0003-token-api-por-conta-e-time-id.md`
- CRUD de tarefas: `docs/references/api-tarefas.md`

Referência de CRUD web: **Cliente**. Referência de API: **Tarefa**.

## Build e testes

Ambiente local com Docker:

```bash
docker compose up -d --build
docker compose exec app php artisan migrate --seed
docker compose --profile assets run --rm node sh -c "npm install && npm run build"
```

A aplicação fica em http://localhost:8080. O MySQL do compose publica a porta 3307 no host.

Testes (Pest 4) e estilo (Laravel Pint):

```bash
docker compose exec app php artisan test
docker compose exec app vendor/bin/pint --test
```

Fora do Docker, `composer test` limpa o config e roda `php artisan test`. O CI em `.github/workflows/ci.yml` executa Pint e a suíte no push e no pull request.

## Arquitetura

CRUD de tela não usa controller. Cada página é Volt single-file em `resources/views/livewire/pages/{entidade}/`, com rotas `Volt::route` no grupo `auth` de `routes/web.php`.

A API usa Controller → Service → Repository → Model. O Service converte Model → Domain → DTO. Controllers HTTP clássicos ficam restritos ao que o Volt não cobre (convite assinado, download, fluxo Breeze, endpoints da API).

O tenant da API é o model `Conta` autenticado por Bearer Sanctum. `time_id` é obrigatório na query (GET) ou no body (POST, PUT, DELETE). O header `X-Time-Id` do ADR 0001 não vale mais.

## Convenções

- No create, gravar `usuario_id` e `time_id` com `auth()->id()` e `current_time_id()`. Filho sem coluna `time_id` usa `BelongsToTeamViaProjeto` ou `BelongsToTeamViaTarefa`.
- Policy por convenção (`Cliente` → `ClientePolicy`), sempre com `HandlesTeamAuthorization`. Não registrar policy no provider.
- O route model binding respeita o global scope: recurso de outro time ou de outra conta responde 404.
- Entidade nova precisa de teste em que a conta B não vê nem altera recurso da conta A. Use `criarCenarioDoisTimes()` em `tests/Pest.php`. A sessão de teste leva `current_time_id` e `current_conta_id`.
- Validação do CRUD web fica na página Volt (`$this->validate()`). Form Request é para HTTP clássico (upload e API).
- Não copiar comportamento da branch `versao_pre_refatoracao` (Laravel 5.7).
- Changelog do produto mora em `config/versoes.php` e na rota `/versoes`, não em tabela de negócio.
