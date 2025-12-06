<!--
SYNC IMPACT REPORT
==================
Version Change: Initial Release → 1.0.0
Type: MAJOR (Initial Constitution)
Date: 2025-12-06

Core Principles Established:
- I. API-First Architecture (Laravel + Vue.js separation)
- II. Security & Data Protection (Educational data compliance)
- III. Test-First Development (NON-NEGOTIABLE for production readiness)
- IV. Accessibility Standards (WCAG 2.1 AA minimum)
- V. Performance & Scalability (Production LMS requirements)
- VI. Modular Feature Design (Independent, testable features)
- VII. Documentation & User Experience (Self-service learning)

Sections Added:
- Technology Stack Standards
- Quality Gates & Deployment
- Governance

Templates Status:
✅ plan-template.md - Constitution Check section ready
✅ spec-template.md - User stories align with accessibility & security
✅ tasks-template.md - Test-first workflow enforced
⚠ All command files - Should reference this constitution for guidance

Follow-up Items:
- None (initial ratification)
-->

# Online Learning Platform Constitution

## Core Principles

### I. API-First Architecture

All features MUST be designed with a clear separation between backend (Laravel) and frontend (Vue.js). Backend provides RESTful APIs; frontend consumes them exclusively through documented endpoints.

**Rules**:
- All business logic resides in Laravel services/actions, never in Vue components
- API endpoints MUST be versioned (e.g., `/api/v1/courses`)
- Frontend state management (Pinia/Vuex) reflects API data models
- No direct database access from frontend; all data via authenticated API calls

**Rationale**: Enables independent scaling, mobile app development, third-party integrations, and clear separation of concerns for maintainability.

### II. Security & Data Protection

Educational platforms handle sensitive student data. Security is NON-NEGOTIABLE and MUST be built-in from day one, not retrofitted.

**Rules**:
- Authentication MUST use Laravel Sanctum or Passport with secure token management
- All user inputs MUST be validated server-side (Laravel Form Requests)
- Authorization MUST follow role-based access control (RBAC) with Laravel Policies
- Sensitive data (grades, personal info) MUST be encrypted at rest and in transit (HTTPS enforced)
- Password policies MUST enforce minimum complexity (Laravel validation rules)
- CSRF protection MUST be enabled for all state-changing operations
- SQL injection prevention via Eloquent ORM (no raw queries without parameter binding)
- XSS prevention through Vue.js automatic escaping and Laravel Blade when used

**Rationale**: Compliance with FERPA, GDPR, and other educational data protection regulations. Protecting student privacy is a legal and ethical obligation.

### III. Test-First Development (NON-NEGOTIABLE)

Production-ready systems require comprehensive testing. Tests MUST be written before implementation to ensure requirements are clear and code is verifiable.

**Rules**:
- Feature specifications MUST include acceptance criteria as testable scenarios
- Backend: PHPUnit tests written first → tests fail → implement → tests pass
- Frontend: Vitest/Jest component tests for critical user flows
- API endpoints MUST have contract tests (request/response validation)
- Integration tests MUST cover user journeys across backend and frontend
- Minimum coverage: 80% for backend services, 70% for frontend components
- Red-Green-Refactor cycle strictly enforced

**Rationale**: Prevents regressions, documents behavior, enables confident refactoring, and ensures production stability.

### IV. Accessibility Standards

Education MUST be accessible to all learners regardless of ability. Accessibility is a feature requirement, not an afterthought.

**Rules**:
- All UI MUST meet WCAG 2.1 Level AA standards minimum
- Semantic HTML MUST be used (proper heading hierarchy, ARIA labels where needed)
- Keyboard navigation MUST work for all interactive elements
- Color contrast MUST meet 4.5:1 ratio for normal text, 3:1 for large text
- Screen reader compatibility MUST be tested for critical workflows
- Forms MUST have proper labels and error messaging
- Video content MUST support captions/transcripts

**Rationale**: Legal compliance (ADA, Section 508), inclusive education, and broader user base access.

### V. Performance & Scalability

LMS platforms serve hundreds to thousands of concurrent users during peak times (e.g., exam periods, assignment deadlines). Performance is critical to user experience.

**Rules**:
- API response times MUST be under 200ms for p95 (excluding file uploads/processing)
- Database queries MUST use eager loading to prevent N+1 problems
- Frontend initial load MUST be under 3 seconds on 3G networks
- Large data sets (student lists, gradebooks) MUST implement pagination or virtual scrolling
- File uploads (assignments, videos) MUST use chunked uploads with progress indicators
- Caching MUST be implemented for frequently accessed, rarely changed data (Redis/Memcached)
- Background jobs MUST handle long-running tasks (grade calculations, email notifications)

**Rationale**: Maintains usability during high-traffic periods, prevents server overload, and provides responsive user experience.

### VI. Modular Feature Design

Features MUST be independently developable, testable, and deployable to enable parallel development and incremental delivery.

