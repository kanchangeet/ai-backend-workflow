---
name: test-generator
description: Generates PHPUnit unit and feature tests for any use case, domain entity, or API endpoint following the project's DDD testing conventions.
---

## Purpose
Generate production-ready PHPUnit tests that match the project's test structure:
- Unit tests in `tests/Unit/Domain/` — pure PHP, no Laravel bootstrap
- Feature tests in `tests/Feature/` — full HTTP stack with `RefreshDatabase`

## Input
Provide ONE of:
- A use case class path (e.g. `app/Application/UseCases/Master/CreateCategoryUseCase.php`)
- An API route (e.g. `POST /api/master/categories`)
- A domain entity (e.g. `app/Domain/Master/Entities/Category.php`)

## Output
- Unit test covering: happy path, edge cases, immutability (for entities), domain exceptions
- Feature test covering: auth guard, validation errors, success response shape, conflict cases
- For BFF endpoints (`/api/bff/*`): feature test asserting auth guard, exact aggregated response shape, and graceful handling of partial use case failures (nullable fields, `warnings` array)

## Rules
- Unit tests extend `PHPUnit\Framework\TestCase` — no Laravel helpers
- Feature tests use `actingAs($user)` + `RefreshDatabase`
- Test method names use `snake_case` prefixed with `test_`
- Assert response structure with `assertJsonPath()`, not `assertJson()`
- Mirror the file under `tests/` matching the `app/` path

## Example prompt
```
Generate tests for app/Application/UseCases/Master/CreateCategoryUseCase.php
and the corresponding POST /api/master/categories endpoint.
Include: duplicate name rejection, missing required fields, successful creation.
```
