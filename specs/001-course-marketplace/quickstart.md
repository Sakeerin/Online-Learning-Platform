# Quickstart Guide: Course Marketplace Platform

**Goal**: Get the platform running locally in under 10 minutes
**Target**: Developers setting up for first time
**Prerequisites**: Docker Desktop installed and running, Node.js 20+

---

## Quick Setup (10 Minutes)

### Step 1: Clone & Environment Setup (2 min)

```bash
# Clone repository
git clone https://github.com/Sakeerin/Online-Learning-Platform.git
cd Online-Learning-Platform

# Copy environment file
cp backend/.env.example backend/.env
```

### Step 2: Configure Environment (1 min)

Edit `backend/.env` file with these essential values:

```env
APP_NAME="Course Marketplace"
APP_URL=http://localhost:8000

# Database (PostgreSQL via Docker)
DB_CONNECTION=pgsql
DB_HOST=pgsql
DB_PORT=5432
DB_DATABASE=learning_platform
DB_USERNAME=sail
DB_PASSWORD=password

# Redis (via Docker)
REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

# Mail (Mailpit for testing)
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025

# AWS S3 (leave blank for local testing, will use storage/app/public)
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=

# Stripe (use test keys from https://dashboard.stripe.com/test/apikeys)
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...

# Frontend URL
FRONTEND_URL=http://localhost:5173
SANCTUM_STATEFUL_DOMAINS=localhost:5173
```

### Step 3: Start Docker Services (3 min)

```bash
# Start all services (Laravel, PostgreSQL, Redis, Mailpit, Nginx)
docker compose up -d

# Wait for services to be healthy (~30 seconds)
# Check status: docker compose ps

# Install Composer dependencies
docker compose exec app composer install

# Generate application key
docker compose exec app php artisan key:generate
```

### Step 4: Database Setup (2 min)

```bash
# Run migrations
docker compose exec app php artisan migrate

# Seed database with demo data
docker compose exec app php artisan db:seed

# This creates:
# - 2 instructor accounts (instructor@example.com, instructor2@example.com)
# - 5 student accounts (student@example.com through student5@example.com)
# - 1 admin account (admin@example.com)
# - 10 sample courses with sections, lessons, and quizzes
# - Sample enrollments, reviews, transactions, discussions, and certificates
# - All passwords: "password"
```

### Step 5: Frontend Setup (2 min)

```bash
# Navigate to frontend directory
cd frontend

# Install dependencies
npm install

# Start Vite dev server
npm run dev

# Frontend available at http://localhost:5173
```

### Step 6: Verify Installation

**Backend API**: http://localhost:8000/api/v1/health
**Frontend**: http://localhost:5173
**Mailpit** (email testing): http://localhost:8025
**API Documentation**: http://localhost:8000/docs/index.html

### Test Accounts (from seeder)

**Instructor**:
- Email: `instructor@example.com`
- Password: `password`

**Student**:
- Email: `student@example.com`
- Password: `password`

**Admin**:
- Email: `admin@example.com`
- Password: `password`

---

## Development Workflow

### Running Commands

Use Docker Compose for all backend commands:

```bash
# Artisan commands
docker compose exec app php artisan migrate
docker compose exec app php artisan tinker

# Composer
docker compose exec app composer install
docker compose exec app composer require package-name

# Tests
docker compose exec app php artisan test
docker compose exec app php artisan test --filter CourseTest

# Queue worker (for background jobs)
docker compose exec app php artisan queue:work

# Code formatting
docker compose exec app ./vendor/bin/pint

# Generate API documentation
docker compose exec app php artisan scribe:generate
```

Frontend commands (from `frontend/` directory):

```bash
# Development server
npm run dev

# Build for production
npm run build

# Lint
npm run lint

# Type checking
npx vue-tsc --noEmit
```

---

## Common Tasks

### Create a New Course (as Instructor)

1. Login as instructor at http://localhost:5173/login
2. Navigate to "Instructor Dashboard"
3. Click "Create Course"
4. Fill in course details (title, description, category, price)
5. Add sections and lessons
6. Upload video (saves to `storage/app/public/videos` locally)
7. Publish course

### Enroll in a Course (as Student)

