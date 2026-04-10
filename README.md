# AI Backend Workflow

A modern, production-ready full-stack application with a **three-tier architecture**: React frontend, Laravel BFF (Backend for Frontend), and Laravel microservice backend.

## 📋 Project Overview

This project demonstrates a clean, scalable architecture separating concerns across three independent layers:

```
┌─────────────────────────┐
│   React Frontend (SPA)  │ ← User Interface
│    (react-frontend/)    │
└────────────┬────────────┘
             │ /api/* requests
             ↓
┌─────────────────────────┐
│  BFF - API Gateway      │ ← Token forwarding & aggregation
│       (bff/)            │
└────────────┬────────────┘
             │ Bearer token forwarding
             ↓
┌─────────────────────────┐
│  Laravel Backend        │ ← Business logic & database
│ (laravel-backend/)      │
└─────────────────────────┘
```

---

## 🏗️ Architecture

### **Three-Tier Stack**

| Component | Purpose | Technology | Key Feature |
|-----------|---------|-----------|------------|
| **react-frontend** | User interface | React + Vite + TypeScript | Single-page app, connects only to BFF |
| **bff** | API Gateway | Laravel (no database) | Token forwarding, request aggregation |
| **laravel-backend** | Business logic | Laravel + Clean Architecture + DDD | Independent microservice, Supabase + AWS SQS |

### **Design Principles**

- **Separation of Concerns**: Each layer has a single, well-defined responsibility
- **Stateless Token Forwarding**: Token passed from frontend through BFF to backend (never stored in BFF)
- **Concurrent Aggregation**: BFF fans out to backend services and consolidates responses
- **Clean Architecture**: Backend follows DDD principles with dependency inversion
- **Independent Deployment**: Each component can be deployed independently

---

## 📁 Folder Structure

```
ai-backend-workflow/
├── README.md                           # This file
├── laravel_demo.session.sql           # Database seed data (optional)
│
├── react-frontend/                    # React SPA (Vite + TypeScript)
│   ├── src/
│   │   ├── api/                       # HTTP client & API calls
│   │   ├── components/                # React components
│   │   ├── pages/                     # Page components
│   │   ├── hooks/                     # React Query & custom hooks
│   │   ├── store/                     # Zustand auth state
│   │   └── router/                    # Client-side routing
│   ├── e2e/                          # Playwright tests
│   ├── docker-compose.yml
│   ├── Dockerfile
│   └── nginx.conf
│
├── bff/                              # Backend for Frontend (Laravel)
│   ├── app/
│   │   ├── Clients/                  # BackendClient (HTTP orchestration)
│   │   ├── Services/                 # Aggregation services
│   │   ├── Transformers/             # Response DTOs
│   │   ├── Http/Controllers/         # Thin controllers
│   │   └── Middleware/               # Token validation
│   ├── config/                       # Laravel config
│   ├── routes/api.php                # API routes
│   ├── docker-compose.yml
│   ├── Dockerfile
│   └── composer.json
│
└── laravel-backend/                  # Laravel Backend (Microservice)
    ├── app/
    │   ├── Domain/                   # DDD domain layer (framework-agnostic)
    │   ├── Application/              # Use cases & DTOs
    │   ├── Infrastructure/           # Eloquent, repositories
    │   ├── Http/Controllers/         # Controllers
    │   ├── Models/                   # Eloquent models
    │   ├── Events/                   # Domain events (with queuing)
    │   └── Listeners/                # Event listeners
    ├── Modules/                      # Feature modules (Auth, Master, etc.)
    ├── config/                       # Laravel config
    ├── database/migrations/          # SQL migrations
    ├── routes/api.php                # API routes
    ├── docker-compose.yml
    ├── Dockerfile
    └── composer.json
```

---

## 🚀 Getting Started

### Prerequisites

- **Docker** & **Docker Compose** (recommended for consistent environment)
- OR **PHP 8.2+**, **Node.js 18+**, **Composer**, **npm/yarn**

### Quick Start with Docker Compose

Each component has its own `docker-compose.yml`. Start them in order:

#### 1️⃣ Start Laravel Backend

```bash
cd laravel-backend
docker-compose up -d
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed  # Optional
```

Backend API will run on `http://localhost:8001`

#### 2️⃣ Start BFF

```bash
cd ../bff
docker-compose up -d
```

BFF will run on `http://localhost:8000`

**Important**: BFF requires `BACKEND_URL` to point to the Laravel backend:
```bash
docker-compose exec app php artisan env:set BACKEND_URL=http://laravel-backend:8001
```

#### 3️⃣ Start React Frontend

```bash
cd ../react-frontend
docker-compose up -d
```

Frontend will run on `http://localhost:5173` (dev mode) or `http://localhost:3000` (production)

---

## 🔌 API Integration Flow

### Request Flow (Example: Dashboard)

