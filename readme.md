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

## Testes

```bash
docker compose exec app php artisan test
```

## Branch de rollback

O legado Laravel 5.7 + Docker antigo está na branch `versao_pre_refatoracao`.
