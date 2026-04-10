---
name: module-generator
description: Scaffolds a complete DDD module from a spec — Entity, RepositoryInterface, UseCase, DTO, EloquentModel, Repository, Controller, Requests, Routes, Migration, and OpenAPI annotations.
---

## Purpose
Generate all boilerplate for a new module following the exact file structure in this project.
Zero manual scaffolding required.

## Input
Provide a module spec:
```
Module: Order
Fields:
  - user_id: int (FK users.id)
  - product_id: int
  - quantity: int
  - notes: string (nullable)
  - status: enum(pending, confirmed, cancelled) default=pending
Operations: create, list, show, cancel (soft status change)
Auth: all routes protected
Events: OrderCreatedEvent on create
```

## Output (files generated)
```
app/Domain/Order/Entities/Order.php
app/Domain/Order/Enums/OrderStatus.php
app/Domain/Order/Events/OrderCreated.php
app/Domain/Order/Repositories/OrderRepositoryInterface.php
app/Domain/Order/Services/OrderDomainService.php
app/Application/DTOs/Order/CreateOrderDTO.php
app/Application/UseCases/Order/CreateOrderUseCase.php
app/Application/UseCases/Order/ListOrdersUseCase.php
app/Application/UseCases/Order/ShowOrderUseCase.php
app/Application/UseCases/Order/CancelOrderUseCase.php
app/Infrastructure/Persistence/Eloquent/OrderModel.php
app/Infrastructure/Repositories/EloquentOrderRepository.php
app/Events/Order/OrderCreatedEvent.php
app/Listeners/Order/HandleOrderCreated.php
app/Http/Controllers/Order/OrderController.php
app/Http/Requests/Order/CreateOrderRequest.php
app/OpenApi/Paths/OrderPaths.php
Modules/Order/Routes/api.php
database/migrations/YYYY_MM_DD_create_orders_table.php
tests/Unit/Domain/Order/OrderTest.php
tests/Feature/Order/OrderApiTest.php
```
Also updates: `RepositoryServiceProvider`, `EventServiceProvider`, `routes/api.php`

## BFF coverage
By default, `module-generator` scaffolds the core DDD module only (domain → application → infrastructure → HTTP controller).
It does **not** generate a BFF endpoint — use `bff-layer-generator` separately once you know the frontend's exact payload requirements.
Include in your spec if you want a BFF stub generated alongside the module:
```
Bff: yes  # generates a skeleton BFF controller + transformer + route entry for this module
```

## Example prompt
```
Generate a complete Order module with fields: user_id, product_id int,
quantity int, status enum(pending|confirmed|cancelled).
Operations: create, list, show, cancel.
Fire OrderCreatedEvent on creation. All routes auth-protected.
```
