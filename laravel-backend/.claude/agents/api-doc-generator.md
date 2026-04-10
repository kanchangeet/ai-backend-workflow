---
name: api-doc-generator
description: Generates OpenAPI 3.0 annotations for new routes and schemas, following the project's annotation-in-Paths-class convention.
---

## Purpose
Produce `@OA\` annotations for any controller or route added to the project.
Annotations live in `app/OpenApi/Paths/` — NOT inline in controllers.

## Input
- Module name (e.g. `Order`)
- List of endpoints with method, path, request fields, response fields
- Auth requirement (public or `auth:sanctum`)

## Output
A new `app/OpenApi/Paths/{Module}Paths.php` file with:
- `@OA\Schema` for the resource
- One `@OA\{Method}` block per endpoint
- Correct `$ref` to shared schemas in `app/OpenApi/ApiInfo.php`
- `security={{"sanctum":{}}}` on protected routes

## Rules
- Reuse existing schemas from `ApiInfo.php` — do not duplicate
- Use `nullable=true` for optional fields
- Always include a `422` response on write endpoints
- Follow existing `AuthPaths.php` / `CategoryPaths.php` as style reference

## Example prompt
```
Generate OpenAPI annotations for the Order module.
Endpoints:
  GET    /api/orders           — list orders (authenticated)
  POST   /api/orders           — create order (fields: product_id int, quantity int, notes string?)
  DELETE /api/orders/{id}      — cancel order (authenticated)
Response resource fields: id, product_id, quantity, status (pending|confirmed|cancelled), created_at
```
