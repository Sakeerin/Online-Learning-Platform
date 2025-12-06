# Research & Technology Decisions: Course Marketplace Platform

**Date**: 2025-12-06  
**Feature**: Course Marketplace Platform (001-course-marketplace)  
**Purpose**: Document technology choices, architecture decisions, and best practices for implementation

---

## Executive Summary

This document consolidates research findings for building a Udemy-style course marketplace platform. Key decisions include Laravel 11 + Vue.js 3 architecture, PostgreSQL for relational data, AWS S3/CloudFront for video delivery, Stripe for payments, and Redis for caching. All choices align with project constitution principles and are optimized for scalability, security, and developer productivity.

---

## 1. Backend Framework & Language

### Decision: Laravel 11.x with PHP 8.2+

**Rationale**:
- **Mature Ecosystem**: Laravel provides built-in features for authentication (Sanctum), authorization (Policies), queue management, and email delivery
- **API Development**: Laravel API Resources and Form Requests align perfectly with API-first architecture requirement
- **ORM Security**: Eloquent ORM prevents SQL injection attacks, meeting security requirements
- **Testing Support**: PHPUnit integration, database factories, and testing utilities support test-first development
- **Community**: Large community, extensive documentation, and packages for common LMS features
- **Performance**: PHP 8.2+ offers significant performance improvements (JIT compiler, optimized opcache)

**Alternatives Considered**:
- **Node.js (Express/NestJS)**: JavaScript full-stack would unify language, but Laravel's batteries-included approach provides faster development for this feature set
- **Python (Django/FastAPI)**: Strong in data science/ML, but Laravel ecosystem better suited for traditional web applications with complex business logic
- **Ruby on Rails**: Similar philosophy to Laravel, but smaller community and fewer modern packages

**Best Practices**:
- Use Laravel 11 service container for dependency injection
- Organize business logic in Services (multi-step operations) and Actions (single-purpose)
- Keep controllers thin (orchestration only, delegate to services)
- Use Laravel Events/Listeners for cross-module communication
- Leverage Laravel Horizon for queue monitoring

---

## 2. Frontend Framework

### Decision: Vue.js 3.x with Composition API and TypeScript

**Rationale**:
- **Reactivity**: Vue 3 Composition API provides efficient reactivity for complex UI like video players and real-time progress updates
- **TypeScript Support**: First-class TypeScript support ensures type safety for API contracts
- **Component Ecosystem**: Rich ecosystem of UI libraries (Vuetify, PrimeVue, Headless UI)
- **Bundle Size**: Vue 3 is lightweight (~34KB gzipped), important for 3-second load target
- **Learning Curve**: Gentler learning curve than React, easier for future contributors
- **Laravel Integration**: Strong integration patterns with Laravel (Inertia.js option available)

**Alternatives Considered**:
- **React**: Larger ecosystem, but more complex (JSX, hooks learning curve), and larger bundle size
- **Angular**: Full-featured framework but heavyweight for this use case, steeper learning curve
- **Svelte**: Excellent performance but smaller ecosystem, fewer component libraries

**Best Practices**:
- Use Composition API (not Options API) for better TypeScript support and code reuse
- Implement Pinia for state management (official Vue 3 replacement for Vuex)
- Use Vue Router with route guards for authentication
- Create composables for reusable logic (useAuth, useEnrollment, useVideoPlayer)
- Implement lazy loading for routes to optimize initial bundle size
- Use `<Suspense>` for async component loading

---

## 3. Database Selection

### Decision: PostgreSQL 14+

**Rationale**:
- **ACID Compliance**: Critical for financial transactions (course purchases, instructor payouts)
- **Relational Integrity**: Complex relationships (courses → sections → lessons → progress) benefit from foreign keys and cascading rules
- **JSON Support**: Flexible storage for quiz questions, analytics data without sacrificing relational benefits
- **Full-Text Search**: Built-in search capabilities for course discovery (can complement or replace Elasticsearch for MVP)
- **Performance**: Excellent query optimizer, support for indexes on computed columns, partitioning for large tables
- **Scalability**: Read replicas, connection pooling (pgBouncer), and horizontal scaling strategies proven at scale

**Alternatives Considered**:
- **MySQL 8.0**: Slightly simpler but lacks advanced features like full-text search quality and JSON query performance
- **MongoDB**: NoSQL flexibility not needed; relational data model is natural fit for this domain
- **SQLite**: Development convenience but not suitable for production at target scale (10,000+ students)

