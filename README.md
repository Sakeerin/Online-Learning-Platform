# Course Marketplace Platform

A comprehensive Udemy-style online learning marketplace built with Laravel 11 and Vue.js 3, where instructors create and monetize courses, and students discover, purchase, and learn through video-based content with interactive features.

## 🎯 Project Status

✅ **Phase 1: Project Setup - COMPLETE**

**Current State**:
- ✅ Project structure initialized (backend/, frontend/, docker/)
- ✅ Laravel 11 backend with PHP 8.2
- ✅ Vue.js 3 + TypeScript frontend with Vite
- ✅ Docker environment (PostgreSQL, Redis, Mailpit)
- ✅ Basic configuration and tooling

**Next Steps**: Phase 2 - Foundational Infrastructure (see `specs/001-course-marketplace/tasks.md`)

## 🏗️ Architecture

**API-First Design** (Constitution Principle I):
- **Backend**: Laravel 11.x REST API (PHP 8.2+) at `/api/v1/*`
- **Frontend**: Vue.js 3.x SPA with TypeScript and Composition API
- **Database**: PostgreSQL 14+ 
- **Caching**: Redis 7.x (cache, sessions, queues)
- **Storage**: AWS S3 + CloudFront CDN for video content
- **Payments**: Stripe integration for course monetization

## 📋 Prerequisites

### Option 1: Docker (Recommended)
- Docker Desktop installed and running
- That's it! Docker handles PHP, PostgreSQL, Redis, Node.js

### Option 2: Local Development
- PHP 8.2 or higher
- Composer 2.x
- Node.js 18.x or higher  
- PostgreSQL 14+
- Redis 7.x

## 🚀 Quickstart (< 10 minutes)

### Using Docker (Recommended)

```bash
# 1. Clone the repository (if not already cloned)
git clone https://github.com/your-org/online-learning-platform.git
cd online-learning-platform

# 2. Copy environment files
cp backend/env.template backend/.env
cp frontend/env.template frontend/.env

# 3. Start Docker services
docker-compose up -d

# 4. Install backend dependencies
docker-compose exec app composer install

# 5. Generate Laravel application key
docker-compose exec app php artisan key:generate

# 6. Run database migrations
docker-compose exec app php artisan migrate

# 7. Install frontend dependencies (in a new terminal)
cd frontend
npm install

# 8. Start frontend development server
npm run dev
```

**Access the platform**:
- **Frontend**: http://localhost:5173
- **Backend API**: http://localhost:8000/api/v1
- **Health Check**: http://localhost:8000/api/health
- **Mailpit** (Email Testing): http://localhost:8025

### Using Local Development

```bash
# 1. Clone and setup environment
git clone https://github.com/your-org/online-learning-platform.git
cd online-learning-platform
cp backend/env.template backend/.env
cp frontend/env.template frontend/.env

# 2. Configure backend .env file
# Update DB_HOST=127.0.0.1 (not 'pgsql')
# Update REDIS_HOST=127.0.0.1 (not 'redis')
# Update MAIL_HOST to your local mail server

# 3. Install backend dependencies
cd backend
composer install
php artisan key:generate
php artisan migrate

# 4. Install frontend dependencies
cd ../frontend
npm install

# 5. Start services (separate terminals)
# Terminal 1: Backend
cd backend && php artisan serve

# Terminal 2: Frontend  
cd frontend && npm run dev

# Terminal 3: Queue worker (for background jobs)
cd backend && php artisan queue:work
```

## 📁 Project Structure

