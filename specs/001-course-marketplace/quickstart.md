# Quickstart Guide: Course Marketplace Platform

**Goal**: Get the platform running locally in under 10 minutes  
**Target**: Developers setting up for first time  
**Prerequisites**: Docker Desktop installed and running

---

## Quick Setup (10 Minutes)

### Step 1: Clone & Environment Setup (2 min)

```bash
# Clone repository
git clone https://github.com/your-org/online-learning-platform.git
cd online-learning-platform

# Copy environment file
cp .env.example .env

# Generate application key
./vendor/bin/sail artisan key:generate
```

### Step 2: Configure Environment (1 min)

Edit `.env` file with these essential values:

```env
APP_NAME="Course Marketplace"
APP_URL=http://localhost

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
# Start all services (Laravel, PostgreSQL, Redis, Mailpit)
./vendor/bin/sail up -d

# Wait for services to be healthy (~30 seconds)
# Check status: ./vendor/bin/sail ps
```

### Step 4: Database Setup (2 min)

```bash
# Run migrations
./vendor/bin/sail artisan migrate

# Seed database with sample data (optional)
./vendor/bin/sail artisan db:seed

# This creates:
# - 2 instructor accounts
# - 5 student accounts
# - 10 sample courses with sections and video lessons
# - Sample enrollments and reviews
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
**API Documentation**: http://localhost:8000/api/documentation

### Test Accounts (from seeder)

**Instructor**:
- Email: `instructor@example.com`
- Password: `password`

**Student**:
- Email: `student@example.com`
- Password: `password`

---

## Development Workflow

### Running Commands

Use Laravel Sail for all backend commands:

```bash
# Artisan commands
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan tinker

# Composer
./vendor/bin/sail composer install
./vendor/bin/sail composer require package-name

# Tests
./vendor/bin/sail artisan test
./vendor/bin/sail artisan test --filter CourseTest

# Queue worker (for background jobs)
./vendor/bin/sail artisan queue:work

# Code formatting
./vendor/bin/sail php ./vendor/bin/pint
```

Frontend commands (from `frontend/` directory):

```bash
# Development server
npm run dev

# Build for production
npm run build

# Lint
npm run lint

# Tests
npm run test
npm run test:watch

# Type checking
npm run type-check
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
2. Browse courses at http://localhost:5173/courses
3. Click on a course to view details
4. Click "Enroll Now" (free) or "Buy Now" (paid)
5. For paid courses, use Stripe test card: `4242 4242 4242 4242`
6. Access course from "My Learning"

### Test Video Upload

```bash
# Create sample video lesson
./vendor/bin/sail artisan tinker

# In tinker:
$lesson = App\Models\Lesson::factory()->create(['type' => 'video']);

# Upload test video via API
curl -X POST http://localhost:8000/api/v1/instructor/lessons/{lesson-id}/upload-video \
  -H "Authorization: Bearer {token}" \
  -F "video=@test-video.mp4"
```

### Test Email Notifications

1. Trigger email (e.g., register new account)
2. Open Mailpit at http://localhost:8025
3. View sent email in inbox

---

## Troubleshooting

### Port Already in Use

If port 8000, 5173, or 5432 is already in use:

```bash
# Stop conflicting services
./vendor/bin/sail down

# Change ports in docker-compose.yml
# Edit APP_PORT, FORWARD_DB_PORT, VITE_PORT
```

### Database Connection Failed

```bash
# Restart PostgreSQL container
./vendor/bin/sail restart pgsql

# Check logs
./vendor/bin/sail logs pgsql
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
./vendor/bin/sail artisan storage:link

# Ensure storage directory is writable
./vendor/bin/sail chmod -R 775 storage
```

### Tests Failing

```bash
# Create test database
./vendor/bin/sail artisan migrate --database=pgsql_testing

# Clear cache
./vendor/bin/sail artisan config:clear
./vendor/bin/sail artisan cache:clear

# Run tests
./vendor/bin/sail artisan test
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
│   │   └── Resources/    # API response transformers
│   ├── Services/         # Business logic (CourseService, PaymentService)
│   ├── Policies/         # Authorization (CoursePolicy, EnrollmentPolicy)
│   └── Jobs/             # Background tasks (ProcessVideoUpload, etc.)
├── database/migrations/  # Database schema
├── routes/api.php        # API routes
└── tests/                # PHPUnit tests
```

### Frontend Structure

```
frontend/
├── src/
│   ├── components/       # Vue components
│   ├── views/            # Page components
│   ├── stores/           # Pinia state management
│   ├── services/         # API clients
│   └── router/           # Vue Router
└── tests/                # Vitest tests
```

### Key Technologies

- **Backend**: Laravel 11, PHP 8.2, PostgreSQL 14, Redis 7
- **Frontend**: Vue.js 3, TypeScript, Vite, Pinia
- **Auth**: Laravel Sanctum (API tokens)
- **Storage**: AWS S3 (production), local storage (development)
- **Payments**: Stripe
- **Email**: SendGrid (production), Mailpit (development)

---

## Next Steps

1. **Read the Spec**: Review [spec.md](spec.md) for user stories and requirements
2. **Review Data Model**: Check [data-model.md](data-model.md) for database schema
3. **API Contracts**: Explore [contracts/](contracts/) for API documentation
4. **Run Tests**: Execute test suite to ensure setup is correct
5. **Start Developing**: Pick a user story and implement features

### Recommended First Task

Implement User Story 1 (Instructor Course Creation):
1. Create course form component (frontend)
2. Add course creation API endpoint (backend)
3. Write tests for creation flow
4. Test in browser

---

## Resources

- **Constitution**: [.specify/memory/constitution.md](../../.specify/memory/constitution.md)
- **Contributing**: [CONTRIBUTING.md](../../CONTRIBUTING.md)
- **Research**: [research.md](research.md)
- **Laravel Docs**: https://laravel.com/docs/11.x
- **Vue.js Docs**: https://vuejs.org/guide/
- **Stripe Testing**: https://stripe.com/docs/testing

---

## Getting Help

- **Issues**: Check existing issues or create new one
- **Discussions**: Join team discussions for questions
- **Docs**: Review specification documents in `specs/001-course-marketplace/`

**Happy coding!** 🚀

