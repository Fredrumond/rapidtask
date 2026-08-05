# RapidTask

Plataforma web de gerenciamento de projetos e tarefas (estudo / refatoração Laravel 13).

## Stack

| Camada | Tecnologias |
|--------|-------------|
| Backend | Laravel 13, PHP 8.4 |
| Frontend | Livewire 3 + Volt, Alpine, Tailwind 4, Vite |
| Banco | MySQL 8.4 |
| Auth | Laravel Breeze (Livewire) |
| Testes | Pest 4 |

## Arquitetura

Padrões do projeto e checklist para criar uma entidade nova: [`docs/arquitetura.md`](docs/arquitetura.md).

Isolamento web por conta (sessão, scopes, policies e nav): [`docs/references/isolamento-conta-web.md`](docs/references/isolamento-conta-web.md).

## API

CRUD de tarefas, header `X-Time-Id` e Swagger: [`docs/references/api-tarefas.md`](docs/references/api-tarefas.md).

Documentação interativa (com stack Docker no ar): http://localhost:8080/api/documentation

## Docker (recomendado)

```bash
docker compose up -d --build
docker compose exec app php artisan migrate --seed
docker compose --profile assets run --rm node sh -c "npm install && npm run build"
```

Acesse http://localhost:8080 — registre-se, crie um time e use o painel.

### Serviços

| Serviço | Porta |
|---------|-------|
| nginx | 8080 |
| mysql | 3307 |
| queue | worker interno |

## Histórico de versões

A rota autenticada `/versoes` (com redirect da antiga `/versions`) mostra a linha do tempo de
releases, com filtro por maturidade e busca. Para registrar uma entrega, edite `config/versoes.php`
— o primeiro release do array é considerado a versão atual.

## Testes

```bash
docker compose exec app php artisan test
```

## Branch de rollback

O legado Laravel 5.7 + Docker antigo está na branch `versao_pre_refatoracao`.