```
online-learning-platform/
├── backend/                 # Laravel 11 API
│   ├── app/
│   │   ├── Models/         # Eloquent models (to be created)
│   │   ├── Http/
│   │   │   ├── Controllers/Api/V1/  # API controllers
│   │   │   ├── Requests/   # Form validation
│   │   │   └── Resources/  # API transformers
│   │   ├── Services/       # Business logic
│   │   ├── Policies/       # Authorization
│   │   └── Jobs/           # Background tasks
│   ├── database/
│   │   ├── migrations/     # Database schema
│   │   └── seeders/        # Test data
│   ├── routes/
│   │   └── api.php         # API routes (/api/v1/*)
│   └── tests/              # PHPUnit tests
│
├── frontend/               # Vue.js 3 SPA
│   ├── src/
│   │   ├── components/    # Vue components
│   │   ├── views/         # Page components
│   │   ├── stores/        # Pinia state management
│   │   ├── services/      # API clients (axios)
│   │   ├── router/        # Vue Router
│   │   └── types/         # TypeScript interfaces
│   └── tests/             # Vitest tests
│
├── docker/                # Docker configuration
│   ├── Dockerfile.backend
│   ├── Dockerfile.frontend
│   └── nginx/
│
├── specs/                 # Project specifications
│   └── 001-course-marketplace/
│       ├── spec.md        # Feature requirements
│       ├── plan.md        # Implementation plan
│       ├── tasks.md       # Task breakdown (280 tasks)
│       ├── data-model.md  # Database design
│       └── contracts/     # API contracts
│
└── docker-compose.yml     # Docker services
```

## 🧪 Running Tests

**Backend (PHPUnit)**:
```bash
# Using Docker
docker-compose exec app php artisan test

# Local
cd backend && php artisan test

# With coverage
php artisan test --coverage
```

**Frontend (Vitest)**:
```bash
cd frontend
npm run test           # Run once
npm run test:watch     # Watch mode
npm run test:coverage  # With coverage
```

## 📚 Project Documentation

**Core Documents**:
- **[Constitution](.specify/memory/constitution.md)** - 7 core development principles (NON-NEGOTIABLE)
- **[Feature Spec](specs/001-course-marketplace/spec.md)** - 9 user stories with acceptance criteria
- **[Implementation Plan](specs/001-course-marketplace/plan.md)** - Technical strategy & architecture
- **[Task Breakdown](specs/001-course-marketplace/tasks.md)** - 280 executable tasks
- **[Data Model](specs/001-course-marketplace/data-model.md)** - 14 entities with relationships
- **[API Contracts](specs/001-course-marketplace/contracts/)** - OpenAPI specifications
- **[Research](specs/001-course-marketplace/research.md)** - Technology decisions & rationale

### Constitution: 7 Core Principles

All development MUST comply with these principles:

1. ✅ **API-First Architecture** - Laravel REST API + Vue.js SPA separation
2. ✅ **Security & Data Protection** - FERPA/GDPR compliance, encryption, RBAC (NON-NEGOTIABLE)
3. ✅ **Test-First Development** - 80% backend, 70% frontend coverage (NON-NEGOTIABLE)
4. ✅ **Accessibility Standards** - WCAG 2.1 AA minimum
5. ✅ **Performance & Scalability** - <200ms API, 1000+ concurrent streams
6. ✅ **Modular Feature Design** - Independent, testable features
7. ✅ **Documentation & User Experience** - Self-service, actionable errors

**📖 Full Constitution**: [.specify/memory/constitution.md](.specify/memory/constitution.md)

## 🛠️ Development Workflow

### Current Implementation Status

**✅ Phase 1: Project Setup** (COMPLETE)
- Project structure initialized
- Laravel 11 & Vue.js 3 configured
- Docker environment ready
- Basic tooling in place

**⏭️ Next: Phase 2 - Foundational Infrastructure**

See `specs/001-course-marketplace/tasks.md` for the complete 280-task breakdown organized by user story.

### Implementing Features

Follow the task-driven approach defined in `tasks.md`:

1. **Phase 2: Foundational** (T019-T060) - 42 tasks
   - Database migrations for all entities
   - Authentication & authorization infrastructure
   - Core middleware and guards
   - BLOCKS all user story development

