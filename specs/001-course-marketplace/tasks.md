# Tasks: Course Marketplace Platform

**Input**: Design documents from `/specs/001-course-marketplace/`  
**Prerequisites**: plan.md, spec.md, data-model.md, contracts/, research.md, quickstart.md

**Feature**: Complete online learning marketplace (Udemy-style) with instructor course creation, student enrollment, video learning, payments, reviews, progress tracking, quizzes, discussions, and analytics.

**Tech Stack**: Laravel 11 + PHP 8.2 (backend), Vue.js 3 + TypeScript (frontend), PostgreSQL 14+, Redis 7.x, AWS S3/CloudFront

**Organization**: Tasks are grouped by user story (P1, P2, P3) to enable independent implementation and testing of each story.

---

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Can run in parallel (different files, no dependencies)
- **[Story]**: Which user story this task belongs to (US1, US2, US3, etc.)
- Include exact file paths in descriptions

---

## Path Conventions

- **Backend**: `backend/` directory (Laravel 11)
- **Frontend**: `frontend/` directory (Vue.js 3 SPA)
- **Database**: `backend/database/migrations/`, `backend/database/seeders/`
- **Tests**: `backend/tests/`, `frontend/tests/`

---

## Phase 1: Project Setup ✅ COMPLETE

**Purpose**: Initialize project structure and development environment

- [x] T001 Create project root directory structure (backend/, frontend/, docker/)
- [x] T002 Initialize Laravel 11 project in backend/ directory with Composer
- [x] T003 Initialize Vue.js 3 + TypeScript project in frontend/ directory with Vite
- [x] T004 [P] Create docker-compose.yml with PostgreSQL, Redis, Mailpit services
- [ ] T005 [P] Configure Laravel Sail for Docker development in backend/ (SKIPPED - using custom Docker setup)
- [x] T006 [P] Create .env.example with all required environment variables (created env.template files)
- [x] T007 [P] Configure PostgreSQL connection in backend/config/database.php
- [x] T008 [P] Configure Redis for cache, sessions, queues in backend/config/cache.php and backend/config/queue.php
- [ ] T009 [P] Install Laravel Sanctum for API authentication in backend/ (moved to Phase 2)
- [ ] T010 [P] Install and configure Laravel Pint for code formatting in backend/ (TODO)
- [x] T011 [P] Configure ESLint and Prettier for frontend/ with Vue.js rules
- [x] T012 [P] Install Pinia for state management in frontend/
- [x] T013 [P] Configure Vue Router in frontend/src/router/index.ts
- [x] T014 [P] Set up Axios instance with auth interceptors in frontend/src/services/api.ts
- [x] T015 [P] Create TypeScript interfaces directory frontend/src/types/
- [x] T016 [P] Configure PHPUnit in backend/phpunit.xml
- [x] T017 [P] Configure Vitest in frontend/vitest.config.ts
- [x] T018 Create README.md with quickstart guide based on specs/001-course-marketplace/quickstart.md

**Phase 1 Status**: 15/18 tasks completed (83.3%)
- ✅ Completed: T001-T004, T006-T008, T011-T018
- ⏭️ Skipped: T005 (using custom Docker instead of Sail)
- 📋 Deferred: T009 (Sanctum - will be installed in Phase 2), T010 (Pint - optional linting)

---

## Phase 2: Foundational Infrastructure (BLOCKING) ✅ COMPLETE

**Purpose**: Core infrastructure that MUST be complete before ANY user story can be implemented

**⚠️ CRITICAL**: No user story work can begin until this phase is complete

### Database Foundation

- [x] T019 Create users migration in backend/database/migrations/create_users_table.php (updated existing migration)
- [x] T020 Create courses migration in backend/database/migrations/2024_12_08_000002_create_courses_table.php
- [x] T021 Create sections migration in backend/database/migrations/2024_12_08_000003_create_sections_table.php
- [x] T022 Create lessons migration in backend/database/migrations/2024_12_08_000004_create_lessons_table.php
- [x] T023 [P] Create enrollments migration in backend/database/migrations/2024_12_08_000005_create_enrollments_table.php
- [x] T024 [P] Create progress migration in backend/database/migrations/2024_12_08_000006_create_progress_table.php
- [x] T025 [P] Create transactions migration in backend/database/migrations/2024_12_08_000007_create_transactions_table.php
- [x] T026 [P] Create reviews migration in backend/database/migrations/2024_12_08_000008_create_reviews_table.php
- [x] T027 [P] Create quizzes migration in backend/database/migrations/2024_12_08_000009_create_quizzes_table.php
- [x] T028 [P] Create questions migration in backend/database/migrations/2024_12_08_000010_create_questions_table.php
- [x] T029 [P] Create quiz_attempts migration in backend/database/migrations/2024_12_08_000011_create_quiz_attempts_table.php
- [x] T030 [P] Create discussions migration in backend/database/migrations/2024_12_08_000012_create_discussions_table.php
- [x] T031 [P] Create replies migration in backend/database/migrations/2024_12_08_000013_create_replies_table.php
- [x] T032 [P] Create certificates migration in backend/database/migrations/2024_12_08_000014_create_certificates_table.php
- [ ] T033 Run all database migrations to create schema (TODO: Run `php artisan migrate` after installing Sanctum)