**Rules**:
- Each feature MUST have clear boundaries (e.g., Courses, Assignments, Gradebook as separate modules)
- Laravel services MUST be single-purpose and composable
- Vue components MUST follow single responsibility (presentational vs. container components)
- Database migrations MUST be reversible and run independently
- Features MUST NOT create tight coupling (use events/listeners for cross-feature communication)
- Feature flags MUST be used for incomplete features in production

**Rationale**: Enables team parallelization, reduces merge conflicts, allows incremental rollout, and simplifies debugging.

### VII. Documentation & User Experience

Self-service learning platforms require excellent documentation and intuitive UX. Users should succeed without support tickets.

**Rules**:
- All API endpoints MUST be documented (Laravel API Resource documentation or OpenAPI)
- Public methods MUST have PHPDoc/JSDoc comments explaining purpose and parameters
- User-facing features MUST include in-app help text or tooltips
- Error messages MUST be actionable (tell users what to do, not just what went wrong)
- README MUST include quickstart guide (setup in under 10 minutes)
- Each feature MUST have acceptance criteria documented in specs/

**Rationale**: Reduces onboarding time, decreases support burden, improves developer productivity, and enhances user satisfaction.

## Technology Stack Standards

**Mandatory Stack**:
- **Backend**: Laravel 11.x (LTS recommended) with PHP 8.2+
- **Frontend**: Vue.js 3.x with Composition API, TypeScript strongly recommended
- **Database**: MySQL 8.0+ or PostgreSQL 14+ (PostgreSQL preferred for advanced features)
- **Caching**: Redis 7.x for session storage, queue driver, and application cache
- **Testing**: PHPUnit 10.x (backend), Vitest or Jest (frontend), Laravel Dusk (E2E)
- **Build Tools**: Vite 5.x for frontend asset compilation
- **Version Control**: Git with semantic commit messages (Conventional Commits)

**Prohibited**:
- Direct use of `DB::raw()` without parameter binding (SQL injection risk)
- Storing passwords in plain text or using weak hashing (use bcrypt/argon2)
- Mixing business logic in controllers (use Actions/Services pattern)
- Global state in Vue components (use Pinia for state management)

**Justification Required**:
- Adding additional JavaScript frameworks beyond Vue.js
- Using NoSQL databases alongside relational DB (justify specific use case)
- Third-party packages without security audit or active maintenance

## Quality Gates & Deployment

**Pre-Commit Requirements**:
- Code MUST pass Laravel Pint or PHP CS Fixer (PSR-12 standard)
- Frontend code MUST pass ESLint (Airbnb or Vue.js recommended config)
- No linter errors allowed (warnings acceptable if justified)

**Pre-Merge Requirements**:
- All tests MUST pass (PHPUnit, Vitest, integration tests)
- Code coverage MUST meet minimum thresholds (80% backend, 70% frontend)
- API contract tests MUST pass for modified endpoints
- Accessibility audit MUST pass for UI changes (axe-core or similar)

**Pre-Deployment Requirements**:
- Staging environment MUST mirror production configuration
- Smoke tests MUST pass in staging (login, critical user flows)
- Database migrations MUST run successfully in staging
- Performance benchmarks MUST be within acceptable ranges
- Security scan MUST show no critical vulnerabilities (Laravel security checker, npm audit)

**Deployment Process**:
- Deployments MUST follow blue-green or rolling deployment strategy
- Database migrations MUST be backward-compatible (run before code deployment)
- Feature flags MUST control new feature visibility
- Rollback plan MUST be documented and tested

## Governance

**Constitution Authority**:
This constitution supersedes all other development practices and coding standards. When conflicts arise, constitution principles take precedence.

**Amendment Procedure**:
1. Proposed changes MUST be documented with rationale and impact analysis
2. Amendment MUST be reviewed by technical lead and stakeholders
3. Version number MUST be incremented per semantic versioning:
   - **MAJOR**: Breaking changes to core principles or removal of requirements
   - **MINOR**: New principles added or significant expansions
   - **PATCH**: Clarifications, examples, or non-semantic improvements
4. All dependent templates and documentation MUST be updated to reflect amendments
5. Team MUST be notified of changes with transition timeline

**Compliance Verification**:
- All feature specifications MUST include "Constitution Check" section referencing relevant principles
- Code reviews MUST verify adherence to applicable principles
- Quarterly audits SHOULD review compliance across the codebase
- Violations MUST be justified in writing (Complexity Tracking table) or remediated

**Complexity Justification**:
Violations of constitutional principles MUST be documented in the implementation plan's "Complexity Tracking" section with:
- Which principle is violated
- Why the violation is necessary
- What simpler alternatives were considered and rejected

**Exception Process**:
Temporary exceptions may be granted for:
- Proof-of-concept or spike work (MUST NOT merge to production)
- Legacy code migration (MUST have remediation timeline)
- Critical security patches (MUST be backfilled with tests within one sprint)

**Version**: 1.0.0 | **Ratified**: 2025-12-06 | **Last Amended**: 2025-12-06
