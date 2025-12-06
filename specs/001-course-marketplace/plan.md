# Implementation Plan: Course Marketplace Platform

**Branch**: `001-course-marketplace` | **Date**: 2025-12-06 | **Spec**: [spec.md](spec.md)
**Input**: Feature specification from `/specs/001-course-marketplace/spec.md`

**Note**: This plan outlines the technical implementation strategy for a comprehensive Udemy-style online learning platform with instructor course creation, student enrollment, video-based learning, payment processing, and interactive features.

## Summary

Build a complete course marketplace platform where instructors create and monetize video-based courses, and students discover, purchase, and learn at their own pace. The platform implements a two-sided marketplace with robust video streaming, secure payment processing, progress tracking, interactive assessments, and community features.

**Technical Approach**: API-first architecture with Laravel 11 REST backend providing versioned endpoints consumed by Vue.js 3 SPA frontend. PostgreSQL for relational data (courses, users, enrollments, transactions), Redis for caching and session management, AWS S3/CloudFront for video hosting and delivery. Stripe for payment processing, Laravel Sanctum for API authentication, and comprehensive test coverage using PHPUnit (backend) and Vitest (frontend).

## Technical Context

**Language/Version**: PHP 8.2+ (backend), TypeScript 5.x (frontend)  
**Primary Dependencies**: Laravel 11.x, Vue.js 3.x with Composition API, Inertia.js (optional) or standalone SPA  
**Storage**: PostgreSQL 14+ (primary database), AWS S3 (video/file storage), Redis 7.x (cache, sessions, queues)  
**Testing**: PHPUnit 10.x (backend unit/feature tests), Vitest (frontend component tests), Laravel Dusk (E2E tests)  
**Target Platform**: Linux server (Ubuntu 22.04 LTS), web browsers (Chrome, Firefox, Safari, Edge - last 2 versions)  
**Project Type**: Web application with separate backend API and frontend SPA  
**Performance Goals**: 1000+ concurrent video streams, <200ms API response time (p95), <3s initial page load on 3G  
**Constraints**: <200ms API p95, video playback <3s load time, 99.9% uptime, WCAG 2.1 AA accessibility  
**Scale/Scope**: Support 10,000+ students, 1,000+ instructors, 5,000+ courses, handle peak traffic during course launches

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

Verify compliance with `.specify/memory/constitution.md` principles:

- [x] **I. API-First Architecture**: ✅ Laravel REST API backend (`/api/v1/*`) with Vue.js 3 SPA frontend consuming versioned endpoints. All business logic in Laravel services/actions, frontend uses Pinia for state management reflecting API models. No direct database access from frontend.

- [x] **II. Security & Data Protection**: ✅ Laravel Sanctum for API authentication with token management. Role-based access control (RBAC) using Laravel Policies (instructor vs. student roles). All inputs validated server-side via Form Requests. Sensitive data (payment info, personal data) encrypted at rest and HTTPS enforced. Video streaming authenticated to prevent unauthorized access. Financial transactions logged for audit.

- [x] **III. Test-First Development**: ✅ 40 acceptance scenarios in spec.md provide test specifications. PHPUnit tests for backend (API endpoints, services, policies) targeting 80% coverage. Vitest tests for frontend components (course discovery, video player, enrollment flows) targeting 70% coverage. Contract tests for all API endpoints. Red-Green-Refactor workflow enforced.

- [x] **IV. Accessibility Standards**: ✅ All UI components meet WCAG 2.1 AA standards. Semantic HTML with proper heading hierarchy. Keyboard navigation for course browsing, video playback controls, and form interactions. Color contrast 4.5:1 for text. Screen reader compatibility tested for enrollment flows. Video player includes captions support. Form labels and error messages accessible.

- [x] **V. Performance & Scalability**: ✅ API response <200ms p95 (per spec SC-008, SC-011). Video streaming via AWS CloudFront CDN loads <3s (SC-004). 1000+ concurrent streams supported (SC-006). Database queries use eager loading to prevent N+1. Pagination for course lists, student lists, reviews. Redis caching for course catalog, user sessions, and frequently accessed data. Laravel queues for background jobs (video processing, email notifications, certificate generation).