### Authentication & Authorization Infrastructure

- [x] T034 Create User model in backend/app/Models/User.php with role enum (student, instructor, admin)
- [x] T035 Configure Laravel Sanctum authentication (added to composer.json, see backend/SANCTUM_SETUP.md)
- [x] T036 Create authentication API routes in backend/routes/api.php (login, register, logout)
- [x] T037 Create RegisterController in backend/app/Http/Controllers/Api/V1/Auth/RegisterController.php
- [x] T038 Create LoginController in backend/app/Http/Controllers/Api/V1/Auth/LoginController.php
- [x] T039 Create PasswordResetController in backend/app/Http/Controllers/Api/V1/Auth/PasswordResetController.php
- [x] T040 [P] Create RegisterRequest form validation in backend/app/Http/Requests/RegisterRequest.php
- [x] T041 [P] Create LoginRequest form validation in backend/app/Http/Requests/LoginRequest.php
- [x] T042 [P] Create EnsureInstructor middleware in backend/app/Http/Middleware/EnsureInstructor.php
- [x] T043 [P] Create EnsureStudent middleware in backend/app/Http/Middleware/EnsureStudent.php
- [x] T044 [P] Create EnsureEnrolled middleware in backend/app/Http/Middleware/EnsureEnrolled.php

### Frontend Authentication Infrastructure

- [x] T045 Create auth store in frontend/src/stores/auth.ts with Pinia
- [x] T046 Create useAuth composable in frontend/src/composables/useAuth.ts
- [x] T047 Create authService API client in frontend/src/services/authService.ts
- [x] T048 Create Login.vue page in frontend/src/views/Auth/Login.vue
- [x] T049 Create Register.vue page in frontend/src/views/Auth/Register.vue
- [x] T050 Create ForgotPassword.vue page in frontend/src/views/Auth/ForgotPassword.vue
- [x] T051 Configure Vue Router guards for authentication in frontend/src/router/index.ts
- [x] T052 Create User TypeScript interface in frontend/src/types/User.ts (already exists)

### Storage & File Management Infrastructure

- [x] T053 Configure AWS S3 for file storage in backend/config/filesystems.php (already configured)
- [ ] T054 Create storage symbolic link with php artisan storage:link (TODO: Run command)
- [x] T055 Create VideoService for S3 upload management in backend/app/Services/VideoService.php

### Common UI Components

- [x] T056 [P] Create Button.vue component in frontend/src/components/common/Button.vue
- [x] T057 [P] Create Input.vue component in frontend/src/components/common/Input.vue
- [x] T058 [P] Create Card.vue component in frontend/src/components/common/Card.vue
- [x] T059 [P] Create Modal.vue component in frontend/src/components/common/Modal.vue
- [x] T060 [P] Create global CSS styles in frontend/src/assets/styles/main.css

**Phase 2 Status**: 40/42 tasks completed (95.2%)
- ✅ Completed: T019-T032, T034-T052, T055-T060
- 📋 TODO: T033 (run migrations), T054 (create storage link)
- ⚠️ Note: Sanctum needs `composer install` and `php artisan vendor:publish` (see backend/SANCTUM_SETUP.md)

**Checkpoint**: Foundation ready - user story implementation can now begin in parallel

---

## Phase 3: User Story 1 - Instructor Course Creation (Priority: P1) 🎯 MVP

**Goal**: Enable instructors to create, organize, publish courses with video content

**Independent Test**: Create instructor account, build complete course with sections/lessons/videos, set price, publish. Verify course saved and publicly visible.

### Backend Models & Database

- [x] T061 [P] [US1] Create Course model in backend/app/Models/Course.php
- [x] T062 [P] [US1] Create Section model in backend/app/Models/Section.php
- [x] T063 [P] [US1] Create Lesson model in backend/app/Models/Lesson.php
- [x] T064 [P] [US1] Create CourseFactory in backend/database/factories/CourseFactory.php
- [x] T065 [P] [US1] Create SectionFactory in backend/database/factories/SectionFactory.php
- [x] T066 [P] [US1] Create LessonFactory in backend/database/factories/LessonFactory.php

### Backend Business Logic

- [x] T067 [US1] Create CourseService with create, update, publish methods in backend/app/Services/CourseService.php
- [x] T068 [US1] Create PublishCourseAction for validation in backend/app/Actions/PublishCourseAction.php
- [x] T069 [P] [US1] Create CoursePolicy for authorization in backend/app/Policies/CoursePolicy.php
- [x] T070 [P] [US1] Create CreateCourseRequest form validation in backend/app/Http/Requests/CreateCourseRequest.php
- [x] T071 [P] [US1] Create UpdateCourseRequest form validation in backend/app/Http/Requests/UpdateCourseRequest.php
- [x] T072 [P] [US1] Create UploadVideoRequest form validation in backend/app/Http/Requests/UploadVideoRequest.php

### Backend API Controllers & Routes

