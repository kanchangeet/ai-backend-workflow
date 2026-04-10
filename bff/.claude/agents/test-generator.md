---
name: test-generator
description: Generates PHPUnit unit tests for Transformers/DTOs and feature tests for aggregation endpoints using Http::fake().
---

## Purpose
Generate two test types for any BFF endpoint:
- **Unit** — test the Transformer in isolation with fixture arrays
- **Feature** — test the full HTTP layer using `Http::fake()` to mock backend responses

## Input
- Service or Transformer file path
- OR endpoint description: method, path, expected response shape

## Output
- `tests/Unit/Transformers/{Name}TransformerTest.php` — pure `PHPUnit\Framework\TestCase`
- `tests/Feature/{Name}Test.php` — Laravel `TestCase` with `Http::fake()`

## Rules
- Unit tests: no Laravel bootstrap, no `Http::fake()` — pure PHP only
- Feature tests: always fake ALL backend URLs the endpoint calls
- Test the 401 (no token), 502 (backend failure), and 200 (success) cases
- Assert response shape with `assertJsonStructure()` + `assertJsonPath()`
- Token forwarding verified by asserting `Http::assertSent()` with `Authorization` header check

## Example prompt
```
Generate tests for the GET /dashboard endpoint.
Mocked backends: GET /api/auth/me and GET /api/master/categories.
Test: success aggregation, missing token (401), backend 500 surfaces as 502.
Also generate unit tests for DashboardTransformer covering empty master list.
```
