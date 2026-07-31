# Arquitetura do RapidTask

Guia do que o projeto usa hoje e do caminho a seguir ao criar uma entidade nova.
Referência canônica de CRUD: **Cliente** (`app/Models/Cliente.php` + `resources/views/livewire/pages/clientes/`).

---

## Stack

| Camada | Tecnologia |
|--------|------------|
| Backend | Laravel 13, PHP 8.4 |
| UI | Livewire 3 + Volt (páginas single-file), Alpine (via Livewire) |
| CSS / build | Tailwind 4, Vite |
| Auth | Laravel Breeze (Livewire) |
| Banco | MySQL 8.4 |
| Testes | Pest 4 |
| Infra local | Docker Compose (nginx, app, mysql, queue) |

---

## Fluxo de domínio

```
Usuário autenticado
  → time ativo (sessão)
    → clientes
      → projetos
        → tarefas (+ comentários, arquivos, anotações)
```

O **time ativo** define o tenant de quase toda query e autorização.
Changelog do produto: rota `/versoes` + dados em `config/versoes.php` (não é entidade de negócio).

---

## Onde mora o quê

```
app/Models/                    # Eloquent
app/Models/Concerns/           # BelongsToTeam, TeamScope, …
app/Policies/                  # Autorização por recurso
app/Policies/Concerns/         # HandlesTeamAuthorization
app/Support/                   # CurrentTeam, Versoes, helpers
app/Http/Middleware/           # SetCurrentTeam
app/Http/Controllers/          # Só casos especiais (não CRUD)
resources/views/livewire/pages/{entidade}/
resources/views/livewire/layout/navigation.blade.php
resources/views/layouts/app.blade.php
routes/web.php                 # Volt::route no grupo auth
database/migrations/
database/factories/
database/seeders/
tests/Feature/
tests/Pest.php                 # seedLookups(), criarCenarioDoisTimes()
```

---

## Multi-tenant (time)

| Peça | Path | Papel |
|------|------|--------|
| Sessão | `App\Support\CurrentTeam` | Chave `current_time_id` |
| Helper | `current_time_id()` em `app/Support/helpers.php` | Atalho para o time ativo |
| Middleware | `SetCurrentTeam` | Se logado sem time na sessão → primeiro time do usuário |
| Scope | `BelongsToTeam` / `TeamScope` | Filtra queries pelo time |
| Policy | `HandlesTeamAuthorization` | `canAccessTeam` / `canAccessCurrentTeam` |
| UI | `navigation.blade.php` → `switchTeam()` | Troca o time na sessão |

Traits de escopo no model:

| Trait | Quando | Exemplos |
|-------|--------|----------|
| `BelongsToTeam` | Tabela tem `time_id` | Cliente, Projeto |
| `BelongsToTeamViaProjeto` | Tenant via `projeto.time_id` | Tarefa, ProjetoArquivo |
| `BelongsToTeamViaTarefa` | Tenant via tarefa → projeto | TarefaComentario |
| `ScopedToMemberTeams` | O próprio Time | Time |

Route model binding (`{cliente}`, `{projeto}`, …) respeita o global scope → recurso de outro time vira **404**.

---

## UI: Livewire Volt

CRUD de domínio **não** usa controller. Cada tela é um Volt single-file em:

`resources/views/livewire/pages/{entidade}/{acao}.blade.php`

Padrão da página:

1. `new #[Layout('layouts.app')] class extends Component`
2. `mount()` / ações chamam `$this->authorize(...)`
3. Validação com `$this->validate([...])` na própria página
4. Persistência no model; flash + `$this->redirect(..., navigate: true)`
5. Título via `<x-slot name="header">` (não usar `#[Title]`)
6. Form com componentes Breeze (`x-text-input`, `x-primary-button`, …)
7. Listagens: `WithPagination`, `#[Url]` para busca, `wire:confirm` na exclusão

Rotas em `routes/web.php`:

```php
Volt::route('clientes', 'pages.clientes.index')->name('clientes.index');
Volt::route('clientes/criar', 'pages.clientes.create')->name('clientes.create');
Volt::route('clientes/{cliente}/editar', 'pages.clientes.edit')->name('clientes.edit');
```

Menu: adicionar link em **desktop e mobile** em `resources/views/livewire/layout/navigation.blade.php`.

### Controllers HTTP — só quando Volt não serve

| Controller | Motivo |
|------------|--------|
| `ConviteController` | URL assinada (aceitar/recusar) |
| `ArquivoDownloadController` | Stream + `authorize` |
| Auth (`VerifyEmailController`, …) | Fluxo Breeze |