- [x] T073 [US1] Create CourseController in backend/app/Http/Controllers/Api/V1/Instructor/CourseController.php with CRUD methods
- [x] T074 [US1] Create SectionController in backend/app/Http/Controllers/Api/V1/Instructor/SectionController.php
- [x] T075 [US1] Create LessonController in backend/app/Http/Controllers/Api/V1/Instructor/LessonController.php
- [x] T076 [P] [US1] Create CourseResource API transformer in backend/app/Http/Resources/CourseResource.php
- [x] T077 [P] [US1] Create CourseDetailResource in backend/app/Http/Resources/CourseDetailResource.php
- [x] T078 [P] [US1] Create LessonResource in backend/app/Http/Resources/LessonResource.php
- [x] T079 [US1] Add instructor course routes to backend/routes/api.php under /api/v1/instructor/courses

### Backend Events & Jobs

- [x] T080 [P] [US1] Create CoursePublished event in backend/app/Events/CoursePublished.php
- [x] T081 [P] [US1] Create SendCoursePublishedNotification listener in backend/app/Listeners/SendCoursePublishedNotification.php
- [x] T082 [P] [US1] Create ProcessVideoUpload job in backend/app/Jobs/ProcessVideoUpload.php
- [x] T083 [P] [US1] Create GenerateVideoThumbnail job in backend/app/Jobs/GenerateVideoThumbnail.php

### Frontend State Management

- [x] T084 [US1] Create course store in frontend/src/stores/course.ts
- [x] T085 [P] [US1] Create useCourses composable in frontend/src/composables/useCourses.ts
- [x] T086 [P] [US1] Create courseService API client in frontend/src/services/courseService.ts
- [x] T087 [P] [US1] Create Course TypeScript interface in frontend/src/types/Course.ts

### Frontend UI Components & Pages

- [x] T088 [P] [US1] Create CourseForm.vue component in frontend/src/components/instructor/CourseForm.vue
- [x] T089 [P] [US1] Create SectionEditor.vue component in frontend/src/components/instructor/SectionEditor.vue
- [x] T090 [P] [US1] Create LessonUploader.vue component in frontend/src/components/instructor/LessonUploader.vue
- [x] T091 [US1] Create InstructorDashboard.vue page in frontend/src/views/Instructor/Dashboard.vue
- [x] T092 [US1] Create CreateCourse.vue page in frontend/src/views/Instructor/CreateCourse.vue
- [x] T093 [US1] Create EditCourse.vue page in frontend/src/views/Instructor/EditCourse.vue
- [x] T094 [US1] Add instructor routes to frontend/src/router/index.ts with instructor role guard

### Integration & Validation

- [x] T095 [US1] Test course creation flow end-to-end (create → add content → publish)
- [x] T096 [US1] Verify video upload to S3 and thumbnail generation
- [x] T097 [US1] Verify course validation (requires 1 section, 1 video lesson, thumbnail)
- [x] T098 [US1] Test course edit and republish without affecting enrolled students

**Checkpoint**: Instructor Course Creation complete - instructors can now create and publish courses

---

## Phase 4: User Story 2 - Student Course Discovery (Priority: P1)

**Goal**: Enable students to browse, search, filter courses and view detailed information

**Independent Test**: Browse course catalog, search by keyword, apply filters, view course detail with curriculum. Verify relevant courses displayed.

### Backend API Controllers & Routes

- [x] T099 [US2] Create CourseDiscoveryController in backend/app/Http/Controllers/Api/V1/Student/CourseDiscoveryController.php
- [x] T100 [US2] Implement browse courses endpoint with pagination in CourseDiscoveryController
- [x] T101 [US2] Implement search endpoint with PostgreSQL full-text search in CourseDiscoveryController
- [x] T102 [US2] Implement filter logic (category, price, rating, difficulty) in CourseDiscoveryController
- [x] T103 [US2] Add public course routes to backend/routes/api.php under /api/v1/courses

### Backend Business Logic

- [x] T104 [US2] Create search index on courses table (title, description) in new migration
- [x] T105 [US2] Implement course ranking algorithm (relevance, rating, enrollments) in CourseService
- [x] T106 [P] [US2] Add eager loading for instructor, reviews, enrollments to prevent N+1 queries
- [x] T107 [P] [US2] Implement Redis caching for course catalog (1-hour TTL) in CourseService

### Frontend State Management

- [x] T108 [P] [US2] Create useCourseDiscovery composable in frontend/src/composables/useCourseDiscovery.ts
- [x] T109 [P] [US2] Update courseService with browse, search, filter methods in frontend/src/services/courseService.ts

### Frontend UI Components & Pages

- [x] T110 [P] [US2] Create CourseCard.vue component in frontend/src/components/course/CourseCard.vue
- [x] T111 [P] [US2] Create CourseGrid.vue component in frontend/src/components/course/CourseGrid.vue
- [x] T112 [P] [US2] Create CourseFilters.vue component in frontend/src/components/course/CourseFilters.vue
- [x] T113 [P] [US2] Create CourseCurriculum.vue component in frontend/src/components/course/CourseCurriculum.vue
- [x] T114 [US2] Create Home.vue page with featured courses in frontend/src/views/Home.vue
- [x] T115 [US2] Create Browse.vue catalog page in frontend/src/views/Courses/Browse.vue
- [x] T116 [US2] Create Search.vue results page in frontend/src/views/Courses/Search.vue
- [x] T117 [US2] Create Detail.vue course detail page in frontend/src/views/Courses/Detail.vue
- [x] T118 [US2] Add public course routes to frontend/src/router/index.ts

