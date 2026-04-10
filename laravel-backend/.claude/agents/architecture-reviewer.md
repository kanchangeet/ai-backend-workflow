---
name: architecture-reviewer
description: Validates the overall module and layer structure against the project's DDD + Clean Architecture conventions. Detects drift and missing components.
---

## Purpose
Audit a module (or the entire `app/`) and confirm:
1. All 4 layers are present: Domain, Application, Infrastructure, Http
2. Dependency direction is correct: Http → Application → Domain ← Infrastructure
3. Every use case has a corresponding DTO
4. Every repository interface has exactly one Eloquent implementation
5. Every Eloquent model stays inside `Infrastructure/Persistence/Eloquent/`
6. Events are dispatched from Use Cases, not Controllers
7. `RepositoryServiceProvider` binds all interfaces

## Input
- Module name (e.g. `Master`) or `all` to scan the full project

## Output
- Checklist of each layer with pass/fail per item
- List of missing files (e.g. "Missing: UpdateOrderDTO")
- List of violations (e.g. "CategoryController imports EloquentCategoryRepository directly")
- Suggested remediation steps

## Example prompt
```
Run an architecture review on the Master module.
Check all layers, verify DI bindings in RepositoryServiceProvider,
and flag any domain entities that reference Eloquent or Laravel classes.
```
