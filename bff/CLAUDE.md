# BFF (Backend for Frontend) — CLAUDE.md

## Overview

Standalone Laravel BFF layer that acts as:
- **API Gateway** — single entry point for frontend clients
- **Aggregation layer** — fans out to backend services, returns unified responses
- **Token forwarder** — passes Bearer tokens downstream; holds no auth state

The BFF has **no database**, **no queue**, **no Eloquent**.

---

## Architecture

```
Frontend
   ↓  Bearer token
BFF (this project)
   ├── ForwardAuthToken middleware  — validates token presence, attaches to request
   ├── Controller (invokable)       — receives request, returns JsonResponse
   ├── Service                      — orchestrates backend calls concurrently
   ├── BackendClient                — single HTTP client for all backend calls
   ├── Transformer                  — maps raw backend arrays → typed DTOs
   └── DTO                          — typed output, serialized via toArray()
         ↓ Bearer token forwarded
   Backend API (laravel-backend)
```

**Rules:**
- Controllers are thin — zero business logic
- Transformers are the only classes that know backend response key names
- DTOs are the only types that flow out of Services into Controllers
- `BackendClient` is the only class that calls `Http::` — never call it elsewhere
- Token is never logged, stored, or returned to the frontend

---

## Folder Structure

```
app/
├── Clients/
│   └── BackendClient.php          # Single HTTP client, token forwarding
├── Services/
│   ├── DashboardService.php       # Concurrent aggregation
│   └── AuthService.php            # Proxy auth calls to backend
├── Transformers/
│   └── DashboardTransformer.php   # Raw arrays → DTOs
├── DTOs/
│   ├── DashboardDTO.php
│   ├── UserDTO.php
│   └── MasterItemDTO.php
├── Http/
│   ├── Controllers/
│   │   ├── DashboardController.php
│   │   └── AuthController.php
│   └── Requests/
│       ├── LoginRequest.php
│       └── RegisterRequest.php
├── Middleware/
│   └── ForwardAuthToken.php       # Extracts + attaches Bearer token
└── Exceptions/
    ├── BackendException.php        # Wraps upstream HTTP errors
    └── Handler.php                 # Maps exceptions → JSON responses

routes/api.php
config/services.php                 # BACKEND_URL, BACKEND_TIMEOUT
docker/
├── nginx/nginx.conf
└── php/{opcache,php}.ini
```

---

## API Flow

### Authenticated request (e.g. GET /dashboard)

```
1. Frontend sends:  GET /api/dashboard
                    Authorization: Bearer <token>

2. ForwardAuthToken middleware:
   - Extracts token from Authorization header
   - Attaches to $request->attributes->get('bearer_token')
   - Returns 401 if missing

3. DashboardController::__invoke():
   - Reads token from request attributes
   - Calls DashboardService::aggregate($token)

4. DashboardService::aggregate():
   - Concurrently::run([
       GET /api/auth/me,
       GET /api/master/categories,
     ])
   - Passes raw responses to DashboardTransformer

5. DashboardTransformer::transform():
   - Builds UserDTO + Collection<MasterItemDTO>
   - Returns DashboardDTO

6. Controller returns:
   { "user": {...}, "master": [...] }
```

### Error propagation

`BackendException` is thrown by `BackendClient`, propagates unhandled through Service and Controller, and is serialized by `Handler`. Never catch it in Services or Controllers.

| Backend status | BFF response |
|---|---|
| 401 | 401 (token rejected by backend) |
| 403 | 403 |
| 404 | 404 |
| 422 | 422 with `message` |
| 5xx | 502 Bad Gateway |
| Timeout / connection refused | 502 Bad Gateway |

One failure in a `Concurrently::run()` fan-out aborts the entire request — there are no partial responses.

> See the `error-handling-guide` agent for the full failure matrix and required test cases.

---

## Environment Setup

```bash
cp .env.example .env
```

| Variable | Description |
|---|---|
| `BACKEND_URL` | Backend base URL e.g. `http://laravel_nginx` (Docker) or `https://api.domain.com` |
| `BACKEND_TIMEOUT` | HTTP timeout in seconds (default: 10) |
| `APP_KEY` | Generate with `php artisan key:generate` |

---