**Schema Design Best Practices**:
- Use UUIDs for primary keys (enables distributed ID generation, prevents enumeration attacks)
- Implement soft deletes for courses (students retain access even if instructor unpublishes)
- Use JSONB columns for flexible data (quiz question options, course metadata)
- Create indexes on foreign keys and frequently queried columns (course_id on enrollments, user_id on progress)
- Partition large tables (progress, transactions) by created_at for query performance
- Use database-level constraints (unique indexes, check constraints) to enforce business rules

---

## 4. Video Storage & Delivery

### Decision: AWS S3 + CloudFront CDN

**Rationale**:
- **Scalability**: S3 handles unlimited storage, CloudFront CDN serves 1000+ concurrent streams globally
- **Cost-Effective**: Pay-per-use model, cheaper than self-hosted storage at scale
- **Security**: Signed URLs for authenticated video access (prevent unauthorized sharing)
- **Performance**: CloudFront edge locations provide <3s video load time globally
- **Durability**: 99.999999999% durability, automatic redundancy across availability zones
- **Integration**: Official Laravel S3 driver, simple configuration

**Alternatives Considered**:
- **Self-Hosted Storage**: Full control but requires significant infrastructure investment, CDN setup, and maintenance
- **Vimeo API**: Easier integration but higher cost per video ($0.10/GB vs $0.023/GB for S3), vendor lock-in
- **YouTube Private**: Free but limited control, branding issues, terms of service restrictions for commercial use

**Implementation Strategy**:
1. **Upload Flow**:
   - Frontend uploads directly to S3 using pre-signed POST URLs (avoids backend bottleneck)
   - Laravel generates signed upload URL with policy (max size 5GB, allowed formats MP4/WebM)
   - S3 triggers Lambda function on upload completion to notify Laravel backend
   
2. **Storage Structure**:
   ```
   s3://bucket-name/
   ├── videos/
   │   ├── {course_id}/
   │   │   └── {lesson_id}/
   │   │       ├── original.mp4
   │   │       ├── 720p.mp4
   │   │       ├── 480p.mp4
   │   │       └── 360p.mp4
   ├── thumbnails/
   │   └── {course_id}/
   │       └── cover.jpg
   └── certificates/
       └── {certificate_id}.pdf
   ```

3. **Delivery**:
   - CloudFront distribution with signed URLs (expires in 24 hours)
   - Adaptive bitrate streaming (HLS/DASH) for quality selection
   - Laravel generates signed URLs on lesson access, frontend requests from CloudFront

**Best Practices**:
- Use S3 Lifecycle policies to archive old videos to Glacier (cost optimization)
- Enable S3 Transfer Acceleration for faster uploads from distant regions
- Implement video transcoding pipeline (AWS MediaConvert or Laravel queue job with FFmpeg)
- Generate multiple resolutions (1080p, 720p, 480p, 360p) for adaptive streaming
- Create video thumbnails on upload (FFmpeg or AWS Lambda)
- Set CORS policies on S3 bucket for direct frontend uploads

---

## 5. Payment Processing

### Decision: Stripe API Integration

**Rationale**:
- **PCI Compliance**: Stripe handles PCI compliance, platform never stores credit card data
- **Developer Experience**: Excellent API documentation, Laravel Cashier package for integration
- **Payment Methods**: Credit cards, PayPal, Apple Pay, Google Pay support
- **Refund Automation**: Programmatic refund API for 30-day refund policy implementation
- **Webhooks**: Real-time payment status updates (success, failure, dispute)
- **Revenue Share**: Stripe Connect supports marketplace model (platform takes 30% fee, instructor receives 70%)
- **Global**: Supports 135+ currencies, works in 46+ countries

**Alternatives Considered**:
- **PayPal**: Higher fees (2.9% + $0.30 vs Stripe 2.9% + $0.30 for standard, but PayPal business accounts have additional fees), less developer-friendly API
- **Square**: Limited international support, primarily US/Canada/UK
- **Authorize.Net**: Older technology, more complex integration, less flexible

**Implementation Strategy**:
1. **Laravel Cashier Integration**:
   ```bash
   composer require laravel/cashier
   ```
   
2. **Checkout Flow**:
   - Student initiates purchase → Laravel creates Stripe Checkout Session
   - Frontend redirects to Stripe hosted checkout (PCI-compliant, no credit card handling)
   - On success, Stripe webhook notifies Laravel → Enroll student
   - Laravel sends purchase receipt email