- [x] **VI. Modular Feature Design**: ✅ 9 user stories organized into independent modules: Course Management, Student Discovery, Video Learning, Payment Processing, Reviews, Progress Tracking, Quizzes, Discussions, Analytics. Each module can be developed and deployed independently using feature flags. Laravel service layer provides clear boundaries. Vue components organized by feature domain. Database migrations reversible and independent.

- [x] **VII. Documentation & User Experience**: ✅ API documentation generated using Laravel API Resource documentation or Scribe. PHPDoc for all public methods. In-app help text for course creation workflow. Actionable error messages ("Course must have at least one video lesson to publish"). Progress indicators for video uploads. README with 10-minute quickstart. OpenAPI contracts in `/contracts/` directory.

**Technology Stack Compliance**:
- [x] Laravel 11.x + PHP 8.2+ (backend)
- [x] Vue.js 3.x + TypeScript (frontend)
- [x] PostgreSQL 14+ (database)
- [x] Redis 7.x (caching, sessions, queues)
- [x] PHPUnit 10.x + Vitest (testing)

**Violations Requiring Justification**: None

**Additional Technologies** (Constitution-compliant additions):
- **AWS S3 + CloudFront**: Video hosting and CDN delivery (required for scalable video streaming)
- **Stripe API**: Payment processing (required for monetization, PCI-compliant)
- **Laravel Sanctum**: API authentication (Laravel ecosystem standard)
- **Pinia**: Vue.js state management (Vue 3 recommended, replaces Vuex)
- **Laravel Queues**: Background job processing (Laravel built-in)
- **Vite 5.x**: Frontend build tool (Laravel 11 default)

**Post-Design Re-Evaluation**: ✅ All principles remain compliant after Phase 1 design. No violations introduced.

## Project Structure

### Documentation (this feature)

```text
specs/001-course-marketplace/
├── plan.md              # This file (/speckit.plan command output)
├── research.md          # Phase 0 output (/speckit.plan command)
├── data-model.md        # Phase 1 output (/speckit.plan command)
├── quickstart.md        # Phase 1 output (/speckit.plan command)
├── contracts/           # Phase 1 output (/speckit.plan command)
│   ├── courses-api.yaml         # Course management endpoints
│   ├── enrollments-api.yaml     # Student enrollment endpoints
│   ├── payments-api.yaml        # Transaction endpoints
│   ├── reviews-api.yaml         # Rating & review endpoints
│   └── analytics-api.yaml       # Instructor analytics endpoints
├── checklists/          # Quality validation
│   └── requirements.md  # Specification quality checklist (PASSED)
├── spec.md              # Feature specification
└── tasks.md             # Phase 2 output (/speckit.tasks command - NOT created yet)
```

### Source Code (repository root)

