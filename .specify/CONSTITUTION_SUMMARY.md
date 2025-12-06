# Constitution Summary

**Version**: 1.0.0  
**Ratified**: 2025-12-06  
**Status**: Active  
**Project**: Online Learning Platform (Laravel + Vue.js LMS)

## Quick Reference

This document provides a quick reference for the project constitution. For full details, see [constitution.md](memory/constitution.md).

## 7 Core Principles

### 1️⃣ API-First Architecture
**What**: Laravel backend provides RESTful APIs; Vue.js frontend consumes them exclusively.

**Key Rules**:
- Business logic in Laravel (never in Vue components)
- API endpoints versioned (`/api/v1/`)
- No direct database access from frontend

**Why**: Independent scaling, mobile app support, third-party integrations.

---

### 2️⃣ Security & Data Protection (NON-NEGOTIABLE)
**What**: Built-in security from day one for educational data protection.

**Key Rules**:
- Authentication via Laravel Sanctum/Passport
- Server-side validation (Form Requests)
- RBAC with Laravel Policies
- Encryption at rest/transit (HTTPS)
- CSRF protection enabled
- SQL injection prevention (Eloquent ORM)
- XSS prevention (auto-escaping)

**Why**: FERPA/GDPR compliance, student privacy protection.

---

### 3️⃣ Test-First Development (NON-NEGOTIABLE)
**What**: Tests written before implementation.

**Key Rules**:
- Tests → Fail → Implement → Pass → Refactor
- 80% backend coverage, 70% frontend coverage
- API contract tests for all endpoints
- Integration tests for user journeys

**Why**: Prevents regressions, enables confident refactoring, production stability.

---

### 4️⃣ Accessibility Standards
**What**: WCAG 2.1 Level AA minimum for all UI.

**Key Rules**:
- Semantic HTML with ARIA labels
- Keyboard navigation support
- 4.5:1 color contrast ratio
- Screen reader compatible
- Video captions/transcripts

**Why**: Legal compliance (ADA, Section 508), inclusive education.

---

### 5️⃣ Performance & Scalability
**What**: Handle 1000+ concurrent users during peak times.

**Key Rules**:
- <200ms API response time (p95)
- <3s initial load on 3G
- Pagination/virtual scrolling for large datasets
- Eager loading (prevent N+1 queries)
- Redis caching for frequent data
- Background jobs for long tasks

**Why**: Usability during high-traffic periods, responsive UX.

---

### 6️⃣ Modular Feature Design
**What**: Independent, testable, deployable features.

**Key Rules**:
- Clear feature boundaries (Courses, Assignments, Gradebook)
- Single-purpose Laravel services
- Single-responsibility Vue components
- Reversible database migrations
- Events/listeners for cross-feature communication
- Feature flags for incomplete features

**Why**: Parallel development, incremental rollout, easier debugging.

---

### 7️⃣ Documentation & User Experience
**What**: Self-service learning platform with excellent docs.

**Key Rules**:
- API documentation (OpenAPI/Laravel docs)
- PHPDoc/JSDoc for public methods
- In-app help text/tooltips
- Actionable error messages
- 10-minute quickstart guide
- Acceptance criteria in specs

**Why**: Reduces onboarding time, decreases support burden, improves productivity.

## Technology Stack

### Mandatory
- **Backend**: Laravel 11.x + PHP 8.2+
- **Frontend**: Vue.js 3.x (Composition API) + TypeScript
- **Database**: PostgreSQL 14+ (recommended) or MySQL 8.0+
- **Caching**: Redis 7.x
- **Testing**: PHPUnit 10.x + Vitest/Jest
- **Build**: Vite 5.x

### Prohibited
- `DB::raw()` without parameter binding
- Plain text passwords (use bcrypt/argon2)
- Business logic in controllers
- Global state in Vue components

### Requires Justification
- Additional JS frameworks beyond Vue.js
- NoSQL databases alongside relational DB
- Third-party packages without security audit

## Quality Gates