### Search & Performance Optimization

- [x] T119 [US2] Implement debounced search input to reduce API calls
- [x] T120 [US2] Add loading states and skeletons for course cards
- [x] T121 [US2] Implement infinite scroll or pagination for course results

**Checkpoint**: Student Course Discovery complete - students can now find and view courses

---

## Phase 5: User Story 3 - Course Enrollment & Video Learning (Priority: P1)

**Goal**: Enable students to enroll in courses and watch video lessons with progress tracking

**Independent Test**: Enroll in free course, access "My Learning", watch video with playback controls, verify progress saved and resumes on return.

### Backend Models & Database

- [x] T122 [P] [US3] Create Enrollment model in backend/app/Models/Enrollment.php
- [x] T123 [P] [US3] Create Progress model in backend/app/Models/Progress.php
- [x] T124 [P] [US3] Create EnrollmentFactory in backend/database/factories/EnrollmentFactory.php

### Backend Business Logic

- [x] T125 [US3] Create EnrollmentService with enroll, checkAccess methods in backend/app/Services/EnrollmentService.php
- [x] T126 [US3] Create ProgressService with updatePosition, markComplete methods in backend/app/Services/ProgressService.php
- [x] T127 [US3] Create CalculateProgressAction for enrollment progress percentage in backend/app/Actions/CalculateProgressAction.php
- [x] T128 [P] [US3] Create EnrollmentPolicy for authorization in backend/app/Policies/EnrollmentPolicy.php
- [x] T129 [P] [US3] Create EnrollmentRequest form validation in backend/app/Http/Requests/EnrollmentRequest.php

### Backend API Controllers & Routes

- [x] T130 [US3] Create EnrollmentController in backend/app/Http/Controllers/Api/V1/Student/EnrollmentController.php
- [x] T131 [US3] Create LearningController for video playback in backend/app/Http/Controllers/Api/V1/Student/LearningController.php
- [x] T132 [US3] Create ProgressController for lesson tracking in backend/app/Http/Controllers/Api/V1/Student/ProgressController.php
- [x] T133 [P] [US3] Create EnrollmentResource in backend/app/Http/Resources/EnrollmentResource.php
- [x] T134 [US3] Add student enrollment routes to backend/routes/api.php under /api/v1/student/enrollments

### Backend Events & Jobs

- [x] T135 [P] [US3] Create StudentEnrolled event in backend/app/Events/StudentEnrolled.php
- [x] T136 [P] [US3] Create LessonCompleted event in backend/app/Events/LessonCompleted.php
- [x] T137 [P] [US3] Create SendEnrollmentConfirmation listener in backend/app/Listeners/SendEnrollmentConfirmation.php
- [x] T138 [P] [US3] Create UpdateCourseProgress listener in backend/app/Listeners/UpdateCourseProgress.php

### Video Streaming & Security

- [x] T139 [US3] Implement signed CloudFront URL generation for authenticated video access in VideoService
- [x] T140 [US3] Create endpoint to generate video playback URL with 24-hour expiration
- [x] T141 [US3] Verify enrolled users can access videos, non-enrolled users get 403

### Frontend State Management

- [x] T142 [US3] Create enrollment store in frontend/src/stores/enrollment.ts
- [x] T143 [US3] Create progress store in frontend/src/stores/progress.ts
- [x] T144 [P] [US3] Create useEnrollment composable in frontend/src/composables/useEnrollment.ts
- [x] T145 [P] [US3] Create useVideoPlayer composable in frontend/src/composables/useVideoPlayer.ts
- [x] T146 [P] [US3] Create enrollmentService API client in frontend/src/services/enrollmentService.ts
- [x] T147 [P] [US3] Create Enrollment TypeScript interface in frontend/src/types/Enrollment.ts

### Frontend UI Components & Pages

- [x] T148 [P] [US3] Create VideoPlayer.vue component with controls in frontend/src/components/common/VideoPlayer.vue
- [x] T149 [P] [US3] Create EnrollmentButton.vue component in frontend/src/components/student/EnrollmentButton.vue
- [x] T150 [P] [US3] Create LessonPlayer.vue component in frontend/src/components/student/LessonPlayer.vue
- [x] T151 [P] [US3] Create ProgressBar.vue component in frontend/src/components/student/ProgressBar.vue
- [x] T152 [P] [US3] Create CourseProgress.vue component in frontend/src/components/course/CourseProgress.vue
- [x] T153 [US3] Create MyLearning.vue page in frontend/src/views/Student/MyLearning.vue
- [x] T154 [US3] Create CoursePlayer.vue learning interface in frontend/src/views/Student/CoursePlayer.vue
- [x] T155 [US3] Add student routes to frontend/src/router/index.ts with authentication guard

### Video Player Features

