# Guia de Padrões de Desenvolvimento — API

Guia único dos padrões arquiteturais usados na API do RapidTask. Consolida o passo a passo de criação de endpoints, as responsabilidades por camada e as convenções do projeto.

Referência canônica de CRUD na API: **Tarefa** (`app/Models/Tarefa.php` + camadas em `app/Services`, `app/Domain`, `app/DTO`, `app/Repositories`).

Para a arquitetura web (Livewire/Volt, multi-tenant, policies), ver [`docs/arquitetura.md`](arquitetura.md).

---

## Estrutura arquitetural (Clean Architecture)

### Fluxo de dados

```
Request → Controller → Service → Repository → Model → Database
Response ← Controller ← Service ← Repository ← Model ← Database
```

Conversão obrigatória no Service:

```
Model → Domain → DTO
```

### Princípios

- **Separation of Concerns**: cada camada tem uma função específica
- **Dependency Inversion**: Services dependem de contratos/repositórios, não de detalhes de persistência no Controller
- **Domain-Driven Design**: objetos de domínio encapsulam regras de negócio
- **Testability**: a estrutura facilita testes de unidade e de integração

---

## Organização de pastas

```
app/
├── Http/Controllers/          # Controllers da API (estendem ApiController)
├── Http/Requests/             # FormRequest (Store/Update)
├── Http/Middleware/           # Ex.: EnsureApiTeam, SetCurrentTeam
├── Services/                  # Orquestração e regras de aplicação
├── Repositories/              # Acesso a dados
├── Domain/                    # Objetos de domínio
├── Models/                    # Eloquent Models
├── DTO/                       # Data Transfer Objects (resposta)
├── Enums/                     # Enums (status, HttpCode, …)
├── Exceptions/                # Exceptions específicas por contexto
└── OpenApi/                   # Schemas / anotações OpenAPI

tests/
├── Feature/                   # Testes de API / Feature
└── Unit/                      # Testes unitários

database/
├── migrations/
└── factories/

routes/
└── api.php
```

---

## Passo a passo para criar uma entidade na API

### 1. Migration

```bash
php artisan make:migration create_entity_names_table
```

Pontos-chave:

- Usar `$table->id()` como chave primária (integer auto-increment)
- Indexar campos consultados com frequência
- Usar `json` quando necessário
- Definir `enum` (banco ou PHP Enum) para campos de status

---

### 2. Model

**Referência:** `app/Models/Tarefa.php`

**Responsabilidade:** apenas configuração de banco (fillable, casts, relations, traits de tenant). Sem regra de negócio.

---

### 3. Enum (quando aplicável)

**Referência:** `app/Enums/HttpCode.php`

Usar Enums PHP tipados para status, códigos HTTP e valores fechados.

---

### 4. Repository

**Referência:** `app/Repositories/TarefaEloquentRepository.php`

```php
namespace App\Repositories;

class EntityNameEloquentRepository extends EloquentRepository
{
    public function __construct()
    {
        $this->model = new EntityName();
    }

    // Métodos de query específicos
}
```

**Responsabilidade:** acesso a dados (queries). Sem regra de negócio.

---

### 5. Domain

**Referência:** `app/Domain/TarefaDomain.php`

**Estrutura típica:**

- Propriedades privadas
- Construtor com os campos relevantes
- Getters (e setters quando fizer sentido)
- Métodos de regra de domínio (`activate()`, `deactivate()`, `isActive()`, etc.)
- Validação de invariantes nos setters / construtor

**Ponto-chave:** a lógica de negócio fica no Domain (e/ou no Service de aplicação), nunca no Controller ou no Repository. Referência: `TarefaDomain`.

---

### 6. DTO

**Referência:** `app/DTO/Tarefa/TarefaResponseDTO.php`

Objeto de saída da API. O Service converte Domain → DTO antes de devolver ao Controller.

---

### 7. Exception

**Referência:** `app/Exceptions/TarefaException.php`