1. Login as student at http://localhost:5173/login
2. Browse courses at http://localhost:5173/courses/browse
3. Click on a course to view details
4. Click "Enroll Now" (free) or "Buy Now" (paid)
5. For paid courses, use Stripe test card: `4242 4242 4242 4242`
6. Access course from "My Learning"

### Test Email Notifications

1. Trigger email (e.g., register new account, enroll in course)
2. Open Mailpit at http://localhost:8025
3. View sent email in inbox

---

## Troubleshooting

### Port Already in Use

If port 8000, 5173, or 5432 is already in use:

```bash
# Stop conflicting services
docker compose down

# Change ports in docker-compose.yml or .env
# Edit APP_PORT, FORWARD_DB_PORT, FORWARD_REDIS_PORT
```

### Database Connection Failed

```bash
# Restart PostgreSQL container
docker compose restart pgsql

# Check logs
docker compose logs pgsql
```

### Frontend Not Loading

```bash
# Clear npm cache
cd frontend
rm -rf node_modules package-lock.json
npm install

# Restart Vite
npm run dev
```

### Video Upload Fails

For local development without AWS S3:

```bash
# Create symbolic link for storage
docker compose exec app php artisan storage:link

# Ensure storage directory is writable
docker compose exec app chmod -R 775 storage
```

### Tests Failing

```bash
# Clear cache
docker compose exec app php artisan config:clear
docker compose exec app php artisan cache:clear

# Run tests
docker compose exec app php artisan test
```

---

## Architecture Overview

### Backend Structure

```
backend/
├── app/
│   ├── Models/           # Eloquent models (User, Course, Enrollment, etc.)
│   ├── Http/
│   │   ├── Controllers/  # API controllers (versioned /api/v1/*)
│   │   ├── Requests/     # Form validation
│   │   └── Middleware/    # Custom middleware (EnsureInstructor, EnsureStudent)
│   ├── Services/         # Business logic (CourseService, PaymentService, CacheService)
│   ├── Exceptions/       # Custom API exceptions
│   ├── Mail/             # Mailable classes (email templates)
│   ├── Policies/         # Authorization (CoursePolicy, EnrollmentPolicy)
│   ├── Jobs/             # Background tasks (ProcessVideoUpload, etc.)
│   └── Events/           # Domain events (CoursePublished, StudentEnrolled)
├── database/
│   ├── migrations/       # Database schema (18 migrations)
│   ├── seeders/          # Demo data seeders
│   └── factories/        # Model factories for testing
├── routes/api.php        # API routes (50+ endpoints)
└── tests/                # PHPUnit tests
```

### Frontend Structure

```
frontend/
├── src/
│   ├── components/       # Vue components (common, course, student, instructor)
│   ├── views/            # Page components (18 views)
│   ├── stores/           # Pinia state management (5 stores)
│   ├── composables/      # Vue composables (7 composables)
│   ├── services/         # API clients (axios-based)
│   ├── types/            # TypeScript interfaces
│   ├── plugins/          # Vue plugins (toast notifications)
│   └── router/           # Vue Router with auth guards
└── tests/                # Vitest tests
```

### Key Technologies

- **Backend**: Laravel 11, PHP 8.2, PostgreSQL 14, Redis 7
- **Frontend**: Vue.js 3, TypeScript, Vite 5, Pinia
- **Auth**: Laravel Sanctum (API tokens)
- **Storage**: AWS S3 (production), local storage (development)
- **Payments**: Stripe
- **Email**: Mailpit (development), configurable for production
- **Cache**: Redis with targeted invalidation
- **CI/CD**: GitHub Actions (backend tests + frontend tests)
- **Docker**: Development and production configurations

---

## Production Deployment

For production deployment, use the production Docker configuration:

```bash
# Build and start production services
docker compose -f docker-compose.prod.yml up -d --build

# Run migrations
docker compose -f docker-compose.prod.yml exec app php artisan migrate --force

# Seed initial data (first deployment only)
docker compose -f docker-compose.prod.yml exec app php artisan db:seed
```

---

## Resources

- **Specification**: [spec.md](spec.md)
- **Data Model**: [data-model.md](data-model.md)
- **Task List**: [tasks.md](tasks.md)
- **Laravel Docs**: https://laravel.com/docs/11.x
- **Vue.js Docs**: https://vuejs.org/guide/
- **Stripe Testing**: https://stripe.com/docs/testing
