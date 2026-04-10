# Laravel Backend — CLAUDE.md

## Overview

Production-ready Laravel microservice built on **Clean Architecture + DDD**.
Independently deployable. Connects to **Supabase (PostgreSQL)** and uses **AWS SQS** for async events.

---

## Architecture

```
Request → Http (Controller + FormRequest)
             ↓
       Application (UseCase + DTO)
             ↓
         Domain (Entity + RepositoryInterface + DomainService + Event)
             ↑
    Infrastructure (EloquentModel + EloquentRepository + Listener)
```

**Rule:** Dependencies point inward. `Domain` has zero framework imports.

---

## Folder Structure

```
app/
├── Domain/
│   └── {Module}/
│       ├── Entities/          # Pure PHP value objects (immutable)
│       ├── Enums/             # Backed enums (e.g. MasterStatus)
│       ├── Events/            # Domain events (no Laravel imports)
│       ├── Repositories/      # Interfaces only
│       └── Services/          # Domain business rules
├── Application/
│   ├── DTOs/                  # Typed input objects
│   └── UseCases/              # One class per operation
├── Infrastructure/
│   ├── Persistence/Eloquent/  # Eloquent models (infra only)
│   └── Repositories/          # Implements Domain interfaces
├── Http/
│   ├── Controllers/           # Thin — delegates to UseCases
│   └── Requests/              # FormRequest validation
├── Events/                    # Laravel events (ShouldQueue)
├── Listeners/                 # Queue listeners
├── OpenApi/                   # Swagger annotation classes
│   └── Paths/                 # One file per module
├── Providers/
│   ├── RepositoryServiceProvider.php
│   └── EventServiceProvider.php
└── Exceptions/
    └── Handler.php            # Unified API error format

Modules/
├── Auth/
│   ├── Controllers/
│   └── Routes/api.php
└── {Module}/
    └── Routes/api.php

routes/api.php                 # Health check + require module routes
database/migrations/
tests/
├── Unit/Domain/               # Pure PHPUnit — no Laravel
└── Feature/                   # Full HTTP stack with RefreshDatabase
docker/
├── nginx/nginx.conf
└── php/{opcache,php}.ini
```

---

## Environment Setup

```bash
cp .env.example .env
```

Key variables to fill in `.env` — **replace all placeholder values before running**:

| Variable | Description |
|---|---|
| `APP_ENV` | Set to `local` for local development |
| `APP_DEBUG` | Set to `true` for local development |
| `DB_CONNECTION` | Must be `pgsql` (not `laravel_demo`) |
| `DB_HOST` | Supabase host: `db.<ref>.supabase.co` (get from Supabase Dashboard → Settings → Database) |
| `DB_PORT` | `5432` for direct connection |
| `DB_PASSWORD` | Your real Supabase DB password |
| `DB_SSLMODE` | Always `require` for Supabase |
| `AWS_ACCESS_KEY_ID` | SQS IAM credentials |
| `AWS_SECRET_ACCESS_KEY` | SQS IAM credentials |
| `SQS_PREFIX` | `https://sqs.<region>.amazonaws.com/<account-id>` |
| `SQS_QUEUE` | Queue name (e.g. `master-events`) |

For local dev without SQS, set `QUEUE_CONNECTION=database`.

---

## Running with Docker

### First-time setup

```bash
# 1. Copy and fill in your real credentials (see Environment Setup above)
cp .env.example .env

# 2. Build and start all services
docker compose up -d --build

# 3. Generate app key (must run as root — .env is owned by root inside container)
docker exec -u root laravel_app php artisan key:generate

# 4. If CACHE_DRIVER=database, create the cache table migration first
docker exec laravel_app php artisan cache:table

# 5. Run migrations
docker exec laravel_app php artisan migrate

# 6. Check health
curl http://localhost:8080/api/health
```

### Subsequent runs

```bash
docker compose up -d --build
```

> **Note:** After editing `.env` locally, always rebuild (`--build`) so the updated file is baked into the image. A plain `docker compose restart` does NOT pick up `.env` changes.

**Services:**

| Container | Role | Port |
|---|---|---|
| `laravel_app` | PHP-FPM | internal :9000 |
| `laravel_nginx` | Nginx reverse proxy | **:8080** |
| `laravel_worker` | Queue worker (SQS) | — |

### Common issues

| Error | Fix |
|---|---|
| `composer.json does not contain valid JSON` | Run `composer install` from inside `laravel-backend/` directory |
| `Database connection [laravel_demo] not configured` | Set `DB_CONNECTION=pgsql` in `.env` |
| `APPLICATION IN PRODUCTION — Command cancelled` | Set `APP_ENV=local` in `.env` and rebuild |
| `file_put_contents .env: Permission denied` | Run `docker exec -u root laravel_app php artisan key:generate` |
| `Network unreachable` connecting to Supabase | Supabase hostname is wrong or project is paused — verify credentials in Supabase Dashboard |
| `storage: No such file or directory` on build | Run `mkdir -p storage/logs storage/framework/{cache/data,sessions,views} bootstrap/cache` |

---

## How to Create a New Module

Follow these steps in order to maintain architectural consistency.

### 1. Domain layer

```
app/Domain/{Module}/Entities/{Model}.php
app/Domain/{Module}/Enums/{Model}Status.php
app/Domain/{Module}/Events/{Model}Created.php
app/Domain/{Module}/Repositories/{Model}RepositoryInterface.php
app/Domain/{Module}/Services/{Model}DomainService.php
```

### 2. Application layer

