# React Frontend — CLAUDE.md

## Purpose

Standalone React SPA using Vite. Connects **only** to the BFF (`/api/*`). Never calls the Laravel backend directly.

---

## Architecture

```
react-frontend/
├── src/
│   ├── api/              # All HTTP calls — one file per domain
│   │   ├── client.ts     # Axios instance with auth interceptor
│   │   ├── auth.ts       # Login, register, logout, me
│   │   └── master.ts     # CRUD for master records
│   ├── components/
│   │   ├── ui/           # Generic design system components
│   │   │   ├── Button, Input, Select, Textarea
│   │   │   ├── Modal, ConfirmModal, Badge, Alert
│   │   │   ├── Table, Pagination, LoadingSpinner
│   │   │   └── index.ts  # barrel export
│   │   ├── layout/       # Header, Sidebar
│   │   └── master/       # Feature-specific: MasterForm
│   ├── hooks/
│   │   ├── useAuth.ts    # Login, register, logout mutations
│   │   └── useMaster.ts  # CRUD hooks via React Query
│   ├── layouts/
│   │   ├── AppLayout.tsx # Protected layout with sidebar + header
│   │   └── AuthLayout.tsx # Centered card for auth pages
│   ├── pages/
│   │   ├── auth/         # LoginPage, RegisterPage
│   │   ├── master/       # MasterListPage, MasterCreatePage, MasterEditPage
│   │   ├── preview/      # PreviewPage — design system showcase
│   │   └── DashboardPage.tsx
│   ├── router/index.tsx  # createBrowserRouter config
│   ├── store/authStore.ts # Zustand auth state (persisted)
│   ├── styles/globals.css # Tailwind + CSS utilities
│   └── main.tsx          # Entry: QueryClient + RouterProvider
├── e2e/                  # Playwright tests
│   ├── auth.spec.ts
│   ├── master.spec.ts
│   ├── navigation.spec.ts
│   └── helpers/auth.ts
├── .claude/agents/       # Claude Code agents
├── .github/workflows/    # CI/CD pipeline
├── Dockerfile
├── docker-compose.yml
└── nginx.conf
```

---

## Setup

```bash
# Install dependencies
npm install

# Copy env
cp .env.example .env
# Set VITE_BFF_URL to your BFF address

# Start dev server
npm run dev                 # http://localhost:3000
```

---

## API Layer

All API calls go through `src/api/`:

```ts
// ✅ Correct — via API module
import { masterApi } from '@/api/master'
const data = await masterApi.list({ page: 1 })

// ❌ Wrong — never inline axios in components
import axios from 'axios'
const data = await axios.get('/api/master')
```

The `apiClient` in `src/api/client.ts`:
- Attaches JWT token from `localStorage` on every request
- Redirects to `/login` on 401 responses
- Base URL from `VITE_BFF_URL` env var

### React Query hooks

```ts
// Read
const { data, isLoading } = useMasterList({ page, search })
const { data } = useMasterItem(id)

// Write
const create = useCreateMaster()    // create.mutate(payload)
const update = useUpdateMaster(id)  // update.mutate(payload)
const del = useDeleteMaster()       // del.mutate(id)
```

---

## Authentication

- JWT token stored in `localStorage` (key: `auth_token`)
- Zustand store (`useAuthStore`) tracks `{ token, user, isAuthenticated }`
- `AppLayout` — redirects to `/login` if not authenticated
- `AuthLayout` — redirects to `/master` if already authenticated

---

## Pages & Routes

| Route | Component | Auth |
|-------|-----------|------|
| `/login` | LoginPage | Public |
| `/register` | RegisterPage | Public |
| `/dashboard` | DashboardPage | Protected |
| `/master` | MasterListPage | Protected |
| `/master/create` | MasterCreatePage | Protected |
| `/master/:id/edit` | MasterEditPage | Protected |
| `/preview` | PreviewPage | Protected |

---

## Design System

**Theme**: Light blue (`primary-*` = sky blue) + white.

### Utility classes (globals.css)

```css
.card         /* bg-white rounded-xl border shadow-sm */
.page-header  /* flex justify-between mb-6 */
.page-title   /* text-2xl font-bold text-slate-900 */
.form-label   /* text-sm font-medium text-slate-700 mb-1 */
.form-error   /* text-sm text-red-500 mt-1 */
```

### Component usage

```tsx
import { Button, Input, Modal, Badge, Table } from '@/components/ui'

<Button variant="primary" size="md" leftIcon={<Plus />}>Create</Button>
<Input label="Name" error={errors.name?.message} {...register('name')} />
<Badge variant="success" dot>Active</Badge>
<Modal open={open} onClose={close} title="Title" footer={...}>...</Modal>
```

