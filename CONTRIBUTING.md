# Contributing to Online Learning Platform

Thank you for your interest in contributing! This guide will help you get started while ensuring your contributions align with our development standards.

## 📜 First: Read the Constitution

**REQUIRED**: Before contributing, read our [Constitution](.specify/memory/constitution.md). It defines the non-negotiable principles that govern all development work.

**Key Principles**:
1. API-First Architecture (Laravel backend, Vue.js frontend)
2. Security & Data Protection (FERPA/GDPR compliance)
3. Test-First Development (NON-NEGOTIABLE)
4. Accessibility Standards (WCAG 2.1 AA)
5. Performance & Scalability
6. Modular Feature Design
7. Documentation & User Experience

## 🚀 Getting Started

### 1. Set Up Development Environment

Follow the [README Quickstart](README.md#-quickstart--10-minutes) to set up your local environment.

### 2. Understand the Workflow

We use a specification-driven development workflow:

```
Specification → Planning → Tasks → Implementation → Testing → Review → Merge
```

## 📝 Contribution Workflow

### Step 1: Create a Feature Specification

**For new features**, create a specification in `specs/[###-feature-name]/spec.md`:

```bash
# Use the speckit.specify command in Cursor
/speckit.specify [Your feature description]
```

**Specification must include**:
- User scenarios with priorities (P1, P2, P3)
- Acceptance criteria (Given/When/Then format)
- Functional requirements (FR-001, FR-002, etc.)
- Success criteria (measurable outcomes)
- Edge cases and error scenarios

### Step 2: Generate Implementation Plan

```bash
# Use the speckit.plan command
/speckit.plan [Reference to your spec]
```

The plan will include:
- **Constitution Check** - Verify compliance with all 7 principles
- Technical context (Laravel, Vue.js, PostgreSQL, Redis)
- Project structure
- Complexity tracking (if any principles violated)

**Constitution Check Example**:
```markdown
## Constitution Check

- [x] I. API-First Architecture - API endpoints versioned at /api/v1/courses
- [x] II. Security & Data Protection - RBAC with policies, Form Request validation
- [x] III. Test-First Development - Acceptance tests written first
- [x] IV. Accessibility - WCAG 2.1 AA checked with axe-core
- [x] V. Performance - Pagination for course lists, eager loading relationships
- [x] VI. Modular Design - Course module independent, uses events for notifications
- [x] VII. Documentation - API docs generated, user tooltips included

Violations Requiring Justification: None
```

### Step 3: Generate Task Breakdown

```bash
# Use the speckit.tasks command
/speckit.tasks
```

Tasks will be organized by:
- **Setup** - Project initialization
- **Foundational** - Blocking infrastructure (MUST complete before user stories)
- **User Stories** - By priority (P1, P2, P3)
- **Polish** - Cross-cutting concerns

### Step 4: Implement with Test-First Approach

**Backend (Laravel)**:

```bash
# 1. Write test FIRST
# tests/Feature/CourseControllerTest.php
public function test_user_can_create_course()
{
    $user = User::factory()->create();
    $response = $this->actingAs($user)
        ->postJson('/api/v1/courses', [
            'title' => 'Introduction to Laravel',
            'description' => 'Learn Laravel basics'
        ]);
    
    $response->assertStatus(201)
        ->assertJsonStructure(['data' => ['id', 'title', 'description']]);
}

# 2. Run test - should FAIL
php artisan test

# 3. Implement feature
# app/Http/Controllers/Api/V1/CourseController.php
# app/Services/CourseService.php
# app/Http/Requests/CreateCourseRequest.php

# 4. Run test - should PASS
php artisan test

# 5. Refactor if needed (tests still pass)
```

**Frontend (Vue.js)**:

```bash
# 1. Write test FIRST
# tests/unit/CourseForm.test.ts
describe('CourseForm', () => {
  it('submits course data when form is valid', async () => {
    const wrapper = mount(CourseForm);
    await wrapper.find('input[name="title"]').setValue('Test Course');
    await wrapper.find('textarea[name="description"]').setValue('Description');
    await wrapper.find('form').trigger('submit');
    
    expect(courseService.create).toHaveBeenCalledWith({
      title: 'Test Course',
      description: 'Description'
    });
  });
});

# 2. Run test - should FAIL
npm test

# 3. Implement component
# src/components/CourseForm.vue

# 4. Run test - should PASS
npm test

# 5. Refactor if needed
```

### Step 5: Code Quality Checks

**Before committing**:

```bash
# Backend - Run Pint (code style)
./vendor/bin/pint

# Frontend - Run ESLint
npm run lint

# Run all tests
php artisan test
npm test

# Check coverage
php artisan test --coverage
npm run test:coverage

# Security scan
composer audit
npm audit
```

**Quality Gates**:
- ✅ All tests passing
- ✅ 80% backend coverage, 70% frontend coverage
- ✅ No linter errors
- ✅ No security vulnerabilities (critical/high)
- ✅ Accessibility check passed (for UI changes)

### Step 6: Submit Pull Request

**PR Template**:

```markdown
## Description
[Brief description of changes]

## Related Specification
- Spec: `specs/###-feature-name/spec.md`
- Plan: `specs/###-feature-name/plan.md`
- Tasks: `specs/###-feature-name/tasks.md`

## Constitution Compliance

### Principles Verified:
- [x] I. API-First Architecture - [How verified]
- [x] II. Security & Data Protection - [How verified]
- [x] III. Test-First Development - [Tests included]
- [x] IV. Accessibility Standards - [Audit results]
- [x] V. Performance & Scalability - [Benchmark results]
- [x] VI. Modular Feature Design - [Module boundaries clear]
- [x] VII. Documentation & User Experience - [Docs updated]

### Test Results:
- Backend: [X] passing, [Y]% coverage
- Frontend: [X] passing, [Y]% coverage
- E2E: [X] passing

### Security:
- [x] Input validation via Form Requests
- [x] Authorization via Policies
- [x] No raw SQL queries
- [x] XSS prevention verified

### Accessibility:
- [x] WCAG 2.1 AA compliance checked
- [x] Keyboard navigation tested
- [x] Screen reader compatible

## Breaking Changes
[Yes/No - describe if yes]

## Screenshots/Demo
[For UI changes]
```

## 🔍 Code Review Process

**Reviewers check**:
1. Constitution compliance (all 7 principles)
2. Test coverage and quality
3. Security best practices
4. Accessibility standards
5. Performance considerations
6. Documentation completeness

**Approval requires**:
- 2+ approvals from maintainers
- All CI checks passing
- Constitution verification complete

## 🎨 Coding Standards

### Backend (Laravel/PHP)

**File Organization**:
```
app/
├── Actions/          # Single-purpose action classes
├── Http/
│   ├── Controllers/  # Thin controllers (delegate to services)
│   ├── Requests/     # Form validation
│   ├── Resources/    # API response transformers
│   └── Middleware/   # Request/response filtering
├── Models/           # Eloquent models
├── Policies/         # Authorization logic
├── Services/         # Business logic
└── Events/           # Domain events
```

**Best Practices**:
- Controllers: Thin, delegate to services/actions
- Services: Business logic, single responsibility
- Models: Eloquent relationships, scopes, accessors
- Policies: Authorization (one method per action)
- Form Requests: All input validation
- Resources: API response transformation
- NO business logic in controllers or models

**Example**:
```php
// ❌ BAD: Logic in controller
public function store(Request $request)
{
    $validated = $request->validate(['title' => 'required']);
    if (Auth::user()->role !== 'instructor') {
        abort(403);
    }
    $course = Course::create($validated);
    return response()->json($course, 201);
}

// ✅ GOOD: Delegate to services/policies
public function store(CreateCourseRequest $request, CourseService $service)
{
    $this->authorize('create', Course::class);
    $course = $service->create($request->validated());
    return new CourseResource($course);
}
```

### Frontend (Vue.js/TypeScript)

**Component Organization**:
```
src/
├── components/       # Reusable components
│   ├── common/      # Buttons, inputs, cards
│   └── features/    # Feature-specific components
├── views/           # Page components (route targets)
├── composables/     # Reusable Composition API logic
├── stores/          # Pinia state management
├── services/        # API client methods
└── router/          # Vue Router configuration
```

**Best Practices**:
- Use Composition API (not Options API)
- TypeScript for type safety
- Props with proper typing
- Emit events for parent communication
- Pinia for shared state (not props drilling)
- Services for API calls (not in components)

**Example**:
```vue
<script setup lang="ts">
// ✅ GOOD: Composition API with TypeScript
import { ref, computed } from 'vue'
import { useCourseStore } from '@/stores/course'
import type { Course } from '@/types'

interface Props {
  courseId: string
}

const props = defineProps<Props>()
const emit = defineEmits<{
  courseUpdated: [course: Course]
}>()

const courseStore = useCourseStore()
const isLoading = ref(false)

const course = computed(() => courseStore.getCourseById(props.courseId))

async function updateCourse(data: Partial<Course>) {
  isLoading.value = true
  try {
    const updated = await courseStore.updateCourse(props.courseId, data)
    emit('courseUpdated', updated)
  } finally {
    isLoading.value = false
  }
}
</script>
```

### Database Migrations

**Rules**:
- Reversible (implement `down()` method)
- Backward compatible (don't break existing code)
- Run migrations before deploying code
- Use Eloquent schema builder (no raw SQL)

**Example**:
```php
// ✅ GOOD: Reversible migration
public function up()
{
    Schema::create('courses', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->string('title');
        $table->text('description')->nullable();
        $table->foreignUuid('instructor_id')->constrained('users');
        $table->timestamps();
        $table->softDeletes();
    });
}