2. **Phase 3-5: MVP (P1)** - User Stories 1-3
   - US1: Instructor Course Creation (T061-T098)
   - US2: Student Course Discovery (T099-T121)
   - US3: Course Enrollment & Video Learning (T122-T161)

3. **Phase 6-8: Enhanced (P2)** - User Stories 4-6
   - US4: Payment & Transactions
   - US5: Reviews & Ratings
   - US6: Progress Tracking & Certificates

4. **Phase 9-11: Advanced (P3)** - User Stories 7-9
   - US7: Interactive Quizzes
   - US8: Course Discussions
   - US9: Instructor Analytics

### Development Commands

**Backend**:
```bash
# With Docker
docker-compose exec app php artisan migrate
docker-compose exec app php artisan make:model Course -m
docker-compose exec app php artisan make:controller Api/V1/CourseController
docker-compose exec app php artisan test

# Local
cd backend
php artisan migrate
php artisan make:model Course -m
php artisan test
```

**Frontend**:
```bash
cd frontend
npm run dev          # Start dev server
npm run build        # Build for production
npm run lint         # Run ESLint
npm run format       # Run Prettier
npm run type-check   # TypeScript validation
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

## 🎯 Feature Implementation Roadmap

### MVP (Priority P1) - Core Learning Loop
| User Story | Tasks | Status | Description |
|------------|-------|--------|-------------|
| US1: Instructor Course Creation | T061-T098 | 📋 Planned | Create, organize, publish courses |
| US2: Student Course Discovery | T099-T121 | 📋 Planned | Browse, search, filter courses |
| US3: Course Enrollment & Learning | T122-T161 | 📋 Planned | Enroll, watch videos, track progress |

**MVP Delivers**: Instructors create courses → Students discover → Students learn

### Enhanced Features (Priority P2) - Monetization
| User Story | Tasks | Status | Description |
|------------|-------|--------|-------------|
| US4: Payment & Transactions | T162-T191 | 📋 Planned | Stripe integration, revenue tracking |
| US5: Reviews & Ratings | T192-T208 | 📋 Planned | Course reviews, social proof |
| US6: Progress & Certificates | T209-T227 | 📋 Planned | Progress tracking, certificates |

### Advanced Features (Priority P3) - Interactivity
| User Story | Tasks | Status | Description |
|------------|-------|--------|-------------|
| US7: Interactive Quizzes | T228-T241 | 📋 Planned | Knowledge assessments |
| US8: Course Discussions | T242-T253 | 📋 Planned | Q&A, community support |
| US9: Instructor Analytics | T254-T265 | 📋 Planned | Revenue & engagement insights |

## 🤝 Contributing

1. **Read Documentation**:
   - [Constitution](.specify/memory/constitution.md) - Core principles
   - [Contributing Guide](CONTRIBUTING.md) - Code standards & workflow
   - [Task Breakdown](specs/001-course-marketplace/tasks.md) - What to work on

2. **Pick a Task**: 
   - Start with foundational tasks (T019-T060) if not complete
   - Then pick from current user story phase
   - Tasks marked [P] can run in parallel

3. **Follow Standards**:
   - Test-first development (write tests before code)
   - PSR-12 coding standard (Laravel Pint)
   - ESLint + Prettier for frontend
   - 80% backend, 70% frontend test coverage

4. **Submit PR**:
   - Include constitution compliance checklist
   - All tests passing
   - No linter errors
   - Accessibility verified (for UI changes)

## 📝 License

[Your License Here] - e.g., MIT, Apache 2.0, or Proprietary

## 📧 Support

- **Documentation**: [docs/](docs/)
- **Issues**: [GitHub Issues](https://github.com/your-org/online-learning-platform/issues)
- **Discussions**: [GitHub Discussions](https://github.com/your-org/online-learning-platform/discussions)

---

**Built with ❤️ for accessible, secure, and scalable education**

