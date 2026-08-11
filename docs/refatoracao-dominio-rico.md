# Guia de Refatoração: Domínio Rico

Como transformar entidades anêmicas (Model/Volt/Service com arrays) em **domínio rico**, no padrão consolidado por **Tarefa**.

Referência canônica já refatorada:

| Artefato | Path |
|----------|------|
| Domain | [`app/Domain/TarefaDomain.php`](../app/Domain/TarefaDomain.php) |
| Enums | [`app/Enums/TarefaStatus.php`](../app/Enums/TarefaStatus.php), [`app/Enums/TarefaSituacao.php`](../app/Enums/TarefaSituacao.php) |
| Exception de domínio | [`app/Exceptions/TarefaDomainException.php`](../app/Exceptions/TarefaDomainException.php) |
| Service | [`app/Services/TarefaService.php`](../app/Services/TarefaService.php) |
| Unit tests | [`tests/Unit/Domain/TarefaDomainTest.php`](../tests/Unit/Domain/TarefaDomainTest.php) |
| API patterns | [`docs/QUICK_START_GUIDE.md`](QUICK_START_GUIDE.md) |
| Web architecture | [`docs/arquitetura.md`](arquitetura.md) |

---

## Objetivo

1. **Uma única fonte de regras de negócio** — Domain, sem Eloquent / `auth()` / `DB` / HTTP.
2. **API e Web usam o mesmo Domain** — toda mutação passa por Service → Domain.
3. **Testes unitários baratos** — invariantes e comportamentos em PHP puro.

```
Antes (anêmico)                         Depois (rico)

Volt / Controller                       Volt / Controller
   │                                       │
   ├─ Model::create / update               ├─ authorize + validate (HTTP)
   └─ regras espalhadas                    ▼
                                        Service (orquestra)
                                           │
                                           ├─ Domain::criar / reconstituir
                                           ├─ $domain->comportamento()
                                           └─ Repository::save(toPersistenceArray)
```

---

## Regra de ouro

| Pode | Não pode |
|------|----------|
| Mutação via `*Service` → `*Domain` | `$model->update([...])` com regra de negócio na Volt/Controller |
| Leitura/listagem com Eloquent na Volt (display) | Domain importar Laravel (`Model`, `Facade`, `auth`) |
| Policy / FormRequest para auth e validação HTTP | Repository conter regras de transição / invariantes |
| DTO só na borda da API | Service decidir “pode arquivar?” |

**Toda mutação passa pelo Domain.** Listagem e show podem continuar com Eloquent para renderizar a UI.

---

## Critério: o Domain é rico?

Checklist rápido — se a resposta for “não” para qualquer item, ainda está anêmico:

- [ ] Construtor privado + factories (`criar()`, `reconstituir()`)
- [ ] Métodos de comportamento com nomes de negócio (`arquivar()`, `finalizar()`, …)
- [ ] Invariantes em `assertInvariantes()` (ou equivalente)
- [ ] Exception de domínio própria (`*DomainException` extends `\DomainException`)
- [ ] Enums de domínio para status / estados fechados (quando fizer sentido)
- [ ] `toPersistenceArray()` (ou equivalente) sem depender de Eloquent
- [ ] Zero imports de `Illuminate\*`, Models, Facades
- [ ] Testes unitários em `tests/Unit/Domain/` sem DB

---

## Passo a passo (por entidade)

Ordem fixa — não pule testes unitários do Domain antes de plugar no Service.

### 1. Inventário das regras (antes de codar)

Liste o que a entidade **já faz** no código (Volt, Service, Model):

| Fonte | O que procurar |
|-------|----------------|
| Volt `create` / `edit` / `index` | `->create()`, `->update([...])`, flags tipo `status => 1` |
| Service (se existir) | arrays passados ao repository, `if` de negócio |
| Migration / seeders | defaults, enums de banco, FKs |
| Discovery / ADR | regras documentadas |

Extraia comportamentos em linguagem de negócio, por exemplo:

- Cliente: ativar / inativar / renomear
- Projeto: encerrar / reabrir / mover de cliente
- Comentário: editar texto (só se não vazio)

Não invente máquina de estados nova sem necessidade — **comece com o que o produto já faz**.

### 2. Enums de domínio (quando houver valores fechados)

```
app/Enums/{Entidade}Status.php
app/Enums/{Entidade}Situacao.php   # se aplicável
```

- PHP backed enum (`: int` ou `: string`) alinhado ao que o banco já guarda
- Métodos de domínio no enum (`podeTransicionarPara()`), não no Service

### 3. Exception de domínio

```
app/Exceptions/{Entidade}DomainException.php
```

- Extende `\DomainException` (não `Exception` do Laravel)
- Factories estáticas: `tituloObrigatorio()`, `jaArquivada()`, …
- Mensagens legíveis para UI/API (400), sem HTML