```php
namespace App\Exceptions;

class EntityNameException extends Exception
{
    public function __construct($message = 'Operation failed', $code = 0)
    {
        parent::__construct($message, $code);
    }
}
```

Preferir factories estáticas (`notFound()`, `createFailed()`) quando o projeto já seguir esse padrão.

---

### 8. FormRequest

**Referência:** `app/Http/Requests/StoreTarefaRequest.php` e `UpdateTarefaRequest.php`

```bash
php artisan make:request StoreEntityNameRequest
php artisan make:request UpdateEntityNameRequest
```

- **StoreRequest:** `required` nos campos obrigatórios
- **UpdateRequest:** `sometimes` (ou `sometimes|required`) para updates parciais; `unique` deve ignorar o ID atual
- Sempre implementar `failedValidation()` com JSON 422 padronizado
- No Controller: sempre `$request->validated()` — nunca `$request->all()`

Exemplo de resposta de validação:

```json
{
    "message": "Erro de validação",
    "errors": {}
}
```

---

### 9. Service

**Referência:** `app/Services/TarefaService.php`

Métodos típicos de CRUD:

- `list()` / `listPaginated()` — listagem
- `find()` — buscar por ID
- `create()` — criar
- `update()` — atualizar
- `delete()` — remover / soft delete
- `convertRecordToDomain()` — Model → Domain
- `convertToDTO()` — Domain → DTO

**Ponto-chave:** sempre converter Model → Domain → DTO. Inicializar arrays vazios antes de popular.

O Service recebe dados já validados pelo FormRequest.

---

### 10. Controller

**Referência:** `app/Http/Controllers/TarefaController.php`

Estende `ApiController` e usa injeção de dependência do Service.

Endpoints REST típicos:

| Método | Rota | Ação |
|--------|------|------|
| GET | `/entity-names` | `index()` |
| GET | `/entity-names/{id}` | `show()` |
| POST | `/entity-names` | `store()` |
| PUT | `/entity-names/{id}` | `update()` |
| DELETE | `/entity-names/{id}` | `destroy()` |

Padrão de resposta:

```php
try {
    $result = $this->service->method($params);
    return $this->sendResponse($result, 'Mensagem de sucesso', HttpCode::OK->value);
} catch (\Exception $e) {
    return $this->sendResponse([], $e->getMessage(), HttpCode::BAD_REQUEST->value);
}
```

**Responsabilidades:**

- Receber a request HTTP
- Autorizar quando aplicável (`$this->authorize(...)`)
- Chamar o Service com `$request->validated()`
- Devolver resposta via `sendResponse()`
- **Não** conter regra de negócio
- **Não** acessar Model diretamente (exceto para Policy / type-hint de autorização)
- **Não** validar input manualmente no Controller

---

### 11. Routes

**Referência:** `routes/api.php`

```php
Route::prefix('entity-names')->group(function () {
    Route::get('/', [EntityNameController::class, 'index']);
    Route::post('/', [EntityNameController::class, 'store']);
    Route::get('/{entity_name_id}', [EntityNameController::class, 'show']);
    Route::put('/{entity_name_id}', [EntityNameController::class, 'update']);
    Route::delete('/{entity_name_id}', [EntityNameController::class, 'destroy']);
});
```

Recursos isolados por time devem usar o middleware `api.team` (ver ADR `0001-contexto-de-time-na-api-via-header`).

---

### 12. Factory e testes

**Factory** (`database/factories/`):

```php
class EntityNameFactory extends Factory
{
    protected $model = EntityName::class;

    public function definition(): array
    {
        return [
            'field' => fake()->word(),
        ];
    }
}
```

**Teste de API** (`tests/Feature/`):

```php
public function test_can_list_items(): void
{
    // Arrange
    EntityName::factory()->create(['field' => 'value']);

    // Act
    $response = $this->getJson('/api/entity-names');

    // Assert
    $response->assertStatus(200)
        ->assertJsonStructure(['message', 'data']);
}
```

Cobrir happy path e casos de borda (vazio, 404, validação 422, autorização 403).

---