```
1. Frontend (React)
   → GET /api/dashboard
   
2. BFF (Laravel)
   ├── Receives request with Bearer token
   ├── Extracts token from Authorization header
   ├── Calls BackendClient concurrently:
   │   ├── GET /api/users (with token)
   │   ├── GET /api/stats (with token)
   │   └── GET /api/recent-activity (with token)
   ├── Transforms responses into DTOs
   └── Returns unified JSON response
   
3. Backend (Laravel Microservice)
   ├── Validates token
   ├── Queries Supabase (PostgreSQL)
   ├── Publishes domain events → AWS SQS
   └── Returns data
   
4. Frontend (React)
   ← Receives aggregated dashboard data
   → Renders UI
```

### Token Lifecycle

- ✅ Frontend generates token (via backend auth endpoint)
- ✅ Frontend stores token in secure storage (localStorage/sessionStorage)
- ✅ Frontend sends token in `Authorization: Bearer <token>` header
- ✅ BFF extracts and forwards token to backend
- ✅ Backend validates token
- ❌ Token **never** stored in BFF (stateless gateway)

---

## 🛠️ Development

### Backend Development

```bash
cd laravel-backend

# Install dependencies
composer install

# Generate OpenAPI docs (Swagger)
php artisan l5-swagger:generate

# Run tests
php artisan test

# Run code style fixes
./vendor/bin/pint
```

**API Documentation**: `http://localhost:8001/api/documentation`

### BFF Development

```bash
cd bff

# Install dependencies
composer install

# Run tests
./vendor/bin/phpunit

# Run tinker (REPL)
php artisan tinker
```

### Frontend Development

```bash
cd react-frontend

# Install dependencies
npm install

# Start dev server
npm run dev

# Run E2E tests
npm run test:e2e

# Build for production
npm run build
```

**Preview**: `http://localhost:5173`

---

## 📦 Environment Variables

### BFF (.env)

```env
BACKEND_URL=http://laravel-backend:8001
BACKEND_TIMEOUT=30
```

### Laravel Backend (.env)

```env
DB_CONNECTION=pgsql
DB_HOST=supabase.co
DB_DATABASE=postgres
DB_USERNAME=...
DB_PASSWORD=...

QUEUE_CONNECTION=sqs
AWS_ACCESS_KEY_ID=...
AWS_SECRET_ACCESS_KEY=...
AWS_DEFAULT_REGION=...
```

### React Frontend (.env)

```env
VITE_API_BASE_URL=http://localhost:8000
```

---

## 🧪 Testing

### Backend Unit Tests

```bash
cd laravel-backend
php artisan test --filter=UnitTest
```

### Backend Feature Tests

```bash
php artisan test --filter=FeatureTest
```

### Frontend E2E Tests

```bash
cd react-frontend
npm run test:e2e
```

---

## 📚 Key Files to Review

| File | Purpose |
|------|---------|
| [bff/CLAUDE.md](bff/CLAUDE.md) | BFF architecture & conventions |
| [laravel-backend/CLAUDE.md/CLAUDE.md](laravel-backend/CLAUDE.md/CLAUDE.md) | Backend Clean Architecture |
| [react-frontend/CLAUDE.md](react-frontend/CLAUDE.md) | Frontend structure & patterns |

---

## 🔍 Notable Features

### Backend
- ✅ Clean Architecture + DDD
- ✅ Domain-driven events with queue support
- ✅ OpenAPI/Swagger documentation
- ✅ Comprehensive test coverage
- ✅ Supabase PostgreSQL integration
- ✅ AWS SQS queue handling

### BFF
- ✅ Stateless token forwarding
- ✅ Concurrent aggregation
- ✅ Transformer-based DTOs
- ✅ Zero database (pure gateway)

### Frontend
- ✅ TypeScript with React Query
- ✅ Zustand state management
- ✅ Tailwind CSS design system
- ✅ Vite bundling
- ✅ Playwright E2E tests
- ✅ Protected routes

---

## 🐛 Troubleshooting

### BFF can't reach backend

Check `BACKEND_URL` in BFF `.env`:
```bash
cd bff
php artisan env:set BACKEND_URL=http://laravel-backend:8001  # Docker
# OR
php artisan env:set BACKEND_URL=http://localhost:8001  # Local
```

### Frontend can't reach BFF

Check `VITE_API_BASE_URL` in React `.env`:
```env
VITE_API_BASE_URL=http://localhost:8000  # Local dev
VITE_API_BASE_URL=http://bff:8000        # Docker
```

### Database migrations fail

```bash
cd laravel-backend
php artisan migrate:fresh --seed
```

### Clear all caches

```bash
# Backend
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Frontend
rm -rf node_modules/.vite
npm cache clean --force
```

---

## 📝 License

Private project

---

## 👥 Contributing

1. Follow the architecture patterns in `CLAUDE.md` files
2. Write tests for new features
3. Ensure OpenAPI docs are updated
4. Run linters before committing

---

## 📞 Support

Refer to the `CLAUDE.md` files in each component for detailed architecture and guidelines.
