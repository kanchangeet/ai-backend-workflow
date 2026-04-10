---
name: refactoring-guide
description: Guides safe refactoring of misplaced logic within the project's DDD layers — from controllers into use cases, from use cases into domain services, or across module boundaries — with step-by-step instructions and risk assessment.
---

## Purpose
When logic has crept into the wrong DDD layer, provide a safe, incremental refactor path:
1. **Identify** — pinpoint exactly what is misplaced and why it violates the layer contract
2. **Target** — determine the correct destination layer and class
3. **Plan** — ordered steps to move the logic without breaking behaviour
4. **Verify** — confirm existing tests still pass; identify tests to add or update

## DDD Layer Contracts (what belongs where)
| Layer | Allowed | Forbidden |
|---|---|---|
| `Domain/` | Entities, Value Objects, Domain Services, Repository Interfaces, Domain Events | Eloquent, HTTP, Laravel facades |
| `Application/` | Use Cases, DTOs, Application Services | Eloquent models directly, HTTP request/response objects |
| `Infrastructure/` | Eloquent models, Repository implementations, external service adapters | Domain logic, use case orchestration |
| `Http/Controllers/` | Input parsing, dispatching to use case, returning response | Business logic, DB queries, event firing |
| `Bff/Controllers/` | Aggregating use cases, shaping response | Business logic, direct DB access |

## Input
Describe the misplaced code:
```
File: app/Http/Controllers/Order/OrderController.php
Problem: The store() method calculates the order total, applies discount rules, and fires OrderCreatedEvent directly. 
This business logic should live in a use case and domain service.
```

## Output
- **Diagnosis** — which rule is violated and why it matters
- **Target location** — exact file path and class name for the destination
- **Step-by-step plan** — ordered, atomic steps (each step leaves code in a working state)
- **Code sketches** — method signatures and key implementation hints for each new/modified file
- **Test impact** — which existing tests break, what new tests are needed
- **Rollback point** — where you can stop and still have working code if the refactor is interrupted

## Rules
- Never suggest moving code in one large step — always break into ≥2 atomic commits
- Always move tests alongside the logic (unit test follows Domain/Application; feature test stays in Feature/)
- Prefer extract-then-delegate: add the new class, wire it up, then remove the old code — not the reverse
- Flag if the refactor crosses a module boundary (requires updating `RepositoryServiceProvider` / `EventServiceProvider`)
- If the misplaced logic fires events, verify the listener registration is not affected by the move
- If the code is in a controller, check that the corresponding `FormRequest` stays in `Http/Requests/` — do not move it
- After each step, specify which tests to run to confirm nothing is broken

## Example prompt
```
The OrderController::store() method calculates the order total and applies
discount rules inline before saving. Guide me through safely moving this logic
into CreateOrderUseCase and an OrderPricingDomainService.
Show me atomic steps, the new class skeletons, and which tests I need to update.
```
