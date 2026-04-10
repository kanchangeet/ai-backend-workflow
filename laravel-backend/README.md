# Laravel Backend

Production-ready Laravel 11 microservice built on **Clean Architecture + DDD**.  
Connects to **Supabase (PostgreSQL)** and uses **AWS SQS** for async events.

## Requirements

- Docker & Docker Compose
- A [Supabase](https://supabase.com) project (for the database)

## Quick Start

### 1. Configure environment

```bash
cp .env.example .env
```

Edit `.env` and replace all placeholder values:

```env
APP_ENV=local
APP_DEBUG=true

DB_CONNECTION=pgsql
DB_HOST=db.<your-project-ref>.supabase.co   # Supabase Dashboard → Settings → Database
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=<your-supabase-db-password>
DB_SSLMODE=require
```

For local dev without SQS, also set:
```env
QUEUE_CONNECTION=database
```

### 2. Start services

```bash
docker compose up -d --build
```

### 3. Bootstrap (first run only)

```bash
# Generate app key
docker exec -u root laravel_app php artisan key:generate

# If QUEUE_CONNECTION=database or CACHE_DRIVER=database, create the table first
docker exec laravel_app php artisan cache:table

# Run migrations
docker exec laravel_app php artisan migrate
```

### 4. Verify

```bash
curl http://localhost:8080/api/health
# → {"status":"ok"}
```

## Services

| Container | Role | Port |
|---|---|---|
| `laravel_app` | PHP-FPM | internal :9000 |
| `laravel_nginx` | Nginx reverse proxy | **:8080** |
| `laravel_worker` | Queue worker | — |

## Common Commands

```bash
# Rebuild after .env changes
docker compose up -d --build

# Run migrations
docker exec laravel_app php artisan migrate

# Run tests
docker exec laravel_app ./vendor/bin/phpunit

# Code style check
docker exec laravel_app ./vendor/bin/pint --test

# Generate Swagger docs
docker exec laravel_app php artisan l5-swagger:generate
# View: http://localhost:8080/api/documentation
```

## Troubleshooting

| Error | Fix |
|---|---|
| `Database connection [laravel_demo] not configured` | Set `DB_CONNECTION=pgsql` in `.env` |
| `APPLICATION IN PRODUCTION — Command cancelled` | Set `APP_ENV=local` in `.env` and rebuild |
| `file_put_contents .env: Permission denied` | Use `docker exec -u root laravel_app php artisan key:generate` |
| `Network unreachable` to Supabase | Wrong DB credentials or Supabase project is paused — check Dashboard |
| `.env changes not taking effect` | Always rebuild: `docker compose up -d --build` (restart alone is not enough) |

## Architecture

See [CLAUDE.md](CLAUDE.md/CLAUDE.md) for full architecture documentation, module creation guide, and AI agent usage.