### Pre-Commit
- ✅ Laravel Pint/PHP CS Fixer (PSR-12)
- ✅ ESLint (Vue.js config)
- ✅ No linter errors

### Pre-Merge
- ✅ All tests passing
- ✅ 80% backend, 70% frontend coverage
- ✅ API contract tests passing
- ✅ Accessibility audit passed (UI changes)

### Pre-Deployment
- ✅ Staging mirrors production
- ✅ Smoke tests passing
- ✅ Migrations successful in staging
- ✅ Performance benchmarks met
- ✅ No critical vulnerabilities

## Constitution Compliance Workflow

### When Creating Features

1. **Specification** (`/speckit.specify`)
   - Write user scenarios (testable)
   - Define acceptance criteria
   - Include success criteria (measurable)

2. **Planning** (`/speckit.plan`)
   - Fill Constitution Check section
   - Verify all 7 principles
   - Justify any violations in Complexity Tracking

3. **Tasks** (`/speckit.tasks`)
   - Organize by user story (P1, P2, P3)
   - Include test tasks (write first)
   - Define independent checkpoints

4. **Implementation**
   - Red-Green-Refactor cycle
   - API-first design
   - Security built-in
   - Accessibility from start

5. **Review**
   - Constitution compliance verified
   - All quality gates passed
   - 2+ maintainer approvals

## Common Violations & Justifications

If you violate a principle, document in Implementation Plan's **Complexity Tracking** table:

| Violation | Why Needed | Simpler Alternative Rejected Because |
|-----------|------------|--------------------------------------|
| Using NoSQL (Redis as primary DB) | Real-time messaging requires pub/sub | PostgreSQL lacks efficient pub/sub for 1000+ concurrent users |
| Skip accessibility audit | Internal admin tool only | Rejected - admin users may have disabilities |

## Quick Decision Tree

**Should I violate a principle?**

```
Is it for production code?
├─ NO (POC/spike) → Allowed, don't merge
└─ YES → Continue

Is it a temporary exception?
├─ YES (e.g., legacy migration)
│   └─ Document remediation timeline → Allowed
└─ NO → Continue

Does a simpler alternative exist?
├─ YES → Use simpler alternative
└─ NO → Document in Complexity Tracking → Allowed
```

## Amendment Process

1. **Propose**: Document rationale and impact analysis
2. **Review**: Technical lead + stakeholders approval
3. **Version**: Increment per semantic versioning
   - **MAJOR**: Breaking changes, principle removal
   - **MINOR**: New principles, significant expansions
   - **PATCH**: Clarifications, examples
4. **Update**: All dependent templates and docs
5. **Notify**: Team with transition timeline

## Resources

- **Full Constitution**: [memory/constitution.md](memory/constitution.md)
- **README**: [../../README.md](../../README.md)
- **Contributing Guide**: [../../CONTRIBUTING.md](../../CONTRIBUTING.md)
- **Plan Template**: [../templates/plan-template.md](../templates/plan-template.md)
- **Spec Template**: [../templates/spec-template.md](../templates/spec-template.md)
- **Tasks Template**: [../templates/tasks-template.md](../templates/tasks-template.md)

## Enforcement

- **Feature specs**: Must include Constitution Check
- **Code reviews**: Must verify principle adherence
- **Quarterly audits**: Review codebase compliance
- **Violations**: Justify in writing or remediate

## Success Metrics

Track constitution effectiveness:

- **Test Coverage**: Maintain >80% backend, >70% frontend
- **Accessibility Score**: 100% WCAG 2.1 AA compliance
- **Performance**: <200ms API response time (p95)
- **Security**: Zero critical vulnerabilities in production
- **Documentation**: <10 minute onboarding for new developers
- **Modularity**: Features deployable independently

## Key Contacts

- **Constitution Amendments**: Technical Lead
- **Security Issues**: security@yourorganization.com
- **Accessibility Questions**: UX Team
- **Performance Concerns**: DevOps Team

---

**Remember**: The constitution exists to ensure we build a production-ready, accessible, secure LMS that serves students and educators effectively. When in doubt, prioritize user value and system integrity.

