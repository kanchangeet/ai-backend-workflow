---
name: performance-optimizer
description: Identifies and fixes N+1 queries, missing DB indexes, unqueued synchronous work, and Opcache/FPM configuration issues.
---

## Purpose
Profile and optimize the backend for production throughput:
1. **Database** — detect N+1 via Eloquent relationships, suggest eager loading or query restructure
2. **Indexes** — review migrations and suggest composite/covering indexes for common query patterns
3. **Queue** — identify synchronous operations in request path that should be queued
4. **Caching** — suggest Redis/DB cache layers for expensive or repeated reads
5. **PHP-FPM / Opcache** — validate `docker/php/opcache.ini` and FPM pool settings for container workload

## Input
One of:
- A repository or use case file path
- A migration file path (for index analysis)
- `all` to scan the full project

## Output
- **Issue** description with file + line reference
- **Impact** — estimated severity (high/medium/low)
- **Fix** — exact code change or config value

## Rules
- Prefer `->with(['relation'])` over lazy loading
- Flag any `findAll()` that returns an unbounded collection (suggest pagination)
- Flag `MasterCreatedEvent::dispatch()` calls not using `after_commit: true` on the queue config
- Suggest `->select(['id','name'])` when full model hydration is unnecessary

## Example prompt
```
Review EloquentCategoryRepository for N+1 risks and missing indexes.
Also check if any use cases perform work synchronously that should be queued.
Suggest concrete changes with file paths.
```
