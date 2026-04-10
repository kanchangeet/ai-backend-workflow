---
name: api-integration-guide
description: Governs how BFF responses are consumed in the frontend — DTO-to-state mapping, error handling for 401/422/502, where API calls live, and how to add a new API domain. Use when wiring up a new BFF endpoint or auditing existing API integration.
---

You are the API integration authority for this React frontend.

## Architecture Contract

```
BFF (Laravel)  ──►  src/api/<domain>.ts  ──►  React Query hook  ──►  Component
```

- The frontend **only** talks to the BFF via `src/api/`. Never call the Laravel backend directly.
- All Axios config lives in `src/api/client.ts`. Never create a second Axios instance.
- Never call `axios` or `fetch` directly inside components, hooks, or pages.

## BFF Endpoints (Current)

| Domain   | Base path       | File              |
|----------|-----------------|-------------------|
| Auth     | `/api/auth/*`   | `src/api/auth.ts` |
| Master   | `/api/master`   | `src/api/master.ts` |

When adding a new domain, create `src/api/<domain>.ts` following the patterns below.

## DTO → Frontend State Mapping

BFF responses follow this envelope:

```ts
// List response
{ data: T[], meta: { current_page, last_page, per_page, total } }

// Single resource
{ data: T }

// Error response
{ message: string, errors?: Record<string, string[]> }
```

**Map DTOs explicitly — do not spread raw API shapes into store or component state.**

```ts
// ✅ Correct — explicit mapping
const mapMasterDto = (dto: MasterDto): Master => ({
  id: dto.id,
  name: dto.name,
  isActive: dto.is_active,   // snake_case → camelCase
  createdAt: new Date(dto.created_at),
})

// ❌ Wrong — raw DTO leaks into UI layer
setState(response.data)
```

Define DTO types in `src/api/<domain>.ts` and domain types in `src/types/<domain>.ts`.

## Error Handling by Status Code

The Axios interceptor in `src/api/client.ts` handles cross-cutting errors:

| Status | Interceptor action | Component responsibility |
|--------|--------------------|--------------------------|
| 401    | Clears auth store, redirects to `/login` | None — handled globally |
| 403    | Throws `ApiError` with `code: 'FORBIDDEN'` | Show "Not authorised" message |
| 422    | Throws `ApiError` with `errors` map | Map field errors to form via `setError()` |
| 500/502 | Throws `ApiError` with `code: 'SERVER_ERROR'` | Show generic error alert |
| Network | Throws `ApiError` with `code: 'NETWORK_ERROR'` | Show "Check your connection" alert |

### Handling 422 validation errors in forms

```ts
const create = useCreateMaster()

const onSubmit = async (data: FormValues) => {
  try {
    await create.mutateAsync(data)
    navigate('/master')
  } catch (err) {
    if (isApiError(err) && err.status === 422) {
      Object.entries(err.errors ?? {}).forEach(([field, messages]) => {
        setError(field as keyof FormValues, { message: messages[0] })
      })
    }
  }
}
```

### Handling non-form errors

```tsx
const { data, isError, error } = useMasterList({ page })

if (isError) {
  return <Alert variant="error">{getErrorMessage(error)}</Alert>
}
```

Use the `getErrorMessage(err)` util (to be created at `src/utils/apiError.ts`) that returns a human-readable string from any `ApiError`.

## Adding a New API Domain

### Step 1 — DTO and domain types

```ts
// src/api/invoice.ts
export interface InvoiceDto {
  id: number
  invoice_number: string
  total_amount: number
  issued_at: string
}

// src/types/invoice.ts
export interface Invoice {
  id: number
  invoiceNumber: string
  totalAmount: number
  issuedAt: Date
}
```

### Step 2 — API module

```ts
// src/api/invoice.ts (continued)
import { apiClient } from './client'
import type { PaginatedResponse } from './types'

export const invoiceApi = {
  list: (params: { page: number }) =>
    apiClient.get<PaginatedResponse<InvoiceDto>>('/api/invoices', { params }).then(r => r.data),

  get: (id: number) =>
    apiClient.get<{ data: InvoiceDto }>(`/api/invoices/${id}`).then(r => r.data.data),

  create: (payload: CreateInvoicePayload) =>
    apiClient.post<{ data: InvoiceDto }>('/api/invoices', payload).then(r => r.data.data),

  update: (id: number, payload: UpdateInvoicePayload) =>
    apiClient.put<{ data: InvoiceDto }>(`/api/invoices/${id}`, payload).then(r => r.data.data),

  delete: (id: number) =>
    apiClient.delete(`/api/invoices/${id}`),
}
```

### Step 3 — React Query hooks

```ts
// src/hooks/useInvoice.ts
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { invoiceApi } from '@/api/invoice'

export const useInvoiceList = (params: { page: number }) =>
  useQuery({ queryKey: ['invoices', params], queryFn: () => invoiceApi.list(params) })

export const useCreateInvoice = () => {
  const qc = useQueryClient()
  return useMutation({
    mutationFn: invoiceApi.create,
    onSuccess: () => qc.invalidateQueries({ queryKey: ['invoices'] }),
  })
}
```

### Step 4 — Register query keys

Add the domain to `src/api/queryKeys.ts` (create if absent):

```ts
export const queryKeys = {
  master: (params?: object) => ['master', params],
  invoices: (params?: object) => ['invoices', params],
}
```

## Rules

1. **One API file per domain** — never put auth calls in `master.ts`
2. **Always map DTOs** — no raw BFF shapes in component/store state
3. **Never add a second Axios instance** — extend `client.ts` interceptors instead
4. **Error handling** — 401 in interceptor, 422 in form catch, others via `isError`
5. **Query key hygiene** — always include params in query key arrays for correct cache invalidation
6. **Optimistic updates** — only if the UX explicitly requires it; default to `invalidateQueries`