public function down()
{
    Schema::dropIfExists('courses');
}
```

## 🧪 Testing Standards

### Test Coverage Requirements

- **Backend**: 80% minimum (services, actions, policies)
- **Frontend**: 70% minimum (components, stores, composables)
- **Critical paths**: 100% (authentication, authorization, payments, grading)

### Test Types

**Backend**:
- **Unit Tests**: Services, actions, policies (isolated, fast)
- **Feature Tests**: API endpoints (integration, database interactions)
- **Contract Tests**: Request/response validation

**Frontend**:
- **Unit Tests**: Composables, utilities (isolated)
- **Component Tests**: Vue components (user interactions)
- **E2E Tests**: Critical user flows (Laravel Dusk)

### Test Naming

```php
// Backend: test_[what]_[scenario]_[expected]
public function test_course_creation_with_valid_data_succeeds()
public function test_course_creation_without_title_fails()
public function test_unauthorized_user_cannot_delete_course()
```

```typescript
// Frontend: describe() + it()
describe('CourseForm', () => {
  it('displays validation error when title is empty')
  it('submits form data when all fields are valid')
  it('disables submit button while request is pending')
})
```

## ♿ Accessibility Checklist

For any UI changes, verify:

- [ ] Semantic HTML (headings, lists, forms)
- [ ] ARIA labels for icons and custom controls
- [ ] Keyboard navigation (Tab, Enter, Escape)
- [ ] Focus indicators visible
- [ ] Color contrast 4.5:1 minimum
- [ ] Form labels associated with inputs
- [ ] Error messages announced to screen readers
- [ ] No keyboard traps
- [ ] Tested with screen reader (NVDA/JAWS/VoiceOver)

**Tools**:
- axe DevTools (browser extension)
- Lighthouse accessibility audit
- WAVE Web Accessibility Evaluation Tool

## 🔒 Security Checklist

- [ ] Input validation (server-side Form Requests)
- [ ] Authorization (Laravel Policies, not manual checks)
- [ ] SQL injection prevention (Eloquent, no raw queries)
- [ ] XSS prevention (Vue auto-escaping, CSP headers)
- [ ] CSRF protection (Laravel middleware)
- [ ] Secure authentication (Sanctum, bcrypt passwords)
- [ ] Sensitive data encrypted (database encryption)
- [ ] HTTPS enforced (production)
- [ ] Rate limiting (API routes)
- [ ] Security headers (Helmet.js equivalent for Laravel)

## 📚 Documentation Requirements

### Code Documentation

**Backend**:
```php
/**
 * Create a new course with the provided data.
 *
 * @param array $data Course attributes (title, description, instructor_id)
 * @return Course The created course instance
 * @throws ValidationException If data is invalid
 * @throws AuthorizationException If user lacks permission
 */