---

## Testing

### Run tests

```bash
# Unit tests
npx vitest                   # Watch mode
npx vitest run               # Single run (CI)
npx vitest run src/hooks/    # Specific directory

# E2E tests
npm run test:e2e             # All browsers
npm run test:e2e:ui          # Interactive UI mode
npx playwright test --project=chromium   # Chromium only
```

### Test structure

```
src/
  hooks/__tests__/     # Hook unit tests (Vitest + RTL)
  components/__tests__/ # Component unit tests (Vitest + RTL)
  utils/__tests__/     # Util unit tests (Vitest)
e2e/
  auth.spec.ts         # Login, register, guards
  master.spec.ts       # CRUD flows
  navigation.spec.ts   # Sidebar, routes, preview page
  helpers/auth.ts      # loginAs(), logout(), TEST_USER
  helpers/bff.ts       # BFF route mocks (mockBff.*)
```

**Unit vs E2E**: Unit tests cover individual hooks/utils/components in isolation with mocked API. E2E tests cover full user flows with all BFF endpoints mocked via `page.route()`.

### data-testid reference

| Element | testid |
|---------|--------|
| Login form | `login-form` |
| Email input | `email-input` |
| Password input | `password-input` |
| Login button | `login-submit` |
| Master create btn | `create-master-btn` |
| Master form | `master-form` |
| Search input | `search-input` |
| Edit row button | `edit-{id}` |
| Delete row button | `delete-{id}` |
| Form submit | `submit-btn` |

---

## Docker

```bash
# Build and run
docker compose up --build

# Production build only
docker build -t react-frontend .
docker run -p 3000:80 react-frontend
```

The Nginx config (`nginx.conf`):
- Serves static files with 1-year cache headers
- Proxies `/api/` to the `bff` service
- Falls back to `index.html` for all routes (SPA support)
- Adds security headers

---

## CI/CD

GitHub Actions (`.github/workflows/ci.yml`):

| Job | Trigger | Description |
|-----|---------|-------------|
| `lint` | All PRs & pushes | TypeScript + ESLint |
| `build` | After lint | `npm run build` + upload artifact |
| `e2e` | After build | Playwright on Chromium |
| `docker` | Push to `main` only | Build & push image to GHCR |

### Required secrets/vars

| Name | Where | Description |
|------|-------|-------------|
| `GITHUB_TOKEN` | Auto | For GHCR push |
| `VITE_BFF_URL` | GitHub Vars | BFF URL for build |

---

## Agents

All agents live in `.claude/agents/`.

### `e2e-test-generator`
**Use**: Generate Playwright tests for new pages or flows.
```
/agent:e2e-test-generator Generate tests for the SettingsPage at /settings
```

### `component-generator`
**Use**: Scaffold a new design system component.
```
/agent:component-generator Create a Tooltip component that wraps any element
```

### `code-reviewer`
**Use**: Review a file or PR diff before merging.
```
/agent:code-reviewer Review src/pages/master/MasterListPage.tsx
```

### `ui-consistency-checker`
**Use**: Audit UI for design system compliance.
```
/agent:ui-consistency-checker Check all pages in src/pages/ for consistency
```

### `unit-test-generator`
**Use**: Generate Vitest + React Testing Library unit tests for hooks, utils, and components.
```
/agent:unit-test-generator Generate unit tests for src/hooks/useMaster.ts
/agent:unit-test-generator Generate tests for src/components/ui/Button.tsx
```

### `api-integration-guide`
**Use**: Wire up a new BFF endpoint or audit existing API integration (DTO mapping, error handling, hook structure).
```
/agent:api-integration-guide Add API integration for the /api/invoices domain
/agent:api-integration-guide Audit src/api/master.ts for error handling gaps
```

### `accessibility-checker`
**Use**: Audit pages or components for WCAG 2.1 AA issues — ARIA labels, keyboard nav, contrast, focus management.
```
/agent:accessibility-checker Audit src/pages/master/MasterListPage.tsx
/agent:accessibility-checker Audit all components in src/components/ui/
```

---

## Key Rules

1. **Never call backend directly** — only BFF via `src/api/`
2. **No business logic in components** — keep pages thin
3. **One API file per domain** — `auth.ts`, `master.ts`, etc.
4. **Always use `data-testid`** on interactive elements
5. **Always use design system components** from `src/components/ui/`
6. **Primary color = `primary-*`** — never raw `blue-*` or hex values
