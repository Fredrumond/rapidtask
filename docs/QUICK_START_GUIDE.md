# Quick Start Guide - API Development

Quick and objective guide for API development following the project architecture.

## 📋 Step by Step to Create an Entity

### 1. Migration

```bash
php artisan make:migration create_entity_names_table
```

**Reference:** `database/migrations/2024_01_01_000001_create_products_table.php`

**Key points:**
- Use `$table->id()` as primary key (auto-increment integer)
- Add indexes on frequently queried fields
- Use `json` for JSON fields when needed
- Define `enum` for status fields

---

### 2. Model

**Reference:** `app/Models/Product.php`
**Responsibility:** Database configuration only.

---

### 3. Enum

**Reference:** `app/Enums/Product/ProductStatus.php`

---

### 4. Repository

**Reference:** `app/Repositories/ProductEloquentRepository.php`

---

### 5. Domain

**Reference:** `app/Domain/ProductDomain.php`

**Structure:**
- Private properties
- Constructor with all fields
- Getters and Setters
- Business rules methods: `activate()`, `deactivate()`, `isActive()`
- Validation in setters

**Key point:** All business logic goes here, not in Service or Controller.

---

### 6. DTO

**Reference:** `app/DTO/Product/ProductResponseDTO.php`

---

### 7. Exception

**Reference:** `app/Exceptions/ProductException.php`

---

### 8. Request Validation

**Reference:** `app/Http/Requests/StoreProductRequest.php` and `UpdateProductRequest.php`

**StoreRequest:** Use `required` for mandatory fields  
**UpdateRequest:** Use `sometimes` for optional fields, handle `unique` validation with current ID

---

### 9. Service

**Reference:** `app/Services/ProductService.php`

**Methods:**
- `listPaginated()` - List with pagination
- `find()` - Find by ID
- `create()` - Create new entity
- `update()` - Update existing entity
- `delete()` - Soft delete (deactivate)
- `convertRecordToDomain()` - Model → Domain
- `convertToDTO()` - Domain → DTO

**Key point:** Always convert Model → Domain → DTO

---

### 10. Controller

**Reference:** `app/Http/Controllers/ProductController.php`

**Methods:**
- `index()` - GET /entity-names
- `show()` - GET /entity-names/{id}
- `store()` - POST /entity-names
- `update()` - PUT /entity-names/{id}
- `destroy()` - DELETE /entity-names/{id}

**Pattern:**
```php
try {
    $result = $this->service->method($params);
    return $this->sendResponse($result, 'Success message', HttpCode::OK);
} catch (\Exception $e) {
    return $this->sendResponse([], $e->getMessage(), HttpCode::BAD_REQUEST);
}
```

---

### 11. Routes

**Reference:** `routes/api.php` (lines 28-34)

```php
Route::prefix('entity-names')->group(function () {
    Route::get('/', [EntityNameController::class, 'index']);
    Route::post('/', [EntityNameController::class, 'store']);
    Route::get('/{entity_name_id}', [EntityNameController::class, 'show']);
    Route::put('/{entity_name_id}', [EntityNameController::class, 'update']);
    Route::delete('/{entity_name_id}', [EntityNameController::class, 'destroy']);
});
```

---

## ✅ Implementation Checklist

- [ ] Migration created and executed
- [ ] Model with `$fillable`, `casts()`, relations
- [ ] Enum with constants
- [ ] Repository extending `EloquentRepository`
- [ ] Domain with business rules
- [ ] DTO implementing `JsonSerializable`
- [ ] Exception class
- [ ] StoreRequest and UpdateRequest
- [ ] Service with CRUD methods
- [ ] Controller with REST endpoints
- [ ] Routes registered

---

## 🎯 Golden Rules

### WHAT TO DO ✅

- Controller → Service → Repository → Model
- Always initialize empty arrays
- Convert Model → Domain → DTO in Service
- Use Form Requests for validation
- Throw specific exceptions
- Use integer IDs (`$table->id()`) as primary keys

### WHAT NOT TO DO ❌

- Business logic in Controller
- Access Model directly in Controller
- Business logic in Repository
- Skip validation
- Leave arrays uninitialized

---

## 📦 Standard Response

```json
{
    "message": "Descriptive message",
    "data": []
}
```

**With pagination:**

```json
{
    "data": {
        "data": [],
        "pagination": {
            "current_page": 1,
            "per_page": 15,
            "total": 100,
            "last_page": 7
        }
    }
}
```

---

## 🎨 Naming Conventions

| Type | Pattern | Example |
|------|---------|---------|
| Controller | `{Entity}Controller` | `ProductController` |
| Service | `{Entity}Service` | `ProductService` |
| Repository | `{Entity}EloquentRepository` | `ProductEloquentRepository` |
| Domain | `{Entity}Domain` | `ProductDomain` |
| DTO | `{Entity}ResponseDTO` | `ProductResponseDTO` |
| Model | `{Entity}` | `Product` |
| Exception | `{Entity}Exception` | `ProductException` |
| Request | `Store/Update{Entity}Request` | `StoreProductRequest` |

---

## 📚 Reference Implementation

**Complete example:** Check `Product` entity implementation

- Migration: `database/migrations/2024_01_01_000001_create_products_table.php`
- Model: `app/Models/Product.php`
- Domain: `app/Domain/ProductDomain.php`
- Service: `app/Services/ProductService.php`
- Controller: `app/Http/Controllers/ProductController.php`
- Repository: `app/Repositories/ProductEloquentRepository.php`
- DTO: `app/DTO/Product/ProductResponseDTO.php`
- Requests: `app/Http/Requests/StoreProductRequest.php`
- Routes: `routes/api.php`

---

**Version:** 1.1  
**Last Update:** August 2026  
**Project:** RapidTask