```text
backend/                           # Laravel 11 API
├── app/
│   ├── Models/                   # Eloquent models
│   │   ├── User.php              # Users (students & instructors)
│   │   ├── Course.php            # Courses
│   │   ├── Section.php           # Course sections
│   │   ├── Lesson.php            # Course lessons (video/quiz/article)
│   │   ├── Enrollment.php        # Student-course relationship
│   │   ├── Progress.php          # Lesson completion tracking
│   │   ├── Transaction.php       # Payment records
│   │   ├── Review.php            # Course reviews
│   │   ├── Quiz.php              # Assessments
│   │   ├── Question.php          # Quiz questions
│   │   ├── QuizAttempt.php       # Student quiz submissions
│   │   ├── Discussion.php        # Q&A posts
│   │   ├── Reply.php             # Discussion answers
│   │   └── Certificate.php       # Completion certificates
│   │
│   ├── Http/
│   │   ├── Controllers/Api/V1/  # API controllers (versioned)
│   │   │   ├── Auth/
│   │   │   │   ├── LoginController.php
│   │   │   │   ├── RegisterController.php
│   │   │   │   └── PasswordResetController.php
│   │   │   ├── Instructor/
│   │   │   │   ├── CourseController.php
│   │   │   │   ├── SectionController.php
│   │   │   │   ├── LessonController.php
│   │   │   │   ├── QuizController.php
│   │   │   │   └── AnalyticsController.php
│   │   │   ├── Student/
│   │   │   │   ├── CourseDiscoveryController.php
│   │   │   │   ├── EnrollmentController.php
│   │   │   │   ├── LearningController.php
│   │   │   │   ├── ProgressController.php
│   │   │   │   ├── ReviewController.php
│   │   │   │   └── CertificateController.php
│   │   │   └── Payment/
│   │   │       ├── CheckoutController.php
│   │   │       ├── WebhookController.php
│   │   │       └── RefundController.php
│   │   │
│   │   ├── Requests/             # Form validation
│   │   │   ├── CreateCourseRequest.php
│   │   │   ├── UpdateCourseRequest.php
│   │   │   ├── UploadVideoRequest.php
│   │   │   ├── EnrollmentRequest.php
│   │   │   ├── ReviewRequest.php
│   │   │   └── CheckoutRequest.php
│   │   │
│   │   ├── Resources/            # API response transformers
│   │   │   ├── CourseResource.php
│   │   │   ├── CourseDetailResource.php
│   │   │   ├── LessonResource.php
│   │   │   ├── EnrollmentResource.php
│   │   │   ├── ReviewResource.php
│   │   │   └── TransactionResource.php
│   │   │
│   │   └── Middleware/
│   │       ├── EnsureInstructor.php
│   │       ├── EnsureStudent.php
│   │       └── EnsureEnrolled.php
│   │
│   ├── Services/                 # Business logic
│   │   ├── CourseService.php     # Course creation, publishing
│   │   ├── VideoService.php      # Video upload, processing
│   │   ├── EnrollmentService.php # Student enrollment logic
│   │   ├── PaymentService.php    # Payment processing, refunds
│   │   ├── ProgressService.php   # Lesson completion tracking
│   │   ├── CertificateService.php# Certificate generation
│   │   ├── ReviewService.php     # Review moderation
│   │   └── AnalyticsService.php  # Instructor insights
│   │
│   ├── Actions/                  # Single-purpose action classes
│   │   ├── PublishCourseAction.php
│   │   ├── ProcessPaymentAction.php
│   │   ├── GenerateCertificateAction.php
│   │   └── CalculateProgressAction.php
│   │
│   ├── Policies/                 # Authorization
│   │   ├── CoursePolicy.php      # Can create, update, delete course
│   │   ├── EnrollmentPolicy.php  # Can enroll, access content
│   │   └── ReviewPolicy.php      # Can review, edit own review
│   │
│   ├── Events/                   # Domain events
│   │   ├── CoursePublished.php
│   │   ├── StudentEnrolled.php
│   │   ├── LessonCompleted.php
│   │   ├── CourseCompleted.php
│   │   └── PaymentProcessed.php
│   │
│   ├── Listeners/                # Event handlers
│   │   ├── SendCoursePublishedNotification.php
│   │   ├── SendEnrollmentConfirmation.php
│   │   ├── UpdateCourseProgress.php
│   │   ├── IssueCertificate.php
│   │   └── UpdateInstructorRevenue.php
│   │
│   └── Jobs/                     # Background processing
│       ├── ProcessVideoUpload.php
│       ├── GenerateVideoThumbnail.php
│       ├── SendTransactionReceipt.php
│       └── GenerateCourseCertificate.php
│
├── database/
│   ├── migrations/               # Database schema
│   │   ├── 2024_01_01_create_users_table.php
│   │   ├── 2024_01_02_create_courses_table.php
│   │   ├── 2024_01_03_create_sections_table.php
│   │   ├── 2024_01_04_create_lessons_table.php
│   │   ├── 2024_01_05_create_enrollments_table.php
│   │   ├── 2024_01_06_create_progress_table.php
│   │   ├── 2024_01_07_create_transactions_table.php
│   │   ├── 2024_01_08_create_reviews_table.php
│   │   ├── 2024_01_09_create_quizzes_table.php
│   │   ├── 2024_01_10_create_questions_table.php
│   │   ├── 2024_01_11_create_quiz_attempts_table.php
│   │   ├── 2024_01_12_create_discussions_table.php
│   │   ├── 2024_01_13_create_replies_table.php
│   │   └── 2024_01_14_create_certificates_table.php
│   │
│   ├── seeders/                  # Test data
│   │   ├── UserSeeder.php        # Sample students & instructors
│   │   ├── CourseSeeder.php      # Sample courses
│   │   └── DatabaseSeeder.php
│   │
│   └── factories/                # Model factories
│       ├── UserFactory.php
│       ├── CourseFactory.php
│       └── EnrollmentFactory.php
│
├── routes/
│   └── api.php                   # API routes (versioned /api/v1/*)
│
├── tests/
│   ├── Feature/                  # Integration tests
│   │   ├── Auth/
│   │   │   ├── LoginTest.php
│   │   │   └── RegisterTest.php
│   │   ├── Instructor/
│   │   │   ├── CourseCreationTest.php
│   │   │   ├── CoursePublishingTest.php
│   │   │   └── VideoUploadTest.php
│   │   ├── Student/
│   │   │   ├── CourseDiscoveryTest.php
│   │   │   ├── EnrollmentTest.php
│   │   │   ├── VideoLearningTest.php
│   │   │   └── ProgressTrackingTest.php
│   │   └── Payment/
│   │       ├── CheckoutTest.php
│   │       └── RefundTest.php
│   │
│   ├── Unit/                     # Unit tests
│   │   ├── Services/
│   │   │   ├── CourseServiceTest.php
│   │   │   ├── PaymentServiceTest.php
│   │   │   └── ProgressServiceTest.php
│   │   └── Policies/
│   │       ├── CoursePolicyTest.php
│   │       └── EnrollmentPolicyTest.php
│   │
│   └── Contract/                 # API contract tests
│       ├── CourseApiContractTest.php
│       ├── EnrollmentApiContractTest.php
│       └── PaymentApiContractTest.php
│
├── storage/
│   └── app/
│       └── public/               # Temporary file storage
│
├── config/
│   ├── filesystems.php           # S3 configuration
│   ├── services.php              # Stripe, email service configs
│   └── sanctum.php               # API auth configuration
│
└── composer.json                 # PHP dependencies

frontend/                          # Vue.js 3 SPA
├── src/
│   ├── components/               # Reusable components
│   │   ├── common/
│   │   │   ├── Button.vue
│   │   │   ├── Input.vue
│   │   │   ├── Card.vue
│   │   │   ├── Modal.vue
│   │   │   └── VideoPlayer.vue   # Custom video player component
│   │   │
│   │   ├── course/
│   │   │   ├── CourseCard.vue
│   │   │   ├── CourseGrid.vue
│   │   │   ├── CourseFilters.vue
│   │   │   ├── CourseCurriculum.vue
│   │   │   └── CourseProgress.vue
│   │   │
│   │   ├── instructor/
│   │   │   ├── CourseForm.vue
│   │   │   ├── SectionEditor.vue
│   │   │   ├── LessonUploader.vue
│   │   │   ├── QuizBuilder.vue
│   │   │   └── AnalyticsDashboard.vue
│   │   │
│   │   └── student/
│   │       ├── EnrollmentButton.vue
│   │       ├── LessonPlayer.vue
│   │       ├── ProgressBar.vue
│   │       ├── ReviewForm.vue
│   │       └── Certificate.vue
│   │
│   ├── views/                    # Page components (route targets)
│   │   ├── Home.vue              # Homepage with featured courses
│   │   ├── Auth/
│   │   │   ├── Login.vue
│   │   │   ├── Register.vue
│   │   │   └── ForgotPassword.vue
│   │   ├── Courses/
│   │   │   ├── Browse.vue        # Course catalog
│   │   │   ├── Detail.vue        # Course detail page
│   │   │   └── Search.vue        # Search results
│   │   ├── Instructor/
│   │   │   ├── Dashboard.vue     # Instructor home
│   │   │   ├── CreateCourse.vue
│   │   │   ├── EditCourse.vue
│   │   │   ├── Analytics.vue
│   │   │   └── Revenue.vue
│   │   ├── Student/
│   │   │   ├── MyLearning.vue    # Enrolled courses
│   │   │   ├── CoursePlayer.vue  # Video learning interface
│   │   │   ├── Certificates.vue
│   │   │   └── Profile.vue
│   │   └── Payment/
│   │       ├── Checkout.vue
│   │       └── Success.vue
│   │
│   ├── stores/                   # Pinia state management
│   │   ├── auth.ts               # Authentication state
│   │   ├── course.ts             # Course catalog state
│   │   ├── enrollment.ts         # Enrollment state
│   │   ├── progress.ts           # Learning progress state
│   │   └── cart.ts               # Shopping cart state
│   │
│   ├── composables/              # Reusable Composition API logic
│   │   ├── useAuth.ts
│   │   ├── useCourses.ts
│   │   ├── useEnrollment.ts
│   │   ├── useVideoPlayer.ts
│   │   └── usePayment.ts
│   │
│   ├── services/                 # API clients
│   │   ├── api.ts                # Axios instance with auth interceptors
│   │   ├── courseService.ts
│   │   ├── enrollmentService.ts
│   │   ├── paymentService.ts
│   │   ├── reviewService.ts
│   │   └── analyticsService.ts
│   │
│   ├── router/                   # Vue Router
│   │   └── index.ts              # Route definitions with guards
│   │
│   ├── types/                    # TypeScript interfaces
│   │   ├── Course.ts
│   │   ├── User.ts
│   │   ├── Enrollment.ts
│   │   └── Review.ts
│   │
│   ├── utils/                    # Helper functions
│   │   ├── formatters.ts         # Date, currency formatting
│   │   ├── validators.ts         # Form validation
│   │   └── constants.ts          # App constants
│   │
│   ├── assets/                   # Static assets
│   │   ├── styles/
│   │   │   └── main.css          # Global styles
│   │   └── images/
│   │
│   ├── App.vue                   # Root component
│   └── main.ts                   # App entry point
│
├── tests/
│   ├── unit/                     # Component unit tests
│   │   ├── components/
│   │   │   ├── CourseCard.test.ts
│   │   │   ├── VideoPlayer.test.ts
│   │   │   └── EnrollmentButton.test.ts
│   │   └── stores/
│   │       ├── auth.test.ts
│   │       └── course.test.ts
│   │
│   └── e2e/                      # End-to-end tests (Dusk/Cypress)
│       ├── course-creation.spec.ts
│       ├── student-enrollment.spec.ts
│       └── payment-flow.spec.ts
│
├── public/
│   └── index.html
│
├── package.json                  # Node dependencies
├── vite.config.ts                # Vite configuration
├── tsconfig.json                 # TypeScript configuration
└── .env.example                  # Environment variables template

.github/
└── workflows/
    ├── backend-tests.yml         # CI/CD for Laravel tests
    ├── frontend-tests.yml        # CI/CD for Vue.js tests
    └── deploy.yml                # Deployment workflow

docker/                           # Docker configuration
├── Dockerfile.backend
├── Dockerfile.frontend
└── docker-compose.yml

.env.example                      # Environment variables
README.md                         # Project documentation
CONTRIBUTING.md                   # Contribution guidelines
```