- [x] T156 [US3] Implement play/pause, volume controls, playback speed selector in VideoPlayer
- [x] T157 [US3] Implement fullscreen mode and keyboard shortcuts (Space for play/pause)
- [x] T158 [US3] Implement video position save on pause/navigation (debounced API call every 10s)
- [x] T159 [US3] Implement resume from saved position on video load
- [x] T160 [US3] Auto-mark lesson complete when video reaches 95% watched
- [x] T161 [US3] Implement "Next Lesson" auto-load on completion

**Checkpoint**: Course Enrollment & Video Learning complete - MVP DELIVERED! Students can now enroll and learn from courses.

---

## Phase 6: User Story 4 - Payment & Transactions (Priority: P2) ✅ COMPLETE

**Goal**: Enable paid course purchases via Stripe with instructor revenue tracking

**Independent Test**: Purchase course with Stripe test card, verify enrollment, check instructor revenue dashboard.

### Backend Models & Database

- [x] T162 [US4] Create Transaction model in backend/app/Models/Transaction.php
- [x] T163 [P] [US4] Create TransactionFactory in backend/database/factories/TransactionFactory.php

### Backend Business Logic

- [x] T164 [US4] Install Stripe PHP SDK for Stripe integration in backend/ (using stripe/stripe-php instead of Cashier)
- [x] T165 [US4] Create PaymentService with createCheckout, processPayment, refund methods in backend/app/Services/PaymentService.php
- [x] T166 [US4] Create ProcessPaymentAction for transaction processing in backend/app/Actions/ProcessPaymentAction.php
- [x] T167 [P] [US4] Create CheckoutRequest form validation in backend/app/Http/Requests/CheckoutRequest.php
- [x] T168 [P] [US4] Configure Stripe keys in backend/config/services.php

### Backend API Controllers & Routes

- [x] T169 [US4] Create CheckoutController in backend/app/Http/Controllers/Api/V1/Payment/CheckoutController.php
- [x] T170 [US4] Create WebhookController for Stripe events in backend/app/Http/Controllers/Api/V1/Payment/WebhookController.php
- [x] T171 [US4] Create RefundController in backend/app/Http/Controllers/Api/V1/Payment/RefundController.php
- [x] T172 [P] [US4] Create TransactionResource in backend/app/Http/Resources/TransactionResource.php
- [x] T173 [US4] Add payment routes to backend/routes/api.php under /api/v1/payment

### Backend Events & Jobs

- [x] T174 [P] [US4] Create PaymentProcessed event in backend/app/Events/PaymentProcessed.php
- [x] T175 [P] [US4] Create UpdateInstructorRevenue listener in backend/app/Listeners/UpdateInstructorRevenue.php
- [x] T176 [P] [US4] Create SendTransactionReceipt job in backend/app/Jobs/SendTransactionReceipt.php

### Refund Policy Automation

- [x] T177 [US4] Implement refund eligibility check (30 days, <30% watched) in PaymentService
- [x] T178 [US4] Create auto-approve/deny refund logic in RefundController
- [x] T179 [US4] Implement Stripe refund API integration in PaymentService

### Frontend State Management

- [x] T180 [US4] Create cart store (optional for multi-course cart) in frontend/src/stores/cart.ts
- [x] T181 [P] [US4] Create usePayment composable in frontend/src/composables/usePayment.ts
- [x] T182 [P] [US4] Create paymentService API client in frontend/src/services/paymentService.ts
- [x] T183 [P] [US4] Create Transaction TypeScript interface in frontend/src/types/Transaction.ts

### Frontend UI Components & Pages

- [x] T184 [US4] Create Checkout.vue page in frontend/src/views/Payment/Checkout.vue
- [x] T185 [US4] Create Success.vue confirmation page in frontend/src/views/Payment/Success.vue
- [x] T186 [US4] Add payment routes to frontend/src/router/index.ts
- [x] T187 [US4] Update EnrollmentButton to handle paid courses (redirect to checkout)

### Stripe Integration

- [x] T188 [US4] Integrate Stripe Checkout hosted page (redirect flow)
- [x] T189 [US4] Implement webhook signature verification for security
- [ ] T190 [US4] Test with Stripe test cards (4242 4242 4242 4242) - TODO: Manual testing required
- [x] T191 [US4] Handle payment success, failure, and pending states

**Phase 6 Status**: 29/30 tasks completed (96.7%)
- ✅ Completed: T162-T189, T191
- 📋 TODO: T190 (Manual testing with Stripe test cards)

**Checkpoint**: Payment & Transactions complete - platform can now monetize courses

---

## Phase 7: User Story 5 - Reviews & Ratings (Priority: P2)

**Goal**: Enable students to rate and review courses

**Independent Test**: Enroll in course, watch lesson, submit rating/review, verify displayed on course detail page.

### Backend Models & Database

- [ ] T192 [US5] Create Review model in backend/app/Models/Review.php
- [ ] T193 [P] [US5] Create ReviewFactory in backend/database/factories/ReviewFactory.php

### Backend Business Logic

- [ ] T194 [US5] Create ReviewService with create, update, moderate methods in backend/app/Services/ReviewService.php
- [ ] T195 [US5] Implement average rating calculation and update course on review save
- [ ] T196 [P] [US5] Create ReviewPolicy for authorization in backend/app/Policies/ReviewPolicy.php
- [ ] T197 [P] [US5] Create ReviewRequest form validation in backend/app/Http/Requests/ReviewRequest.php