---

## Autorização

- Policy por convenção: `App\Models\Cliente` → `App\Policies\ClientePolicy` (sem registro manual).
- Trait `HandlesTeamAuthorization` em toda policy de recurso de time.
- Na página Volt: `$this->authorize('create', Cliente::class)` ou `$this->authorize('update', $cliente)`.

---

## Validação

- **Padrão atual do CRUD:** `$this->validate()` no Volt.
- Form Requests em `app/Http/Requests/` existem em parte sem uso nas telas Volt; use Form Request só para upload/binário HTTP clássico, se precisar.

---

## Persistência

- Migrations em `database/migrations/` (série `0001_01_01_*`).
- Entidade com tenant direto: `time_id` → `time`, `usuario_id` → `users`, `softDeletes()` quando fizer sentido.
- Factory em `database/factories/` com states úteis.
- Seeder só para lookup/fixo (`Tipos`, `Situacoes`, …) ou bootstrap (`AdminUserSeeder`).

No create Volt, preencher tenant explicitamente:

```php
Cliente::query()->create([
    ...$data,
    'usuario_id' => auth()->id(),
    'time_id' => current_time_id(),
]);
```

Filho sem `time_id` (ex.: Tarefa): gravar a FK do pai (`projeto_id`) e usar o trait `BelongsToTeamViaProjeto`.

---

## Testes

- Pest em `tests/Feature/`.
- Helpers em `tests/Pest.php`: `seedLookups()`, `criarCenarioDoisTimes()`.
- Isolamento tenant: `TeamScopeTest`, `TenantIsolationTest` (sessão com `current_time_id`).
- Páginas Volt: `Volt::test('pages.…')` + `actingAs` (ver `VersoesTest`, Auth).

Para entidade nova: estender o cenário de dois times e garantir que o time B não vê/altera recurso do time A.

```bash
docker compose exec app php artisan test
```

---

## Checklist: nova entidade CRUD

Ordem alinhada ao que o código já faz. Copie **Cliente** (tenant com `time_id`) ou **Tarefa** (tenant via projeto).

1. **Migration** — `time_id` / FK do pai, `usuario_id`, `softDeletes()` se aplicável  
2. **Model** — `HasFactory`, trait de tenant correto, `$fillable`, `casts()`, relações  
3. **Policy** — `HandlesTeamAuthorization`; espelhar `ClientePolicy` ou `TarefaPolicy`  
4. **Factory** — + states úteis  
5. **Seeder** — só se for lookup/fixo  
6. **Volt** — `pages/{entidade}/index|create|edit` (+ `show` se precisar)  
7. **Rotas** — `Volt::route` no grupo `auth` de `routes/web.php`  
8. **Menu** — desktop + responsive em `navigation.blade.php`  
9. **Testes** — isolamento cross-tenant; opcionalmente `Volt::test` do CRUD  

### Mapa mental

```
Migration
  → Model (+ BelongsToTeam ou Via*)
  → Policy (+ HandlesTeamAuthorization)
  → Factory
  → pages/{entidade}/*.blade.php
  → Volt::route
  → link no menu
  → testes com dois times
```

---

## O que não fazer

| Evitar | Por quê |
|--------|---------|
| Controller Admin para CRUD | UI é Volt |
| jQuery / assets legados por tela | Stack = Vite + Livewire/Alpine |
| Esquecer policy ou trait de tenant | Abre IDOR entre times |
| Exclusão via GET | Usar ação Livewire (`wire:click` / POST) |
| Classes Livewire PHP para CRUD novo | Preferir Volt single-file |
| `#[Title]` | Usar slot `header` |
| Form Request em todo create/edit Volt | Padrão é validate na página |
| Registrar policy no provider | Convenção Laravel basta |
| Basear feature na branch `versao_pre_refatoracao` | Legado Laravel 5.7 |

---

## Referências rápidas no código

| Assunto | Arquivo |
|---------|---------|
| Model tenant | `app/Models/Cliente.php` |
| Policy | `app/Policies/ClientePolicy.php` |
| Create Volt | `resources/views/livewire/pages/clientes/create.blade.php` |
| Filho via projeto | `app/Models/Tarefa.php`, `app/Policies/TarefaPolicy.php` |
| Time ativo | `app/Support/CurrentTeam.php` |
| Rotas | `routes/web.php` |
| Menu | `resources/views/livewire/layout/navigation.blade.php` |
| Cenário de teste | `tests/Pest.php` → `criarCenarioDoisTimes()` |
| Changelog | `config/versoes.php` + `/versoes` |
