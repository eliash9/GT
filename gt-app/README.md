# Laravel Production Ready Starter Pack

This is a robust, clean, and scalable Laravel starter template designed for real-world applications (SaaS, ERP, CRM). It follows strict coding standards, modular architecture, and modern frontend practices.

## Features

- **Architecture**: Modular Monolith / Clean Architecture principles.
- **Backend**: Laravel 11+, PHP 8.2+, Strict Types, PSR-12.
- **Frontend**: Inertia.js + Vue 3 (TypeScript, Composition API) + TailwindCSS.
- **Authentication**: Laravel Breeze + Spatie Permissions (RBAC).
- **Testing**: Pest PHP (Feature + Unit).
- **Code Quality**: Laravel Pint (Style), PHPStan (Static Analysis).
- **Security**: CSP, Sanctum, Rate Limiting, Policies.
- **Performance**: SSR (Server Side Rendering), ASM (Asset Managment).
- **Developer Experience**:
  - `make:module` command (example).
  - Pre-configured CI workflows.
  - Custom Form Requests & DTOs.

## Folder Structure

The application separates Domain logic from Infrastructure, but respects Laravel conventions where pragmatic.

```
src/
├── Modules/             # Domain Modules (User, Auth, Product, etc.)
│   ├── User/
│   │   ├── Domain/      # Entities, Value Objects, Contracts
│   │   ├── Application/ # Use Cases, DTOs, Jobs, Services
│   │   ├── Infrastructure/ # Implementations, Repositories
│   │   └── Presentation/   # API Resources, Requests (Controllers stay in Http if using standard routing, or here)
│   └── Shared/          # Shared Logic (Traits, Base Classes)
app/
├── Http/                # Request Handling
│   ├── Controllers/     # Thin controllers calling UseCases
│   └── Middleware/      # Global middleware
├── Models/              # Eloquent Models (Infrastructure Layer implementation of Entities)
├── Providers/           # Service Providers
└── ...
```

## Getting Started

## Included Modules

- **Auth**: Fully refactored to use DTOs and UseCases.
- **User**: Domain-driven structure.
- **Product**: Example CRUD module demonstrating the architecture.

## Getting Started

1. **Install Dependencies**
   ```bash
   composer install
   npm install --legacy-peer-deps
   ```

2. **Environment Setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Database**
   Make sure MySQL is running. The `gt_app` database will be created automatically if you use the provided script, or create it manually.
   ```bash
   # Create Database (Optional helper)
   php create_gt_app_db.php
   
   # Run Migrations & Seeders
   php artisan migrate:fresh --seed
   ```

4. **Build Frontend**
   ```bash
   npm run build
   ```

5. **Run Application**
   ```bash
   npm run dev
   php artisan serve
   ```

## Architecture Details

### Domain Layer (Core Business Logic)
- **Entities**: Pure PHP classes or Eloquent models enriched with domain behavior.
- **Value Objects**: Immutable objects (Email, Money, Coordinates).
- **Repositories**: Interfaces defining data access contracts.

### Application Layer (Orchestration)
- **Use Cases / Services**: Contains business flow (e.g., `CreateUserAction`, `ProcessOrderService`).
- **DTOs**: Data Transfer Objects for strictly typed data passing.

### Infrastructure Layer (Implementation)
- **Persistence**: Eloquent implementations of Repositories.
- **External Services**: API clients (Stripe, SendGrid).

### Presentation Layer (Interface)
- **Controllers**: Handle HTTP requests, validate input validation, call Application services, return responses.
- **Resources**: API Resources for consistent JSON output.
- **Inertia Pages**: Vue components.

## Best Practices
- **Strict Typing**: All function arguments and return types must be typed.
- **Thin Controllers**: No business logic in controllers.
- **Repository Pattern**: Use repositories to decouple persistence logic from business logic.
- **DTOs**: Use DTOs instead of passing arrays or request objects deep into the application.

## Testing
Run tests with Pest:
```bash
php artisan test
```

## Code Style
Fix code style with Pint:
```bash
./vendor/bin/pint
```
Analyze code with PHPStan:
```bash
./vendor/bin/phpstan analyse
```