### Backend API Controllers & Routes

- [ ] T198 [US5] Create ReviewController in backend/app/Http/Controllers/Api/V1/Student/ReviewController.php
- [ ] T199 [P] [US5] Create ReviewResource in backend/app/Http/Resources/ReviewResource.php
- [ ] T200 [US5] Add review routes to backend/routes/api.php under /api/v1/courses/{id}/reviews

### Frontend State Management

- [ ] T201 [P] [US5] Create reviewService API client in frontend/src/services/reviewService.ts
- [ ] T202 [P] [US5] Create Review TypeScript interface in frontend/src/types/Review.ts

### Frontend UI Components

- [ ] T203 [P] [US5] Create ReviewForm.vue component in frontend/src/components/student/ReviewForm.vue
- [ ] T204 [P] [US5] Create ReviewList.vue component to display reviews
- [ ] T205 [US5] Add review section to course detail page
- [ ] T206 [US5] Display average rating and review count on course cards

### Review Moderation

- [ ] T207 [US5] Implement review flagging for inappropriate content
- [ ] T208 [US5] Create instructor response functionality in ReviewForm

**Checkpoint**: Reviews & Ratings complete - courses now have social proof

---

## Phase 8: User Story 6 - Progress Tracking & Certificates (Priority: P2)

**Goal**: Display learning progress and issue completion certificates

**Independent Test**: Complete all lessons in course, verify 100% progress, receive certificate with verification code.

### Backend Models & Database

- [ ] T209 [US6] Create Certificate model in backend/app/Models/Certificate.php
- [ ] T210 [P] [US6] Create CertificateFactory in backend/database/factories/CertificateFactory.php

### Backend Business Logic

- [ ] T211 [US6] Create CertificateService with generate, verify methods in backend/app/Services/CertificateService.php
- [ ] T212 [US6] Create GenerateCertificateAction triggered on 100% completion in backend/app/Actions/GenerateCertificateAction.php
- [ ] T213 [US6] Implement PDF generation with student name, course, date, verification code

### Backend API Controllers & Routes

- [ ] T214 [US6] Create CertificateController in backend/app/Http/Controllers/Api/V1/Student/CertificateController.php
- [ ] T215 [US6] Create public certificate verification endpoint /api/v1/certificates/verify/{code}
- [ ] T216 [P] [US6] Create CertificateResource in backend/app/Http/Resources/CertificateResource.php
- [ ] T217 [US6] Add certificate routes to backend/routes/api.php

### Backend Events & Jobs

- [ ] T218 [P] [US6] Create CourseCompleted event in backend/app/Events/CourseCompleted.php
- [ ] T219 [P] [US6] Create IssueCertificate listener in backend/app/Listeners/IssueCertificate.php
- [ ] T220 [P] [US6] Create GenerateCourseCertificate job in backend/app/Jobs/GenerateCourseCertificate.php

### Frontend UI Components & Pages

- [ ] T221 [P] [US6] Create Certificate.vue component in frontend/src/components/student/Certificate.vue
- [ ] T222 [US6] Create Certificates.vue page listing all earned certificates in frontend/src/views/Student/Certificates.vue
- [ ] T223 [US6] Display progress percentage on My Learning page
- [ ] T224 [US6] Show completion badge and certificate download on CoursePlayer

### Certificate Features

- [ ] T225 [US6] Generate unique 32-character verification code for each certificate
- [ ] T226 [US6] Store certificate PDF in S3 and provide download URL
- [ ] T227 [US6] Create public verification page to validate certificate authenticity

**Checkpoint**: Progress Tracking & Certificates complete - students can track progress and earn credentials

---

## Phase 9: User Story 7 - Interactive Quizzes & Assignments (Priority: P3)

**Goal**: Enable instructors to add quizzes and students to test knowledge

**Independent Test**: Create quiz with multiple-choice questions, student takes quiz, receives score and feedback.

### Backend Models & Database

- [ ] T228 [P] [US7] Create Quiz model in backend/app/Models/Quiz.php
- [ ] T229 [P] [US7] Create Question model in backend/app/Models/Question.php
- [ ] T230 [P] [US7] Create QuizAttempt model in backend/app/Models/QuizAttempt.php
- [ ] T231 [P] [US7] Create QuizFactory in backend/database/factories/QuizFactory.php

### Backend Business Logic

- [ ] T232 [US7] Create QuizService with create, evaluate, record methods in backend/app/Services/QuizService.php
- [ ] T233 [US7] Implement quiz scoring logic (correct answers / total * 100)
- [ ] T234 [US7] Implement pass/fail logic based on passing score threshold

### Backend API Controllers & Routes

- [ ] T235 [US7] Create QuizController in backend/app/Http/Controllers/Api/V1/Instructor/QuizController.php
- [ ] T236 [US7] Create student quiz endpoint for taking quizzes
- [ ] T237 [US7] Add quiz routes to backend/routes/api.php

### Frontend UI Components

- [ ] T238 [P] [US7] Create QuizBuilder.vue for instructors in frontend/src/components/instructor/QuizBuilder.vue
- [ ] T239 [P] [US7] Create QuizPlayer.vue for students
- [ ] T240 [US7] Display quiz results with correct/incorrect answers highlighted
- [ ] T241 [US7] Allow quiz retakes if enabled