## Padrões por camada (resumo)

| Camada | Faz | Não faz |
|--------|-----|---------|
| **Controller** | HTTP, authorize, `validated()`, `sendResponse()` | Regra de negócio, query no Model |
| **FormRequest** | Validação de input HTTP | Persistência |
| **Service** | Orquestra fluxo, Model→Domain→DTO, exceptions | Validar HTTP (`$request`) |
| **Domain** | Regras e invariantes do domínio | Depender do framework / Eloquent |
| **Repository** | Queries e persistência | Regra de negócio |
| **Model** | Config Eloquent, relations, casts | Regra de negócio |
| **DTO** | Contrato de saída da API | Persistência |
| **Exception** | Erros de contexto específico | Fluxo feliz |

---

## Resposta padrão

Base em `ApiController::sendResponse()`:

```json
{
    "message": "Mensagem descritiva",
    "data": []
}
```

### Com paginação

```json
{
    "message": "List Items.",
    "data": {
        "data": [],
        "pagination": {
            "current_page": 1,
            "per_page": 15,
            "total": 100,
            "last_page": 7,
            "from": 1,
            "to": 15
        }
    }
}
```

> Se o endpoint retornar `data` + `pagination` no mesmo nível, manter consistência em todos os list endpoints do mesmo recurso. O importante é o contrato ser estável.

### Tratamento de erros

```php
try {
    $result = $this->service->performOperation($data);
    return $this->sendResponse($result, 'Operação concluída.');
} catch (SpecificException $e) {
    return $this->sendResponse([], $e->getMessage(), HttpCode::BAD_REQUEST->value);
} catch (\Exception $e) {
    \Log::error('Unexpected error: '.$e->getMessage());
    return $this->sendResponse([], 'Internal server error', HttpCode::INTERNAL_SERVER_ERROR->value);
}
```

---

## Paginação e filtros

Listagens grandes devem paginar. Filtros são opcionais.

### Repository

```php
public function findAllWithFilters(array $filters = [], int $perPage = 15)
{
    $query = $this->model::query();

    foreach ($filters as $field => $value) {
        if ($value !== null && $value !== '') {
            $query->where($field, $value);
        }
    }

    return $query->paginate($perPage);
}
```

### Service

```php
public function listPaginated(array $filters = [], int $perPage = 15)
{
    $paginated = $this->repository->findAllWithFilters($filters, $perPage);

    $items = [];
    foreach ($paginated->items() as $item) {
        $items[] = $this->convertToDTO($this->convertRecordToDomain($item));
    }

    return [
        'data' => $items,
        'pagination' => [
            'current_page' => $paginated->currentPage(),
            'per_page' => $paginated->perPage(),
            'total' => $paginated->total(),
            'last_page' => $paginated->lastPage(),
            'from' => $paginated->firstItem(),
            'to' => $paginated->lastItem(),
        ],
    ];
}
```

### Controller

```php
public function index(Request $request): JsonResponse
{
    try {
        $filters = $request->only(['field1', 'field2']);
        $perPage = (int) $request->input('per_page', 15);

        $result = $this->service->listPaginated($filters, $perPage);

        return $this->sendResponse($result, 'Itens listados.');
    } catch (\Exception $e) {
        return $this->sendResponse([], $e->getMessage(), HttpCode::BAD_REQUEST->value);
    }
}
```

### Uso na API

```bash
GET /api/entity-names
GET /api/entity-names?page=2
GET /api/entity-names?per_page=50
GET /api/entity-names?status=pending&page=2&per_page=20
```

Boas práticas:

1. Default `per_page`: 15
2. Limitar `per_page` máximo (ex.: 100)
3. Filtros sempre opcionais
4. Documentar parâmetros no OpenAPI

---

## Documentação OpenAPI (Swagger)

Anotar Controllers / schemas (ver `app/OpenApi/` e atributos no `TarefaController`).

```bash
# Gerar documentação
php artisan l5-swagger:generate

# Interface
http://localhost:8080/api/documentation
```

---

