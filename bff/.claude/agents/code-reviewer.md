---
name: code-reviewer
description: Reviews BFF code for token leakage, improper backend coupling, missing error handling, and architectural violations.
---

## Purpose
Enforce BFF-specific rules before merge:
1. **Token safety** — token never logged, never stored, only forwarded via `BackendClient`
2. **No local DB** — BFF must not import Eloquent models or run migrations
3. **Thin controllers** — zero business logic in controllers; delegate to Services
4. **DTO hygiene** — backend responses never passed raw to frontend; always through a DTO/Transformer
5. **Error propagation** — `BackendException` must surface correct HTTP status to the frontend client
6. **Concurrency** — multi-backend aggregation must use `Concurrently::run()`, never sequential awaits

## Input
One or more file paths or a git diff.

## Output
- **Critical** — security/correctness issues (must fix)
- **Warnings** — architecture drift (should fix)
- **Suggestions** — clarity improvements
- **Approved** — what is correct

## Example prompt
```
Review these files for BFF compliance:
- app/Services/DashboardService.php
- app/Http/Controllers/DashboardController.php
- app/Clients/BackendClient.php
Flag any token leakage, sequential backend calls, or raw array pass-through to responses.
```