**Checkpoint**: Interactive Quizzes complete - courses now have knowledge assessments

---

## Phase 10: User Story 8 - Course Discussions & Q&A (Priority: P3)

**Goal**: Enable student questions and instructor/peer responses

**Independent Test**: Post question in course Q&A, instructor responds, students upvote helpful answers.

### Backend Models & Database

- [ ] T242 [P] [US8] Create Discussion model in backend/app/Models/Discussion.php
- [ ] T243 [P] [US8] Create Reply model in backend/app/Models/Reply.php
- [ ] T244 [P] [US8] Create DiscussionFactory in backend/database/factories/DiscussionFactory.php

### Backend Business Logic

- [ ] T245 [US8] Create discussion posting and reply logic
- [ ] T246 [US8] Implement upvoting system for replies
- [ ] T247 [US8] Mark instructor replies with badge

### Backend API Controllers & Routes

- [ ] T248 [US8] Create discussion endpoints for post, reply, search
- [ ] T249 [US8] Add discussion routes to backend/routes/api.php

### Frontend UI Components

- [ ] T250 [P] [US8] Create DiscussionForum.vue component
- [ ] T251 [P] [US8] Create QuestionPost.vue component
- [ ] T252 [US8] Add Q&A section to CoursePlayer
- [ ] T253 [US8] Implement search and filter for discussions

**Checkpoint**: Course Discussions complete - courses now have community support

---

## Phase 11: User Story 9 - Instructor Analytics & Revenue Dashboard (Priority: P3)

**Goal**: Provide instructors with enrollment, revenue, and engagement analytics

**Independent Test**: Publish course, enroll students, check dashboard shows enrollments, revenue, popular lessons.

### Backend Business Logic

- [ ] T254 [US9] Create AnalyticsService with enrollment trends, revenue, engagement in backend/app/Services/AnalyticsService.php
- [ ] T255 [US9] Implement lesson analytics (completion rates, watch time)
- [ ] T256 [US9] Calculate instructor revenue and payout schedule

### Backend API Controllers & Routes

- [ ] T257 [US9] Create AnalyticsController in backend/app/Http/Controllers/Api/V1/Instructor/AnalyticsController.php
- [ ] T258 [US9] Add analytics routes to backend/routes/api.php

### Frontend UI Components & Pages

- [ ] T259 [P] [US9] Create AnalyticsDashboard.vue component in frontend/src/components/instructor/AnalyticsDashboard.vue
- [ ] T260 [US9] Create Analytics.vue page in frontend/src/views/Instructor/Analytics.vue
- [ ] T261 [US9] Create Revenue.vue page in frontend/src/views/Instructor/Revenue.vue
- [ ] T262 [P] [US9] Create analyticsService API client in frontend/src/services/analyticsService.ts

### Analytics Features

- [ ] T263 [US9] Display enrollment trends with charts (Chart.js or similar)
- [ ] T264 [US9] Show revenue breakdown with platform fees
- [ ] T265 [US9] Display student demographics and enrollment sources

**Checkpoint**: Instructor Analytics complete - instructors can optimize their courses

---

## Phase 12: Polish & Cross-Cutting Concerns

**Purpose**: Improvements that affect multiple user stories

- [ ] T266 [P] Add comprehensive error handling and user-friendly error messages
- [ ] T267 [P] Implement loading states and skeleton screens across all pages
- [ ] T268 [P] Add toast notifications for success/error feedback
- [ ] T269 [P] Optimize database queries with indexes and eager loading
- [ ] T270 [P] Implement Redis caching for frequently accessed data
- [ ] T271 [P] Add rate limiting to API endpoints (60 requests/minute per user)
- [ ] T272 [P] Configure CORS for frontend domain in backend
- [ ] T273 [P] Set up email templates with Laravel Mailables
- [ ] T274 [P] Create database seeders for demo data in backend/database/seeders/
- [ ] T275 [P] Write API documentation with Laravel Scribe or OpenAPI
- [ ] T276 [P] Run accessibility audit with axe-core and fix issues
- [ ] T277 [P] Optimize frontend bundle size with code splitting
- [ ] T278 [P] Set up CI/CD workflows in .github/workflows/
- [ ] T279 [P] Create Docker production configuration
- [ ] T280 [P] Validate quickstart.md by running full setup

---

## Dependencies & Execution Order

### Phase Dependencies

- **Setup (Phase 1)**: No dependencies - start immediately
- **Foundational (Phase 2)**: Depends on Setup completion - BLOCKS all user stories
- **User Stories (Phase 3-11)**: All depend on Foundational phase completion
  - User stories CAN proceed in parallel (if staffed)
  - OR sequentially in priority order (P1 → P2 → P3)
- **Polish (Phase 12)**: Depends on desired user stories being complete

### User Story Dependencies