```
app/Application/DTOs/{Module}/Create{Model}DTO.php
app/Application/DTOs/{Module}/Update{Model}DTO.php
app/Application/UseCases/{Module}/Create{Model}UseCase.php
app/Application/UseCases/{Module}/Update{Model}UseCase.php
app/Application/UseCases/{Module}/Show{Model}UseCase.php
app/Application/UseCases/{Module}/List{Model}sUseCase.php
app/Application/UseCases/{Module}/Delete{Model}UseCase.php
```

### 3. Infrastructure layer

```
app/Infrastructure/Persistence/Eloquent/{Model}Model.php
app/Infrastructure/Repositories/Eloquent{Model}Repository.php
```

### 4. Events + Listeners

```
app/Events/{Module}/{Model}CreatedEvent.php       # implements ShouldQueue
app/Listeners/{Module}/Handle{Model}Created.php   # implements ShouldQueue
```

### 5. HTTP layer

```
app/Http/Controllers/{Module}/{Model}Controller.php
app/Http/Requests/{Module}/Create{Model}Request.php
app/Http/Requests/{Module}/Update{Model}Request.php
Modules/{Module}/Routes/api.php
```

### 6. API Documentation

```
app/OpenApi/Paths/{Module}Paths.php
```

### 7. Wire everything up

**`RepositoryServiceProvider.php`**
```php
$this->app->bind({Model}RepositoryInterface::class, Eloquent{Model}Repository::class);
```

**`EventServiceProvider.php`**
```php
{Model}CreatedEvent::class => [Handle{Model}Created::class],
```

**`routes/api.php`**
```php
require base_path('Modules/{Module}/Routes/api.php');
```

**`database/migrations/`** — add schema migration.

### 8. Tests

```
tests/Unit/Domain/{Module}/{Model}Test.php
tests/Feature/{Module}/{Model}ApiTest.php
```

> Tip: Use the `module-generator` agent to scaffold all of the above automatically.

---

## Testing

```bash
./vendor/bin/phpunit                                          # all tests
./vendor/bin/phpunit --testsuite Unit                         # domain only
./vendor/bin/phpunit --testsuite Feature                      # API tests
./vendor/bin/phpunit --coverage-html build/coverage           # with coverage
./vendor/bin/phpunit tests/Feature/Master/CategoryApiTest.php # single file
```

**Conventions:**
- Unit tests extend `PHPUnit\Framework\TestCase` — no Laravel bootstrap
- Feature tests use `RefreshDatabase` + `actingAs($user)` for auth
- Method names: `test_snake_case_description`
- Assert with `assertJsonPath()`, not `assertJson()`

---

## Code Quality

```bash
./vendor/bin/pint --test   # check
./vendor/bin/pint          # auto-fix
```

Config: [`pint.json`](../pint.json) — Laravel preset, strict imports, trailing commas enforced.

---

## API Documentation

```bash
php artisan l5-swagger:generate
# View: http://localhost:8080/api/documentation
```

Annotations live in `app/OpenApi/Paths/` — never inline in controllers.
Shared schemas are in `app/OpenApi/ApiInfo.php`.

---

## CI/CD

Pipeline: [`.github/workflows/ci.yml`](../.github/workflows/ci.yml)

| Job | Trigger | What it does |
|---|---|---|
| `lint` | Every push / PR | Pint --test |
| `test` | After lint | PHPUnit with Postgres 16 service + Codecov upload |
| `docker` | Push to `main` only | Build + push to GHCR as `sha-<commit>` and `latest` |

No extra secrets needed — `GITHUB_TOKEN` is automatic for GHCR.

---

## AI Agents

Agents live in [`.claude/agents/`](../.claude/agents/). Invoke in Claude Code.

| Agent | When to use |
|---|---|
| `module-generator` | Scaffold a complete new module from a spec |
| `test-generator` | Generate tests for an existing use case or endpoint |
| `api-doc-generator` | Add OpenAPI annotations for new routes |
| `code-reviewer` | Pre-merge DDD compliance + security review |
| `architecture-reviewer` | Audit a module for layer violations |
| `performance-optimizer` | Find N+1s, missing indexes, sync bottlenecks |
| `migration-reviewer` | Audit migrations for indexes, FK constraints, rollback safety |
| `refactoring-guide` | Safely move or restructure code across DDD layers |
| `bff-layer-generator` | Expose a new backend endpoint for BFF consumption |

---

## Key Dependencies

| Package | Purpose |
|---|---|
| `laravel/sanctum` | API token authentication |
| `darkaonline/l5-swagger` | OpenAPI / Swagger UI |
| `laravel/pint` | Code style (PHP-CS-Fixer preset) |
| `phpunit/phpunit` | Testing |
| `aws/aws-sdk-php` | SQS queue driver |


---

## Development Workflow

### During Development
Just build what is asked. No automatic agent runs.

### Before Committing
Trigger phrase: `"ready to commit"` or `"commit this"`

1. Run `code-reviewer` on changed files
2. Run `architecture-reviewer` on affected module
3. Stop if any Critical issues found — report and wait for instructions

### Before Pull Request
Trigger phrase: `"ready for PR"` or `"prepare PR"`

1. Run `code-reviewer`
2. Run `architecture-reviewer`
3. Run `migration-reviewer` if any migrations were added or changed
4. Run `test-generator` on new Use Cases and endpoints
5. Run `api-doc-generator` if new routes were added
6. Summarize all findings in one report

### Before Deployment
Trigger phrase: `"ready to deploy"` or `"prepare release"`

1. Run `performance-optimizer` on changed modules
2. Run `architecture-reviewer` on full project (`all`)
3. Confirm OpenAPI docs are up to date

### Agent Usage Rules
- Never run full pipeline on small fixes (typos, config changes, minor refactors)
- One agent at a time — wait for output before running next
- Use `refactoring-guide` only when explicitly asked to restructure code
- Use `bff-layer-generator` only when a new backend endpoint needs BFF exposure