**Structure Decision**: 

Web application architecture (Option 2) selected due to clear separation between backend API and frontend SPA as required by Constitution Principle I (API-First Architecture). This structure enables:

1. **Independent Development**: Backend and frontend teams can work in parallel on `/api/v1` contracts
2. **Independent Scaling**: Backend can scale horizontally, frontend served via CDN
3. **Mobile Readiness**: Same Laravel API can serve future mobile apps
4. **Clear Boundaries**: Business logic in backend services, UI logic in frontend components
5. **Technology Isolation**: Laravel ecosystem (backend), Vue.js ecosystem (frontend), connected via REST APIs

The structure follows Laravel 11 conventions with API controllers organized by domain (Instructor, Student, Payment) and Vue.js 3 best practices with Composition API, TypeScript, and Pinia for state management.

## Complexity Tracking

> **Fill ONLY if Constitution Check has violations that must be justified**

**No violations detected**. All constitution principles are followed:
- API-First Architecture maintained with Laravel/Vue.js separation
- Security built-in from day one (Sanctum, Policies, Form Requests)
- Test-first workflow with 80%/70% coverage targets
- Accessibility standards planned (WCAG 2.1 AA)
- Performance targets defined (<200ms API, 1000+ streams)
- Modular design with 9 independent user stories
- Documentation and UX prioritized (OpenAPI, in-app help)