## Running with Docker

```bash
docker compose up -d --build

# Bootstrap
docker compose exec app php artisan key:generate

# Health check
curl http://localhost:9090/api/health
```

| Container | Role | Port |
|---|---|---|
| `bff_app` | PHP-FPM | internal :9000 |
| `bff_nginx` | Nginx | **:9090** |

To connect BFF → Backend on the same Docker host, set:
```
BACKEND_URL=http://laravel_nginx
```
And add the backend's `bff` network to both compose files.

---

## How to Add a New Aggregation Endpoint

1. **DTO(s)** — `app/DTOs/{Resource}DTO.php` with `fromArray()` + `toArray()`
2. **Transformer** — `app/Transformers/{Resource}Transformer.php` — maps backend arrays → DTOs
3. **Service** — `app/Services/{Resource}Service.php` — calls `BackendClient` via `Concurrently::run()`; never catch `BackendException` here
4. **Controller** — `app/Http/Controllers/{Resource}Controller.php` — invokable, reads token, returns JSON
5. **Route** — add to `routes/api.php` under `ForwardAuthToken` middleware
6. **Tests** — unit test for Transformer, feature test with `Http::fake()` covering 200, 401, 404, 502, and connection failure

> Tip: use the `api-aggregation-generator` agent to scaffold all steps from a spec.
> Tip: use the `error-handling-guide` agent to audit error propagation or generate missing failure-path tests.

---

## Testing

```bash
./vendor/bin/phpunit                            # all tests
./vendor/bin/phpunit --testsuite Unit           # DTOs + Transformers only
./vendor/bin/phpunit --testsuite Feature        # HTTP layer with Http::fake()
```

**Conventions:**
- Unit tests: `extends PHPUnit\Framework\TestCase` — no Laravel bootstrap, no Http::fake()
- Feature tests: always call `Http::fake([...])` before the request — mock ALL backend URLs hit by the endpoint
- Always test: 200 success, 401 no token, 502 backend failure
- Assert shape with `assertJsonPath()`, count with `assertJsonCount()`

---

## Code Quality

```bash
./vendor/bin/pint --test   # check
./vendor/bin/pint          # fix
```

---

## CI/CD

Pipeline: [`.github/workflows/ci.yml`](.github/workflows/ci.yml)

| Job | Trigger | Steps |
|---|---|---|
| `lint` | Every push / PR | Pint --test |
| `test` | After lint | PHPUnit (no DB needed) |
| `docker` | Push to `main` | Build + push to GHCR |

No database service needed in CI — BFF has no local DB. All backend calls are faked with `Http::fake()`.

---

## AI Agents

Agents in [`.claude/agents/`](.claude/agents/):

| Agent | When to use |
|---|---|
| `api-aggregation-generator` | Scaffold a new aggregation endpoint from a spec |
| `test-generator` | Generate unit + feature tests for any endpoint |
| `code-reviewer` | Pre-merge review for token safety and architecture compliance |
| `performance-optimizer` | Find sequential backend calls, missing cache, payload bloat |
| `error-handling-guide` | Audit or generate correct error handling — concurrent failure modes, timeout vs 4xx vs 5xx, BackendException propagation |

---

## Development Workflow

### During Development
Just build what is asked. No automatic agent runs.

### Before Committing
Trigger phrase: `"ready to commit"` or `"commit this"`

1. Run `code-reviewer` on changed files
2. Stop if any Critical issues found — report and wait for instructions

### Before Pull Request
Trigger phrase: `"ready for PR"` or `"prepare PR"`

1. Run `code-reviewer`
2. Run `test-generator` on new endpoints or transformers
3. Summarize all findings in one report

### Before Deployment
Trigger phrase: `"ready to deploy"` or `"prepare release"`

1. Run `performance-optimizer` on changed services
2. Run `error-handling-guide` on any new aggregation endpoints
3. Confirm all backend failure cases (401, 404, 502, timeout) are tested

### Agent Usage Rules
- Never run full pipeline on small fixes (typos, config changes, minor refactors)
- One agent at a time — wait for output before running next
- Use `error-handling-guide` whenever a new `Concurrently::run()` fan-out is added
- Use `api-aggregation-generator` only for new endpoints, not modifications