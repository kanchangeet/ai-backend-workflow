---
name: api-aggregation-generator
description: Scaffolds a new BFF aggregation endpoint — Service, DTO, Transformer, Controller, and route — from a spec of backend calls.
---

## Purpose
Generate a complete aggregation endpoint that fans out to multiple backend services and returns a unified response.

## Input
```
Endpoint: GET /profile
Auth: required
Backend calls:
  - GET /api/auth/me          → user data
  - GET /api/orders           → recent orders (last 5)
  - GET /api/notifications    → unread count
Response shape:
  { "user": {}, "orders": [], "unread_notifications": 0 }
```

## Output
```
app/DTOs/ProfileDTO.php
app/DTOs/OrderDTO.php
app/Transformers/ProfileTransformer.php
app/Services/ProfileService.php              ← uses Concurrently::run()
app/Http/Controllers/ProfileController.php
```
Route added to `routes/api.php` under `ForwardAuthToken` middleware.

## Rules
- All backend calls fire concurrently via `Concurrently::run()`
- DTOs use `fromArray()` factory + `toArray()` output — no direct array access in controllers
- Transformer is the only class that knows response key names from the backend
- Controller is a single-action invokable (`__invoke`)

## Example prompt
```
Generate a /profile aggregation endpoint.
Backend calls: GET /api/auth/me, GET /api/orders?limit=5, GET /api/notifications/unread-count.
Response: { user: {}, recent_orders: [], unread_count: int }
All routes auth-protected.
```
