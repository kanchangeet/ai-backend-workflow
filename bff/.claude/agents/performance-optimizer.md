---
name: performance-optimizer
description: Identifies sequential backend calls, missing concurrency, response caching opportunities, and unnecessary data fetched from backend.
---

## Purpose
Optimize BFF throughput and latency:
1. **Concurrency** — detect sequential `$client->get()` calls that can be parallelized with `Concurrently::run()`
2. **Response caching** — identify stable data (master/config) that should be cached with `Cache::remember()`
3. **Payload trimming** — flag when BFF forwards the full backend payload but only uses 2-3 fields (add `->select()` or filter in Transformer)
4. **Timeout tuning** — verify `BACKEND_TIMEOUT` is set appropriately per endpoint latency profile
5. **HTTP keep-alive** — confirm `Http::pool()` or persistent connections used for high-frequency endpoints

## Input
- A Service file path
- OR `all` to scan all `app/Services/`

## Output
- **Issue** with file + line reference
- **Impact** — latency saved (ms estimate) or throughput gain
- **Fix** — exact code change

## Example prompt
```
Analyze DashboardService for sequential backend calls and cache opportunities.
The master/categories endpoint rarely changes — suggest appropriate cache TTL.
Also check if the full user object is needed or just id + name.
```