3. **Revenue Share Model**:
   - Use Stripe Connect (Express accounts for instructors)
   - Platform creates "transfer" from platform account to instructor account
   - Platform fee (30%) deducted automatically
   - Instructor sees net revenue in dashboard

4. **Refund Policy Automation**:
   - Store enrollment timestamp and "content_watched_percentage"
   - Refund endpoint checks: (current_date - enrollment_date) <= 30 days AND content_watched < 30%
   - Auto-approve if policy met, create Stripe refund, unenroll student
   - Auto-deny otherwise, provide reason to student

**Best Practices**:
- Use Stripe webhooks for payment confirmation (not redirect-only, prevents race conditions)
- Store Stripe payment_intent_id for reconciliation and dispute handling
- Implement idempotency keys for payment requests (prevent double-charging)
- Test with Stripe test mode and test card numbers before production
- Enable Stripe Radar for fraud detection
- Set up webhook signature verification to prevent spoofing

---

## 6. Caching Strategy

### Decision: Redis 7.x

**Rationale**:
- **Speed**: In-memory data store, <1ms read latency for cached data
- **Data Structures**: Support for strings, hashes, lists, sets (perfect for course lists, user sessions, leaderboards)
- **Laravel Integration**: Built-in Laravel cache and session driver
- **Queue Backend**: Laravel queues can use Redis for background job processing
- **Pub/Sub**: Real-time features (live notifications, course updates) via Redis pub/sub
- **Persistence**: Optional persistence for important cache data (course catalog)

**Caching Plan**:
1. **Course Catalog**: Cache course listings for 1 hour (invalidate on course update)
   - Key pattern: `courses:category:{category_id}:page:{page}`
   - Reduces database load for browse/search

2. **User Sessions**: Store session data in Redis (faster than database)
   - Key pattern: `session:{session_id}`
   - Enables horizontal scaling (shared session storage)

3. **Enrolled Courses**: Cache per-user enrollments for 15 minutes
   - Key pattern: `user:{user_id}:enrollments`
   - Reduces queries on "My Learning" page

4. **Video Playback Positions**: Cache lesson progress
   - Key pattern: `progress:{enrollment_id}:{lesson_id}`
   - Write-through cache (update both cache and database)

5. **API Rate Limiting**: Use Redis for rate limit counters
   - Key pattern: `rate_limit:{user_id}:{endpoint}:{window}`
   - Prevent abuse, ensure fair usage

**Best Practices**:
- Set appropriate TTL (Time To Live) for each cache key type
- Use cache tags for group invalidation (e.g., invalidate all course caches when one updates)
- Implement cache warming for popular courses (prevent cold start)
- Use Laravel cache events to log cache misses (optimize caching strategy)
- Deploy Redis in master-replica configuration for high availability
- Enable Redis persistence (AOF or RDB) for session data

---

## 7. Authentication & Authorization

### Decision: Laravel Sanctum (API Token Authentication)

**Rationale**:
- **SPA-Friendly**: Designed for SPAs, uses HTTP-only cookies for security
- **Stateless**: Token-based authentication for API, no session storage needed
- **CSRF Protection**: Built-in CSRF protection for same-origin requests
- **Simple**: Lighter than OAuth2 (Passport), sufficient for internal SPA usage
- **Mobile-Ready**: Same API can serve mobile apps with API tokens

**Authorization Strategy**:
- **Role-Based Access Control (RBAC)** using Laravel Policies
- User model has `role` enum: `student`, `instructor`, `admin`
- Policies check role and resource ownership:
  - `CoursePolicy::update()` → Only course owner (instructor) can update
  - `EnrollmentPolicy::access()` → Only enrolled student can access content
  - `ReviewPolicy::create()` → Only enrolled students can review

**Best Practices**:
- Store tokens in HTTP-only cookies (not localStorage, prevents XSS attacks)
- Use Laravel middleware `auth:sanctum` on protected routes
- Implement token expiration (7 days, require re-login)
- Create custom middleware for role checks (`EnsureInstructor`, `EnsureStudent`)
- Use Laravel Gates for simple permission checks, Policies for model-specific authorization
- Log authentication failures for security monitoring

---

## 8. Testing Strategy

### Decision: Multi-Layer Testing Approach

**Backend Testing (PHPUnit)**:
1. **Unit Tests**: Services, Actions, Policies (isolated, mocked dependencies)
   - Example: `CourseServiceTest` mocks repositories, tests business logic
   - Target: 80% coverage for services and policies

2. **Feature Tests**: API endpoints (integration, real database)
   - Example: `POST /api/v1/courses` with authenticated instructor
   - Asserts: 201 status, database record created, response structure
   - Target: 100% API endpoint coverage