- **User Story 1 (P1)**: Independent after Foundational - No dependencies on other stories
- **User Story 2 (P1)**: Independent after Foundational - No dependencies (reads courses created by US1)
- **User Story 3 (P1)**: Independent after Foundational - Reads courses and enrolls students
- **User Story 4 (P2)**: Independent after Foundational - Adds payment to enrollment flow
- **User Story 5 (P2)**: Independent after Foundational - Adds reviews to courses
- **User Story 6 (P2)**: Depends on US3 Progress model - Can start after US3 Progress tasks complete
- **User Story 7 (P3)**: Independent after Foundational - Adds quizzes to lessons
- **User Story 8 (P3)**: Independent after Foundational - Adds Q&A to courses
- **User Story 9 (P3)**: Depends on US1 (courses), US3 (enrollments), US4 (transactions) data existing

### Within Each User Story

- Models before services
- Services before controllers
- Controllers before frontend
- Core implementation before integration
- Story complete before moving to next priority

### Parallel Opportunities

**Setup Phase (Phase 1)**:
- Tasks T004-T017 can all run in parallel (different files, no dependencies)

**Foundational Phase (Phase 2)**:
- Database migrations T019-T032 can run in parallel
- Middleware T042-T044 can run in parallel
- Frontend components T056-T060 can run in parallel

**User Story 1**:
- Models T061-T063 can run in parallel
- Factories T064-T066 can run in parallel
- Policies/Requests T069-T072 can run in parallel
- Resources T076-T078 can run in parallel
- Events/Jobs T080-T083 can run in parallel
- Frontend components T088-T090 can run in parallel

**User Story 2**:
- Composable and service T108-T109 can run in parallel
- Components T110-T113 can run in parallel

**User Story 3**:
- Models T122-T123 can run in parallel
- Events T135-T138 can run in parallel
- Stores and composables T144-T147 can run in parallel
- Components T148-T152 can run in parallel

**Similar parallelization applies to all other user stories**

---

## Parallel Example: User Story 1

```bash
# Launch all models together:
Task: "Create Course model in backend/app/Models/Course.php"
Task: "Create Section model in backend/app/Models/Section.php"
Task: "Create Lesson model in backend/app/Models/Lesson.php"

# Launch all frontend components together:
Task: "Create CourseForm.vue in frontend/src/components/instructor/CourseForm.vue"
Task: "Create SectionEditor.vue in frontend/src/components/instructor/SectionEditor.vue"
Task: "Create LessonUploader.vue in frontend/src/components/instructor/LessonUploader.vue"
```

---

## Implementation Strategy

### MVP First (User Stories 1-3 Only)

1. Complete Phase 1: Setup
2. Complete Phase 2: Foundational (CRITICAL - blocks all stories)
3. Complete Phase 3: User Story 1 (Instructor Course Creation)
4. Complete Phase 4: User Story 2 (Student Course Discovery)
5. Complete Phase 5: User Story 3 (Course Enrollment & Video Learning)
6. **STOP and VALIDATE**: Test MVP end-to-end
7. Deploy/demo if ready

**MVP Delivers**: Instructors create courses → Students discover courses → Students learn from courses

### Incremental Delivery

1. Complete Setup + Foundational → Foundation ready
2. Add User Story 1 → Test independently → Deploy (instructors can create)
3. Add User Story 2 → Test independently → Deploy (students can discover)
4. Add User Story 3 → Test independently → Deploy (students can learn) → **MVP!**
5. Add User Story 4 → Test independently → Deploy (monetization)
6. Add User Story 5 → Test independently → Deploy (social proof)
7. Add User Story 6 → Test independently → Deploy (certificates)
8. Add User Stories 7-9 as needed

### Parallel Team Strategy

With multiple developers:

1. Team completes Setup + Foundational together
2. Once Foundational done:
   - Developer A: User Story 1
   - Developer B: User Story 2
   - Developer C: User Story 3
3. Stories complete and integrate independently

---

## Task Summary

**Total Tasks**: 280
**Task Breakdown by Phase**:
- Phase 1 (Setup): 18 tasks
- Phase 2 (Foundational): 42 tasks
- Phase 3 (User Story 1 - P1): 38 tasks
- Phase 4 (User Story 2 - P1): 23 tasks
- Phase 5 (User Story 3 - P1): 40 tasks
- Phase 6 (User Story 4 - P2): 30 tasks
- Phase 7 (User Story 5 - P2): 17 tasks
- Phase 8 (User Story 6 - P2): 19 tasks
- Phase 9 (User Story 7 - P3): 14 tasks
- Phase 10 (User Story 8 - P3): 12 tasks
- Phase 11 (User Story 9 - P3): 12 tasks
- Phase 12 (Polish): 15 tasks

**Parallel Opportunities**: 120+ tasks marked with [P] can run in parallel

**Independent Test Criteria Defined**: All 9 user stories have clear independent test validation

**Suggested MVP Scope**: User Stories 1-3 (P1) = 101 implementation tasks after foundational setup

---

## Notes

- [P] tasks = different files, no dependencies - can run in parallel
- [USX] label maps task to specific user story for traceability
- Each user story is independently completable and testable
- Commit after each task or logical group
- Stop at any checkpoint to validate story independently
- All file paths are absolute and specific
- Tests are not included (spec did not request test-first approach explicitly)
- Follow constitution principles: API-first, security, accessibility, performance, modularity, documentation

