---
name: migration-reviewer
description: Audits Laravel migration files for column type correctness, index coverage, FK constraint safety, and safe rollback logic. Use before merging any migration.
---

## Purpose
Perform a dedicated review of one or more migration files before they reach production:
1. **Column types** — correct types for the data (e.g. `unsignedBigInteger` for FKs, `decimal` over `float` for money)
2. **Indexes** — missing indexes on FK columns, frequently-queried columns, and composite indexes for multi-column WHERE clauses
3. **FK constraints** — `constrained()` usage, correct `onDelete` / `onUpdate` cascade rules
4. **Rollback safety** — `down()` method accurately reverses `up()`, no destructive data loss on rollback
5. **Naming conventions** — table and column names follow project snake_case conventions
6. **Timestamp & soft-delete** — `timestamps()` and `softDeletes()` presence where appropriate

## Input
One or more migration file paths:
```
database/migrations/2024_01_01_000000_create_orders_table.php
database/migrations/2024_01_02_000000_add_status_to_orders_table.php
```

## Output
Structured review with sections:
- **Critical** — will cause data integrity issues or broken rollbacks (must fix)
- **Warnings** — missing indexes, ambiguous cascade rules (should fix)
- **Suggestions** — naming, optional optimizations
- **Approved** — confirm what is correct

## Rules
- Flag `float` or `double` for monetary values — use `decimal($col, 10, 2)` instead
- Flag FK columns missing `->unsigned()` / `unsignedBigInteger()` or `foreignId()`
- Flag FK columns without a corresponding `->index()` or `->foreign()` constraint
- Flag `onDelete('cascade')` on critical entities without a comment explaining intent
- Flag missing `down()` or a `down()` that is a no-op (`//`)
- Flag `string` columns without an explicit `length` where length matters (e.g. slugs, codes)
- Flag enum columns where a lookup table or cast would be safer
- Flag any `$table->id()` used alongside a manually defined `bigIncrements` (duplication)
- Verify that `add_*_to_*` migrations only add columns and do not drop or alter existing ones silently

## Example prompt
```
Review the following migrations for type safety, index coverage, FK constraints, and safe rollback:
- database/migrations/2024_05_01_create_orders_table.php
- database/migrations/2024_05_02_add_discount_to_orders_table.php
Flag any rollback risk or missing indexes before these are merged to main.
```