3. **Contract Tests**: Validate API request/response schemas
   - Example: Ensure course JSON response matches OpenAPI spec
   - Prevents breaking changes in API contracts

**Frontend Testing (Vitest)**:
1. **Unit Tests**: Composables, utilities, stores
   - Example: `useAuth` composable with mocked API calls
   - Target: 70% coverage for composables and stores

2. **Component Tests**: Vue components with user interactions
   - Example: `CourseCard.vue` displays title, price, rating correctly
   - Use `@vue/test-utils` for mounting and interacting with components

3. **E2E Tests**: Critical user flows (Laravel Dusk or Cypress)
   - Example: Complete enrollment flow (browse → detail → enroll → watch video)
   - Target: Cover P1 user stories (MVP features)

**Best Practices**:
- **Test-First Workflow**: Write test → Run (should fail) → Implement → Run (should pass)
- **Database Transactions**: Wrap feature tests in transactions (rollback after each test)
- **Factories**: Use Laravel factories for test data generation
- **Mocking**: Mock external services (Stripe, AWS S3) in tests
- **CI/CD**: Run tests automatically on pull requests (GitHub Actions)
- **Coverage Reports**: Generate and review coverage reports, improve coverage over time

---

## 9. Background Job Processing

### Decision: Laravel Queues with Redis Driver

**Use Cases for Background Jobs**:
1. **Video Processing**: Transcode videos to multiple resolutions (long-running, CPU-intensive)
2. **Email Notifications**: Send enrollment confirmations, course updates (avoid blocking API responses)
3. **Certificate Generation**: Generate PDF certificates (I/O-intensive)
4. **Analytics Calculation**: Update instructor revenue, course statistics (batch processing)

**Queue Configuration**:
- **Queue Driver**: Redis (fast, reliable, supports priorities)
- **Job Types**:
  - `high` priority: Payment confirmations, critical notifications
  - `default` priority: Email notifications, progress updates
  - `low` priority: Analytics, cleanup jobs

**Best Practices**:
- Use `php artisan queue:work` with supervisor for production (auto-restart on failure)
- Implement job retries (3 attempts) with exponential backoff
- Use job batching for bulk operations (e.g., send email to all enrolled students)
- Monitor queues with Laravel Horizon (web UI for queue metrics)
- Set job timeouts to prevent infinite loops
- Use unique job IDs to prevent duplicate processing

---

## 10. Email Delivery

### Decision: Transactional Email Service (SendGrid or Mailgun)

**Rationale**:
- **Deliverability**: Professional email services have established sender reputation, avoid spam folders
- **Templates**: HTML email templates with variables (personalization)
- **Analytics**: Track email opens, clicks (measure engagement)
- **API Integration**: Laravel drivers for both services
- **Cost**: Free tier available (SendGrid: 100 emails/day, Mailgun: 5,000 emails/month)

**Email Types**:
1. **Transactional**:
   - Email verification (registration)
   - Password reset
   - Purchase receipts
   - Enrollment confirmations
   - Certificate delivery

2. **Notifications**:
   - Course published (to instructor)
   - New student enrolled (to instructor)
   - Course update (to enrolled students)
   - Lesson completed (progress milestone)

