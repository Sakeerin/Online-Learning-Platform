# Online Learning Platform

A production-ready Learning Management System (LMS) built with Laravel and Vue.js, designed for scalability, security, and accessibility.

## 🎯 Vision

Deliver a modern, accessible, and secure learning platform that serves students, instructors, and administrators with an exceptional user experience.

## 🏗️ Architecture

**API-First Design**:
- **Backend**: Laravel 11.x REST API (PHP 8.2+)
- **Frontend**: Vue.js 3.x SPA with TypeScript
- **Database**: PostgreSQL 14+ (recommended) or MySQL 8.0+
- **Caching**: Redis 7.x
- **Real-time**: Laravel Broadcasting with Pusher/Socket.io

## 📋 Prerequisites

- PHP 8.2 or higher
- Composer 2.x
- Node.js 18.x or higher
- PostgreSQL 14+ or MySQL 8.0+
- Redis 7.x

## 🚀 Quickstart (< 10 minutes)

### 1. Clone & Install Dependencies

```bash
# Clone repository
git clone https://github.com/your-org/online-learning-platform.git
cd online-learning-platform

# Install backend dependencies
composer install

# Install frontend dependencies
npm install
```

### 2. Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure database in .env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=learning_platform
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Configure Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### 3. Database Setup

```bash
# Run migrations
php artisan migrate

# Seed initial data (optional)
php artisan db:seed
```

### 4. Start Development Servers

```bash
# Terminal 1: Laravel backend (API)
php artisan serve
# API available at http://localhost:8000

# Terminal 2: Vite frontend dev server
npm run dev
# Frontend available at http://localhost:5173
```

### 5. Access the Platform

- **Frontend**: http://localhost:5173
- **API**: http://localhost:8000/api/v1
- **API Documentation**: http://localhost:8000/api/documentation (if configured)

## 🧪 Running Tests

```bash
# Backend tests
php artisan test

# Frontend tests
npm run test

# E2E tests
php artisan dusk

# Run all tests
composer test && npm test
```

## 📚 Constitution & Standards

This project follows strict development standards defined in our **[Constitution](.specify/memory/constitution.md)**. All features must comply with these core principles:

### Core Principles

1. **API-First Architecture** - Clean separation between Laravel backend and Vue.js frontend
2. **Security & Data Protection** - FERPA/GDPR compliance, encryption, RBAC
3. **Test-First Development** - Tests written before implementation (NON-NEGOTIABLE)
4. **Accessibility Standards** - WCAG 2.1 AA minimum for all UI
5. **Performance & Scalability** - <200ms API response, handles 1000+ concurrent users
6. **Modular Feature Design** - Independent, testable, deployable features
7. **Documentation & User Experience** - Self-service learning, actionable errors

**📖 Read the full constitution**: [.specify/memory/constitution.md](.specify/memory/constitution.md)

## 🛠️ Development Workflow

### Creating a New Feature

```bash
# 1. Create feature specification
# Use /speckit.specify command or create in specs/[###-feature-name]/spec.md

# 2. Generate implementation plan
# Use /speckit.plan command - includes constitution check

# 3. Generate tasks
# Use /speckit.tasks command

# 4. Implement with test-first approach
# Write tests → Tests fail → Implement → Tests pass → Refactor
```

### Code Quality Standards

**Backend (Laravel)**:
- PSR-12 coding standard (Laravel Pint)
- 80% minimum test coverage
- All database queries via Eloquent (prevent SQL injection)
- Laravel Form Requests for validation
- Laravel Policies for authorization

**Frontend (Vue.js)**:
- ESLint (Vue.js recommended config)
- 70% minimum test coverage
- TypeScript for type safety
- Composition API (Vue 3)
- Pinia for state management

## 📁 Project Structure