## Convenções de nomenclatura

| Tipo | Padrão | Exemplo |
|------|--------|---------|
| Controller | `{Entity}Controller` | `TarefaController` |
| Service | `{Entity}Service` | `TarefaService` |
| Repository | `{Entity}EloquentRepository` | `TarefaEloquentRepository` |
| Domain | `{Entity}Domain` | `TarefaDomain` |
| DTO | `{Entity}ResponseDTO` | `TarefaResponseDTO` |
| Model | `{Entity}` (singular) | `Tarefa` |
| Exception | `{Entity}Exception` | `TarefaException` |
| FormRequest | `Store/Update{Entity}Request` | `StoreTarefaRequest` |
| Test | `{Entity}ApiTest` | `TarefaApiTest` |

---

## Regras de ouro

### Fazer

- Controller → Service → Repository → Model
- Validar input com FormRequest (antes do Controller)
- Usar sempre `$request->validated()`
- Inicializar arrays vazios
- Converter Model → Domain → DTO no Service
- Usar `sendResponse()` padronizado
- Lançar exceptions específicas
- Usar IDs integer (`$table->id()`) como PK

### Não fazer

- Regra de negócio no Controller
- Validar input HTTP dentro do Service
- Usar `$request->all()`
- Acessar Model diretamente no Controller para CRUD
- Regra de negócio no Repository
- Deixar arrays sem inicialização
- Pular validação

---

## Checklist de implementação

Para cada novo endpoint / entidade na API:

- [ ] Migration criada e executada
- [ ] Model com `$fillable`, `casts()`, relations / traits de tenant
- [ ] Enum (se aplicável)
- [ ] Repository estendendo `EloquentRepository`
- [ ] Domain com regras / estrutura de domínio
- [ ] DTO de resposta
- [ ] Exception específica
- [ ] `Store{Entity}Request` e `Update{Entity}Request`
- [ ] Service com CRUD e conversões Model → Domain → DTO
- [ ] Controller com endpoints REST + `sendResponse()`
- [ ] Rotas em `routes/api.php` (middleware `auth:sanctum` / `api.team` quando necessário)
- [ ] Factory para testes
- [ ] Testes de API (happy path + edge cases)
- [ ] Documentação OpenAPI

---

## Comandos úteis

```bash
# Testes
php artisan test
php artisan test --filter=Tarefa

# OpenAPI
php artisan l5-swagger:generate
```

---

## Implementação de referência

Entidade **Tarefa**:

| Artefato | Path |
|----------|------|
| Migration | `database/migrations/` (tabelas de tarefas) |
| Model | `app/Models/Tarefa.php` |
| Domain | `app/Domain/TarefaDomain.php` |
| Service | `app/Services/TarefaService.php` |
| Controller | `app/Http/Controllers/TarefaController.php` |
| Repository | `app/Repositories/TarefaEloquentRepository.php` |
| DTO | `app/DTO/Tarefa/TarefaResponseDTO.php` |
| Requests | `app/Http/Requests/StoreTarefaRequest.php`, `UpdateTarefaRequest.php` |
| Exception | `app/Exceptions/TarefaException.php` |
| Routes | `routes/api.php` |
| OpenAPI | `app/OpenApi/TarefaSchemas.php` |

---

## Referências

- [Clean Architecture](https://blog.cleancoder.com/uncle-bob/2012/08/13/the-clean-architecture.html)
- [Laravel Best Practices](https://github.com/alexeymezenin/laravel-best-practices)
- [PSR-12 Coding Standard](https://www.php-fig.org/psr/psr-12/)
- [Domain-Driven Design](https://martinfowler.com/bliki/DomainDrivenDesign.html)
- Arquitetura web do projeto: [`docs/arquitetura.md`](arquitetura.md)

---

**Versão:** 2.0  
**Última atualização:** Agosto 2026  
**Projeto:** RapidTask  

Este documento consolida o antigo `QUICK_START_GUIDE_2.md` e o `API_ARCHITECTURE_GUIDELINE.md`.