**Best Practices**:
- Use Laravel Mailables with Markdown templates (easy to maintain)
- Queue all emails (don't block API responses)
- Implement unsubscribe links for notification emails (not transactional)
- Use email verification before sending to prevent bounces
- Set up SPF and DKIM records for domain authentication
- Monitor bounce rates and adjust sending patterns

---

## 11. Search Functionality

### Decision: PostgreSQL Full-Text Search (MVP) → Elasticsearch (Later)

**MVP Approach**:
- Use PostgreSQL `tsvector` and `tsquery` for full-text search
- Create GIN index on course title and description
- Sufficient for initial course catalog (up to 10,000 courses)
- Simple Laravel Scout integration with database driver

**Future Enhancement (P3)**:
- Migrate to Elasticsearch when course catalog exceeds 10,000 courses
- Benefits: Fuzzy matching, relevance scoring, faceted search, autocomplete
- Laravel Scout supports Elasticsearch driver (easy migration)

**Best Practices** (MVP):
- Create search index: `CREATE INDEX courses_search_idx ON courses USING GIN(to_tsvector('english', title || ' ' || description))`
- Use Laravel Scout with database driver for unified search interface
- Implement search filters (category, price, rating) using SQL WHERE clauses
- Cache popular search results (1-hour TTL)

---

## 12. Error Handling & Logging

### Decision: Laravel Logging with Sentry Integration

**Logging Strategy**:
- **Local/Development**: Laravel log files (`storage/logs/laravel.log`)
- **Production**: Sentry for error tracking and alerting
- **Log Channels**:
  - `stack`: General application logs
  - `single`: Daily log files
  - `sentry`: Critical errors, exceptions

**Error Handling Patterns**:
1. **API Errors**: Return consistent JSON error responses
   ```json
   {
     "message": "Validation failed",
     "errors": {
       "title": ["The title field is required."]
     }
   }
   ```

2. **User-Friendly Messages**: Translate technical errors to actionable messages
   - Backend: "Database connection failed" → "Service temporarily unavailable. Please try again."
   - Frontend: Display toast notifications with retry options

3. **Logging Context**: Include user ID, request ID, and relevant data in logs
   ```php
   Log::error('Course publication failed', [
       'user_id' => $user->id,
       'course_id' => $course->id,
       'validation_errors' => $errors
   ]);
   ```

**Best Practices**:
- Use Laravel exception handler to customize error responses
- Log all payment failures with full context (for financial audit)
- Set up Sentry alerts for critical errors (email, Slack)
- Use log levels appropriately: `debug`, `info`, `warning`, `error`, `critical`
- Implement request ID header for distributed tracing
- Never log sensitive data (passwords, payment tokens)

---

## 13. Development Environment

### Decision: Docker + Laravel Sail

**Rationale**:
- **Consistency**: Same environment across all developers (macOS, Windows, Linux)
- **Dependencies Included**: PHP, PostgreSQL, Redis, Mailpit (email testing) pre-configured
- **Easy Setup**: `sail up` starts entire stack
- **Laravel Integration**: Sail is official Laravel Docker environment

**docker-compose.yml Services**:
- `app`: Laravel application (PHP 8.2, Nginx)
- `postgres`: PostgreSQL 14 database
- `redis`: Redis 7 cache and queue
- `mailpit`: Email testing UI
- `frontend`: Vue.js dev server (Vite)

**Best Practices**:
- Use Laravel Sail commands: `sail artisan`, `sail composer`, `sail npm`
- Map local storage directory for file persistence
- Use named volumes for database persistence (survives container restart)
- Configure Xdebug for debugging (disabled by default for performance)

---

## 14. Accessibility Implementation

### Decision: Headless UI + ARIA Best Practices

**Component Library**: Headless UI (Vue 3)
- Unstyled, accessible components (modals, dropdowns, tabs)
- Built-in keyboard navigation and screen reader support
- Customizable styling with Tailwind CSS

**Accessibility Checklist**:
1. **Semantic HTML**:
   - Use `<nav>`, `<main>`, `<section>`, `<article>` for structure
   - Proper heading hierarchy (`<h1>` → `<h2>` → `<h3>`)

2. **ARIA Labels**:
   - `aria-label` for icon-only buttons
   - `aria-describedby` for form field hints
   - `aria-live` for dynamic content updates (progress notifications)

3. **Keyboard Navigation**:
   - All interactive elements focusable via Tab
   - Modal traps focus (Esc to close)
   - Video player controls accessible (Space to play/pause, arrow keys for seek)

4. **Color Contrast**:
   - Use contrast checker tool (minimum 4.5:1 for normal text)
   - Provide visual indicators beyond color (icons, text labels)

5. **Video Captions**:
   - Support WebVTT caption files
   - Display captions toggle in video player

**Testing Tools**:
- **axe DevTools**: Browser extension for automated accessibility testing
- **NVDA/JAWS**: Screen reader testing (Windows)
- **VoiceOver**: Screen reader testing (macOS)

---

## 15. Performance Optimization

### Strategies:

1. **Backend**:
   - **Database Query Optimization**: Use `with()` for eager loading, avoid N+1 queries
   - **Response Caching**: Cache API responses for course catalog (1-hour TTL)
   - **Database Indexing**: Index foreign keys, frequently queried columns
   - **Connection Pooling**: Use pgBouncer for PostgreSQL connection pooling

2. **Frontend**:
   - **Code Splitting**: Lazy load routes with Vue Router
   - **Image Optimization**: Use WebP format, responsive images with `srcset`
   - **Bundle Optimization**: Tree-shaking unused code, minification in production
   - **Prefetching**: Prefetch next lesson while current lesson plays

3. **CDN**:
   - **Static Assets**: Serve frontend assets from CDN (CloudFlare or AWS CloudFront)
   - **Video Delivery**: CloudFront CDN for video streaming (global edge locations)

4. **Monitoring**:
   - **Laravel Telescope**: Local debugging and performance profiling
   - **New Relic/Datadog**: Production APM (application performance monitoring)
   - **Lighthouse CI**: Automated performance audits on deployment

---

## 16. Security Hardening

### Measures:

1. **API Security**:
   - Rate limiting: 60 requests/minute per user (Laravel throttle middleware)
   - CORS configuration: Whitelist frontend domain only
   - HTTPS enforced in production (redirect HTTP to HTTPS)

2. **Input Validation**:
   - Laravel Form Requests for all POST/PUT/PATCH endpoints
   - Whitelist allowed file types for uploads (videos: MP4/WebM, images: JPG/PNG)
   - Maximum file size limits (videos: 5GB, images: 5MB)

3. **Output Escaping**:
   - Vue.js automatic escaping prevents XSS
   - Laravel Blade `{{ }}` escapes output
   - Use `v-html` only for trusted content (markdown course descriptions)

4. **Video Security**:
   - Signed URLs with expiration (24-hour TTL)
   - Referrer policy to prevent hotlinking
   - Video DRM (optional P3 feature for premium content)

5. **Payment Security**:
   - Never store credit card data (Stripe Checkout handles all)
   - Validate webhook signatures from Stripe
   - Implement idempotency for payment requests

6. **Database Security**:
   - Use Eloquent ORM (prevents SQL injection)
   - Encrypt sensitive columns (personal data)
   - Regular database backups (daily)

---

## 17. Deployment Strategy

### Decision: Platform as a Service (PaaS) - Laravel Forge + DigitalOcean/AWS

**Rationale**:
- **Laravel Forge**: Server provisioning and deployment automation for Laravel
- **DigitalOcean/AWS**: Cloud infrastructure (compute, storage, databases)
- **Benefits**: Managed infrastructure, automatic deployments, zero-downtime deployments, SSL certificates, monitoring

**Deployment Pipeline**:
1. **Push to GitHub**: Developer pushes code to `main` branch
2. **CI Tests**: GitHub Actions runs tests (backend + frontend)
3. **Build**: Frontend assets compiled (`npm run build`)
4. **Deploy**: Forge deploys to production servers
5. **Migrations**: Database migrations run automatically
6. **Queue Restart**: Laravel queues restarted to load new code

**Environment Separation**:
- **Local**: Docker + Laravel Sail (development)
- **Staging**: Separate server, mirrors production config (testing)
- **Production**: Load-balanced servers, managed database, CDN

**Best Practices**:
- Use blue-green deployment (zero downtime)
- Implement health check endpoint (`/api/health`)
- Enable automatic backups (database, S3)
- Use environment variables for secrets (never commit to git)
- Implement rollback strategy (previous version deployment)

---

## Summary of Key Decisions

| Area | Technology | Rationale |
|------|-----------|-----------|
| **Backend** | Laravel 11 + PHP 8.2 | Mature ecosystem, API-first, security features |
| **Frontend** | Vue.js 3 + TypeScript | Reactive, lightweight, TypeScript support |
| **Database** | PostgreSQL 14+ | ACID compliance, JSON support, full-text search |
| **Video Storage** | AWS S3 + CloudFront | Scalable, cost-effective, global CDN |
| **Payment** | Stripe | PCI-compliant, marketplace features, developer-friendly |
| **Caching** | Redis 7.x | Fast, data structures, queue backend |
| **Auth** | Laravel Sanctum | SPA-friendly, simple, secure |
| **Email** | SendGrid/Mailgun | Deliverability, analytics, templates |
| **Search** | PostgreSQL FTS (MVP) | Simple, sufficient for MVP, upgrade path to Elasticsearch |
| **Testing** | PHPUnit + Vitest | Test-first workflow, 80%/70% coverage targets |
| **Deployment** | Laravel Forge + PaaS | Automated deployments, managed infrastructure |

---

## Next Steps

1. ✅ Research complete - All technology decisions documented
2. ⏭️ **Phase 1**: Create data model with entity relationships
3. ⏭️ **Phase 1**: Generate API contracts (OpenAPI specs)
4. ⏭️ **Phase 1**: Write quickstart guide for local development setup
5. ⏭️ **Phase 2**: Break down into tasks using `/speckit.tasks` command

All decisions align with project constitution and support the course marketplace feature requirements.