public function createCourse(array $data): Course
{
    // Implementation
}
```

**Frontend**:
```typescript
/**
 * Fetches a course by ID from the API.
 * @param courseId - Unique course identifier
 * @returns Promise resolving to Course object
 * @throws Error if course not found or network fails
 */
async function fetchCourse(courseId: string): Promise<Course> {
  // Implementation
}
```

### API Documentation

Document all endpoints using Laravel API Resource documentation or OpenAPI:

```php
/**
 * @group Courses
 * 
 * Create a new course.
 * 
 * @bodyParam title string required The course title. Example: Introduction to Laravel
 * @bodyParam description string The course description. Example: Learn Laravel from scratch
 * @bodyParam instructor_id uuid required The instructor's user ID.
 * 
 * @response 201 {"data": {"id": "uuid", "title": "Introduction to Laravel", ...}}
 * @response 422 {"errors": {"title": ["The title field is required."]}}
 */
public function store(CreateCourseRequest $request)
```

### User-Facing Documentation

- In-app help text for complex features
- Tooltips for icons and actions
- Actionable error messages ("Email already registered. Try logging in or reset your password.")
- Success confirmations ("Course created successfully!")

## 🚫 Common Mistakes to Avoid

### Backend

❌ Business logic in controllers
❌ Raw SQL queries without parameter binding
❌ Authorization checks in controllers (use Policies)
❌ Validation in controllers (use Form Requests)
❌ N+1 queries (use eager loading)
❌ Storing passwords in plain text
❌ Exposing sensitive data in API responses

### Frontend

❌ API calls in components (use stores/services)
❌ Props drilling (use Pinia)
❌ Missing TypeScript types
❌ Options API (use Composition API)
❌ Accessibility ignored (keyboard, screen readers)
❌ Poor error handling (network failures)
❌ Missing loading states

## 🆘 Getting Help

- **Constitution Questions**: Review [.specify/memory/constitution.md](.specify/memory/constitution.md)
- **Technical Questions**: [GitHub Discussions](https://github.com/your-org/online-learning-platform/discussions)
- **Bugs**: [GitHub Issues](https://github.com/your-org/online-learning-platform/issues)
- **Security Issues**: security@yourorganization.com (private disclosure)

## 🎉 Recognition

Quality contributions will be recognized:
- Contributors list in README
- Special mentions in release notes
- Badges for significant contributions

Thank you for contributing to accessible, secure, and high-quality education! 🚀

