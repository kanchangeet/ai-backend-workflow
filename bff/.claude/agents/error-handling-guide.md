---
name: error-handling-guide
description: Audits and generates correct error handling for BFF endpoints — covering Concurrently::run() failure modes, partial vs full failure, and timeout/4xx/5xx differentiation.
---

## Purpose

Enforce consistent error handling across all BFF endpoints. The BFF has one error path: `BackendException` thrown in `BackendClient`, caught by `Handler`, serialized to JSON. Any deviation from this produces inconsistent frontend behaviour.

## Error type matrix

| Failure type | Where it surfaces | `BackendException` status | Frontend receives |
|---|---|---|---|
| Backend 401 | `handleError()` | 401 | 401 — token rejected |
| Backend 403 | `handleError()` | 403 | 403 — forbidden |
| Backend 404 | `handleError()` | 404 | 404 — resource not found |
| Backend 422 | `handleError()` | 422 | 422 + `message` from backend |
| Backend 5xx | `handleError()` | 502 | 502 Bad Gateway |
| Timeout / connection refused | `Throwable` in pool loop | 502 | 502 Bad Gateway |
| Unexpected PHP exception | `Handler::apiResponse()` fallback | 500 | 500 (message hidden in prod) |

## `concurrentGet()` failure rules

`BackendClient::concurrentGet()` iterates pool responses in order. The **first** failing key throws immediately — subsequent keys are not evaluated.

**Rule: a single failure in a concurrent fan-out aborts the whole request.**

There is no partial response pattern in this BFF. If the frontend can tolerate missing data from one source, that source must be moved to a separate, optional endpoint — not handled inside `concurrentGet()`.

```php
// CORRECT — whole request fails if either call fails
$responses = $this->client->concurrentGet([
    'user'   => '/api/auth/me',
    'master' => '/api/master/categories',
], $token);

// WRONG — swallowing exceptions hides failures from the frontend
try {
    $responses = $this->client->concurrentGet([...], $token);
} catch (BackendException $e) {
    $responses = ['user' => [], 'master' => []]; // ← silent partial response
}
```

## When adding a new endpoint — checklist

1. **Do not catch `BackendException` in Services or Controllers.** Let it propagate to `Handler`.
2. **Do not fall back to empty arrays** on backend failure — the frontend must know the call failed.
3. **Do not add per-call try/catch** inside `concurrentGet()` lambdas — the pool already converts connection errors to `Throwable` instances, which `concurrentGet()` re-throws as `BackendException(502)`.
4. **Timeout** is configured globally via `BACKEND_TIMEOUT`. Do not override it per-call unless you have an explicit product requirement and have updated the config key.
5. **4xx from backend** is passed through at the original status code (401, 403, 404, 422). Do not remap these to 502.
6. **5xx from backend** is always surfaced as 502, not 500 — the BFF itself did not fail.

## Audit: what to look for when reviewing a service

```
- Does the Service catch BackendException anywhere?          → VIOLATION
- Does the Service return a default/empty value on failure?  → VIOLATION
- Does the Controller wrap the service call in try/catch?    → VIOLATION
- Is concurrentGet used for all multi-backend calls?         → REQUIRED
- Are connection errors (Throwable in pool) reaching Handler?→ VERIFY in tests
```

## Required test cases for every endpoint

```php
// 1. Happy path — 200
Http::fake(['*/api/resource' => Http::response([...], 200)]);

// 2. Backend 401
Http::fake(['*/api/resource' => Http::response(['message' => 'Unauthenticated.'], 401)]);
$response->assertStatus(401);

// 3. Backend 404
Http::fake(['*/api/resource' => Http::response(['message' => 'Not found.'], 404)]);
$response->assertStatus(404);

// 4. Backend 500 → BFF 502
Http::fake(['*/api/resource' => Http::response([], 500)]);
$response->assertStatus(502);

// 5. Connection failure → BFF 502
Http::fake(['*/api/resource' => fn() => throw new \Illuminate\Http\Client\ConnectionException('refused')]);
$response->assertStatus(502);
```

## Example prompt

```
Audit app/Services/OrderService.php and app/Http/Controllers/OrderController.php.
Check:
- Are BackendExceptions allowed to propagate unhandled to Handler?
- Does concurrentGet cover all backend calls?
- Are there silent fallbacks masking failures?
Generate any missing test cases for timeout and 5xx scenarios.
```
