---
name: bff-layer-generator
description: Generates BFF (Backend for Frontend) endpoints, response transformers, and aggregation logic that compose multiple domain use cases into client-optimised payloads.
---

## Purpose
Scaffold the BFF layer that sits between the frontend client and the core DDD application layer.
The BFF aggregates, shapes, and adapts internal domain responses into payloads the frontend actually needs — without polluting domain use cases with presentation concerns.

## Responsibilities of the BFF layer
- **Route definition** — dedicated BFF routes under `Modules/Bff/Routes/api.php`
- **Controller** — thin orchestrator in `app/Http/Controllers/Bff/` that calls multiple use cases
- **Transformer / Resource** — `app/Http/Resources/Bff/` classes that reshape domain output
- **Aggregation** — combining results from ≥2 use cases into one response payload
- **Client-specific logic** — field renaming, date formatting, feature flags, pagination meta

## Input
Provide a BFF endpoint spec:
```
BFF Endpoint: Dashboard Summary
Route: GET /api/bff/dashboard
Auth: required
Aggregates:
  - ListRecentOrdersUseCase (last 5 orders for user)
  - GetUserProfileUseCase
  - GetNotificationCountUseCase
Response shape:
  {
    "user": { "name", "avatar_url" },
    "recent_orders": [ { "id", "status", "total", "created_at" } ],
    "unread_notifications": int
  }
```

## Output (files generated)
```
app/Http/Controllers/Bff/DashboardController.php
app/Http/Resources/Bff/DashboardResource.php
Modules/Bff/Routes/api.php  (or appended)
tests/Feature/Bff/DashboardBffTest.php
```

## Rules
- BFF controllers must NOT contain business logic — delegate entirely to use cases
- BFF controllers may call multiple use cases; keep orchestration flat (no nested BFF calls)
- Transformers live in `app/Http/Resources/Bff/` — never reuse domain-level API Resources
- BFF routes are prefixed `/api/bff/` and registered in a dedicated module route file
- All BFF routes must be auth-protected unless explicitly marked `public`
- If a use case can fail independently (e.g. notification service down), handle partial failures gracefully with a nullable field and a `warnings` array in the response
- Never expose internal IDs or domain model field names directly — map to client-friendly keys
- Each BFF endpoint gets its own feature test asserting: auth guard, exact response shape, and each aggregated data source

## Example prompt
```
Generate a BFF endpoint GET /api/bff/dashboard that aggregates:
1. ListRecentOrdersUseCase — last 5 orders for the authenticated user
2. GetUserProfileUseCase — name and avatar_url
3. GetNotificationCountUseCase — unread count

Response must include user, recent_orders, and unread_notifications.
Handle GetNotificationCountUseCase failure gracefully (return null count, add warning).
Generate controller, transformer, route entry, and feature test.
```