Separar conceitualmente de `app/Exceptions/{Entidade}Exception.php` (infra/aplicação: notFound, createFailed) — ambas ficam em `app/Exceptions/`, mas a `*DomainException` extende `\DomainException` e a outra extende `Exception`.

### 4. Enriquecer o Domain

```
app/Domain/{Entidade}Domain.php
```

Estrutura mínima (espelho de Tarefa):

```php
final class EntidadeDomain
{
    private function __construct(/* estado */) {
        $this->assertInvariantes();
    }

    public static function criar(...): self { /* status inicial + invariantes */ }

    public static function reconstituir(...): self { /* a partir da persistência */ }

    // comportamentos
    public function renomear(string $nome): void { ... }
    public function arquivar(): void { ... }

    // queries
    public function isAtiva(): bool { ... }

    /** @return array<string, mixed> */
    public function toPersistenceArray(): array { ... }

    private function assertInvariantes(): void { ... }
}
```

**Lookups aninhados** (`{id, nome}` para resposta API): opcionais no `reconstituir()`, só para projeção. O núcleo mutável usa IDs/enums.

**Atualização “formulário completo”** (PUT / edit Volt): um método `aplicarAtualizacao(array $data)` que chama os comportamentos internos (não seta propriedades cruas sem regra).

### 5. Testes unitários (antes do Service)

```
tests/Unit/Domain/{Entidade}DomainTest.php
```

- Pest em `tests/Unit/` (sem `RefreshDatabase`)
- Cobrir: happy path de cada comportamento + cada exception de domínio
- Sem HTTP, sem factory Eloquent, sem Sanctum

Isso trava as regras **antes** de mexer em API/Web.

### 6. Repository

- Manter queries/persistência
- Aceitar `array` vindo de `$domain->toPersistenceArray()`
- Adicionar `exists(int $id): bool` se outro service precisar checar existência (evitar `Model::query()` no Service)

### 7. Service de aplicação

- `create`: `Domain::criar` → `repository->create(toPersistenceArray())` → DTO (API) ou void/id (web)
- `update`: `find` → `reconstituir` → `aplicarAtualizacao` / métodos → `repository->update`
- Métodos explícitos para ações de UI (`arquivar`, `recuperar`) quando existirem botões
- Receber `int $usuarioId` (e opcionalmente `?int $contaId` para log), não Model `Conta` se não for necessário
- `DB::transaction`, `Log`, `auth()` ficam **aqui**, não no Domain
- Catch: relançar `*DomainException`; mapear falhas de infra para `app/Exceptions/*Exception`

### 8. API (se a entidade tiver endpoints)

- Controller: `authorize` → Service → `sendResponse`
- Sem `Model::query()` no Controller (usar `findModel` / Service)
- FormRequest: validação HTTP apenas (`required`, `exists`, formato)
- Catch `*DomainException` → 400 com mensagem do domínio
- Suite `tests/Feature/Api/` deve continuar passando (guard da refatoração)

### 9. Web Livewire Volt

Mutações:

```php
public function save(EntidadeService $service): void
{
    $this->authorize(...);
    $data = $this->validate([...]);
    try {
        $service->create((int) auth()->id(), $data);
    } catch (EntidadeDomainException $e) {
        $this->addError('campo', $e->getMessage());
        return;
    }
    // flash + redirect
}
```

- Injeção do Service no método da action (Livewire 3)
- Sempre exibir `@error` nos campos (falha silenciosa parece “não salvou”)
- Defaults de selects alinhados ao Domain (`tipo_id = 1`, etc.)
- Leitura (`with()`, paginação) pode continuar Eloquent

### 10. Fechamento

- [ ] Unit Domain verde
- [ ] Feature API existente verde
- [ ] Feature Volt das mutações principais (create/edit/ação)
- [ ] Nenhuma mutação da entidade chama Eloquent com regra de negócio fora do Service/Domain
- [ ] Atualizar este guia / checklist de backlog abaixo

---

## O que fica fora do Domain

| Responsabilidade | Onde |
|------------------|------|
| Tenant / time / conta | Scope, Policy, middleware, FormRequest `exists` scoped |
| Autorização (quem pode) | Policy |
| Formato HTTP / JSON | FormRequest, DTO, `ApiController` |
| Soft delete técnico | Repository / Model |
| Paginação, busca, eager load | Repository / Volt `with()` |
| Logs, transação, jobs | Service |

---

## Anti-padrões (evitar)

1. **Domain anêmico disfarçado** — só getters + Service com `if` de negócio.
2. **Dois Domains** — um "para API" e regras duplicadas na Volt.
3. **`*DomainException` misturada com `*Exception` de infra** — mesmo namespace (`app/Exceptions/`), mas classes distintas: `*DomainException extends \DomainException` para regras, `*Exception extends Exception` para notFound/createFailed.
4. **Pular testes unitários** e validar só via HTTP — volta a ficar caro e embolado.
5. **`aplicarAtualizacao` setando propriedades sem passar pelos métodos** — burla invariantes.