```
online-learning-platform/
├── backend/                    # Laravel API
│   ├── app/
│   │   ├── Models/            # Eloquent models
│   │   ├── Http/
│   │   │   ├── Controllers/   # API controllers
│   │   │   ├── Requests/      # Form validation
│   │   │   └── Resources/     # API transformers
│   │   ├── Services/          # Business logic
│   │   ├── Policies/          # Authorization
│   │   └── Actions/           # Single-purpose actions
│   ├── database/
│   │   ├── migrations/        # Database schema
│   │   └── seeders/           # Test data
│   ├── routes/
│   │   └── api.php            # API routes (versioned)
│   └── tests/
│       ├── Feature/           # Integration tests
│       └── Unit/              # Unit tests
│
├── frontend/                   # Vue.js SPA
│   ├── src/
│   │   ├── components/        # Vue components
│   │   ├── views/             # Page components
│   │   ├── stores/            # Pinia stores
│   │   ├── composables/       # Composition API logic
│   │   ├── services/          # API clients
│   │   └── router/            # Vue Router
│   └── tests/
│       ├── unit/              # Component tests
│       └── e2e/               # End-to-end tests
│
├── specs/                      # Feature specifications
│   └── [###-feature-name]/
│       ├── spec.md            # Feature specification
│       ├── plan.md            # Implementation plan
│       ├── tasks.md           # Task breakdown
│       └── contracts/         # API contracts
│
└── .specify/                   # Project governance
    ├── memory/
    │   └── constitution.md    # Development standards
    └── templates/             # Spec templates
```

## 🔒 Security

- **Authentication**: Laravel Sanctum with secure token storage
- **Authorization**: Role-Based Access Control (RBAC) with Laravel Policies
- **Input Validation**: Server-side validation via Form Requests
- **Data Protection**: Encrypted at rest (sensitive data), HTTPS enforced
- **SQL Injection**: Prevented via Eloquent ORM
- **XSS Prevention**: Vue.js auto-escaping, Content Security Policy
- **CSRF Protection**: Enabled for all state-changing operations

**Report security vulnerabilities**: security@yourorganization.com

## ♿ Accessibility

All features meet **WCAG 2.1 Level AA** standards:
- Semantic HTML with proper ARIA labels
- Keyboard navigation support
- 4.5:1 color contrast ratio
- Screen reader compatible
- Captions/transcripts for video content

## 📊 Performance Targets

- **API Response Time**: <200ms (p95)
- **Initial Page Load**: <3s on 3G networks
- **Concurrent Users**: 1000+ during peak times
- **Database Queries**: N+1 prevention via eager loading
- **Caching**: Redis for frequently accessed data

## 🚢 Deployment

**Pre-Deployment Checklist**:
- [ ] All tests passing (backend + frontend + E2E)
- [ ] Code coverage meets minimums (80% backend, 70% frontend)
- [ ] Security scan clean (no critical vulnerabilities)
- [ ] Database migrations tested in staging
- [ ] Performance benchmarks within targets
- [ ] Accessibility audit passed

**Deployment Strategy**: Blue-green or rolling deployment with feature flags

## 📖 Documentation

- **Constitution**: [.specify/memory/constitution.md](.specify/memory/constitution.md)
- **API Documentation**: http://localhost:8000/api/documentation
- **Feature Specs**: [specs/](specs/)
- **Contributing Guide**: [CONTRIBUTING.md](CONTRIBUTING.md) *(to be created)*

## 🤝 Contributing

1. Read the [Constitution](.specify/memory/constitution.md)
2. Create feature specification using `/speckit.specify`
3. Follow test-first development workflow
4. Ensure all quality gates pass
5. Submit pull request with constitution compliance verification

## 📝 License

[Your License Here] - e.g., MIT, Apache 2.0, or Proprietary

## 📧 Support

- **Documentation**: [docs/](docs/)
- **Issues**: [GitHub Issues](https://github.com/your-org/online-learning-platform/issues)
- **Discussions**: [GitHub Discussions](https://github.com/your-org/online-learning-platform/discussions)

---

**Built with ❤️ for accessible, secure, and scalable education**