---

## Escala de padronização (0–10)

Pontuação única por entidade, cruzando **este guia** (domínio rico + Web) e o [`QUICK_START_GUIDE.md`](QUICK_START_GUIDE.md) (camadas da API). Use para priorizar backlog e declarar “finalizado”.

| Faixa | Significado |
|-------|-------------|
| **0** | Só Model/migration/seed; mutação e regra espalhadas ou inexistentes |
| **1–2** | Volt/Model (ou CRUD parcial); sem Service/Domain compartilhado |
| **3–4** | Policy/Factory/alguma borda HTTP; ainda sem Domain+Service orquestrado |
| **5–6** | Camadas API presentes (Service/Repo/DTO) **ou** Domain parcial/anêmico; mutações ainda furam o padrão |
| **7–8** | Domain rico + Service nas mutações; falta fechar API completa, Web alinhada ou testes |
| **9** | Quase canônico: Domain rico, API e Web pelo Service, testes unitários Domain; gaps menores |
| **10** | **Finalizado** no padrão dos dois guias (referência: Tarefa) |

### Como somar (checklist — ~1 ponto cada, arredonde)

**Domínio rico (este guia)**

1. Construtor privado + `criar()` / `reconstituir()`
2. Comportamentos de negócio + `assertInvariantes()` (ou equivalente)
3. `*DomainException` + `toPersistenceArray()`; Domain sem Illuminate/Eloquent
4. Testes unitários em `tests/Unit/Domain/` (sem DB)
5. Mutações Web (Volt) só via Service → Domain (sem `$model->update` com regra)

**API / camadas ([`QUICK_START_GUIDE.md`](QUICK_START_GUIDE.md))**

6. Repository + Service com `Model → Domain → DTO`
7. Controller (`ApiController`) + FormRequest (`validated()` / attributes)
8. Policy + isolamento tenant/time onde couber
9. Feature tests API (e Feature Volt das mutações principais, se houver UI)
10. OpenAPI / factory / rotas no padrão do projeto (quando a entidade tiver API)

**Finalizado?** = nota **10** (todos os itens aplicáveis atendidos). Entidades só-web ou só-API: itens N/A não descontam — a nota espelha o escopo real do produto. Lookups de referência ficam fora da escala (**—**).

---

## Ordem sugerida no RapidTask

Priorize entidades com mutação de negócio clara e superfície web+API (ou web intensa).

| Ordem | Entidade | Nota | Finalizado? | Notas |
|-------|----------|------|-------------|-------|
| 1 | **Tarefa** | 10 | Sim | Referência canônica (status, situação, datas, arquivar/recuperar) |
| 2 | **TarefaComentario** | 10 | Sim | Texto obrigatório; `editarTexto`; web `show` + API via Service |
| 3 | **Token** | 6 | Não | API com Domain parcial (`inactive` / `withPlainTextToken`); falta DomainException/invariantes/unit |
| 4 | **Cliente** | 2 | Não | Volt + Model + Policy; introduzir Domain + Service; API se houver |
| 5 | **Projeto** | 2 | Não | Volt + Model + Policy; datas, vínculo com cliente; Service compartilhado |
| 6 | **Conta / Time / Convite** | 3 | Não | Fluxos SaaS; Domain fino, Application Service mais grosso |
| — | Lookups (Tipo, Situação, Prioridade) | — | N/A | Seed / dados de referência — em geral sem Domain rico |

---

## Template mínimo de arquivos novos

Para entidade `Cliente`:

```
app/Domain/ClienteDomain.php
app/Enums/ClienteStatus.php               # se houver
app/Exceptions/ClienteDomainException.php
app/Services/ClienteService.php
app/Repositories/ClienteEloquentRepository.php
tests/Unit/Domain/ClienteDomainTest.php
tests/Feature/...                         # Volt create/edit + API se existir
```

Web: páginas em `resources/views/livewire/pages/clientes/` passam a chamar `ClienteService` nas actions de mutação.

---

## Relação com os outros docs

| Doc | Papel |
|-----|--------|
| [`QUICK_START_GUIDE.md`](QUICK_START_GUIDE.md) | Como criar endpoint API (camadas, DTO, rotas) |
| [`arquitetura.md`](arquitetura.md) | Como criar CRUD web (Volt, Policy, tenant) |
| **Este guia** | Como **refatorar** entidade existente para Domain rico compartilhado por API e Web |

Ao criar entidade **nova**, já nasça rica (Passos 2–9) em vez de Model-first na Volt.

---

**Versão:** 1.1  
**Baseado em:** refatoração Tarefa (agosto 2026)  
**Projeto:** RapidTask
