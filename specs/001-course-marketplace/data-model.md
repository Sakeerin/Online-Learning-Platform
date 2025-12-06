# Data Model: Course Marketplace Platform

**Date**: 2025-12-06  
**Feature**: Course Marketplace Platform (001-course-marketplace)  
**Purpose**: Define database schema, entity relationships, validation rules, and state transitions

---

## Entity Relationship Diagram (ERD)

```
┌─────────────┐
│    User     │
│ (Students & │
│ Instructors)│
└──────┬──────┘
       │
       ├──────owns──────┐
       │                │
       │         ┌──────▼──────┐
       │         │   Course    │◄────has────┐
       │         └──────┬──────┘            │
       │                │                   │
       ├──enrolls─in───►│◄───belongs to────┤
       │                │                ┌──┴───┐
       │         ┌──────▼──────┐        │Section│
       │         │ Enrollment  │        └───┬───┘
       │         └──────┬──────┘            │
       │                │                   │
       │          ┌─────▼─────┐      ┌─────▼────┐
       │          │ Progress  │◄─────│  Lesson  │
       │          └───────────┘      └─────┬────┘
       │                                   │
       ├──makes────┐                       │
       │           │                ┌──────▼─────┐
       │    ┌──────▼──────┐         │    Quiz    │
       │    │Transaction  │         └──────┬─────┘
       │    └─────────────┘                │
       │                            ┌──────▼──────┐
       ├──writes───┐                │  Question   │
       │           │                └─────────────┘
       │    ┌──────▼──────┐
       │    │   Review    │         ┌─────────────┐
       │    └─────────────┘         │ QuizAttempt │
       │                            └─────────────┘
       ├──posts────┐
       │           │
       │    ┌──────▼──────┐         ┌─────────────┐
       │    │ Discussion  │◄────┬───│   Reply     │
       │    └─────────────┘     │   └─────────────┘
       │                        │
       └──replies to────────────┘
       
       ┌──────────────┐
       │ Certificate  │
       └──────────────┘
```

---

## 1. User

**Purpose**: Represents all platform users (students, instructors, administrators)

### Schema

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| id | UUID | PRIMARY KEY | Unique user identifier |
| name | VARCHAR(255) | NOT NULL | Full name |
| email | VARCHAR(255) | UNIQUE, NOT NULL | Email address (login) |
| email_verified_at | TIMESTAMP | NULLABLE | Email verification timestamp |
| password | VARCHAR(255) | NOT NULL | Hashed password (bcrypt) |
| role | ENUM | NOT NULL, DEFAULT 'student' | User role: student, instructor, admin |
| profile_photo | VARCHAR(255) | NULLABLE | S3 URL to profile image |
| bio | TEXT | NULLABLE | User biography (instructors) |
| created_at | TIMESTAMP | NOT NULL | Account creation date |
| updated_at | TIMESTAMP | NOT NULL | Last profile update |
| deleted_at | TIMESTAMP | NULLABLE | Soft delete timestamp |

### Validation Rules

- **name**: Required, max 255 characters
- **email**: Required, valid email format, unique, max 255 characters
- **password**: Required on creation, min 8 characters, must contain uppercase, lowercase, number
- **role**: Must be one of: student, instructor, admin
- **profile_photo**: Optional, valid URL format, file size <5MB
- **bio**: Optional, max 1000 characters

### Indexes

- PRIMARY KEY on `id`
- UNIQUE INDEX on `email`
- INDEX on `role` (for role-based queries)
- INDEX on `created_at` (for analytics)

### Relationships

- **One-to-Many** with Course (as instructor)
- **One-to-Many** with Enrollment (as student)
- **One-to-Many** with Transaction (as buyer)
- **One-to-Many** with Review (as reviewer)
- **One-to-Many** with Discussion (as author)
- **One-to-Many** with Reply (as author)
- **One-to-Many** with Certificate (as recipient)
- **One-to-Many** with QuizAttempt (as student)

---

## 2. Course

**Purpose**: Represents a learning course created by an instructor

### Schema

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| id | UUID | PRIMARY KEY | Unique course identifier |
| instructor_id | UUID | FOREIGN KEY (users.id), NOT NULL | Course owner |
| title | VARCHAR(255) | NOT NULL | Course title |
| subtitle | VARCHAR(500) | NULLABLE | Short tagline |
| description | TEXT | NOT NULL | Full course description |
| learning_objectives | JSONB | NULLABLE | Array of learning goals |
| category | VARCHAR(100) | NOT NULL | Main category (e.g., Development) |
| subcategory | VARCHAR(100) | NULLABLE | Subcategory (e.g., Web Development) |
| difficulty_level | ENUM | NOT NULL | Beginner, Intermediate, Advanced |
| thumbnail | VARCHAR(255) | NULLABLE | S3 URL to course cover image |
| price | DECIMAL(10,2) | NOT NULL, DEFAULT 0.00 | Course price (0 = free) |
| currency | VARCHAR(3) | NOT NULL, DEFAULT 'USD' | Price currency code |
| status | ENUM | NOT NULL, DEFAULT 'draft' | draft, published, unpublished |
| published_at | TIMESTAMP | NULLABLE | First publication date |
| average_rating | DECIMAL(3,2) | NULLABLE | Calculated average (1.00-5.00) |
| total_enrollments | INTEGER | NOT NULL, DEFAULT 0 | Cached enrollment count |
| total_reviews | INTEGER | NOT NULL, DEFAULT 0 | Cached review count |
| created_at | TIMESTAMP | NOT NULL | Course creation date |
| updated_at | TIMESTAMP | NOT NULL | Last modification date |
| deleted_at | TIMESTAMP | NULLABLE | Soft delete timestamp |

### Validation Rules

- **title**: Required, max 255 characters, unique per instructor
- **description**: Required, min 100 characters
- **category**: Required, must be from predefined list
- **difficulty_level**: Required, one of: beginner, intermediate, advanced
- **price**: Required, >= 0, max 9999.99
- **status**: One of: draft, published, unpublished
- **Before publishing**: Must have at least 1 section with 1 video lesson, thumbnail, description

### Indexes

- PRIMARY KEY on `id`
- INDEX on `instructor_id` (find courses by instructor)
- INDEX on `category` (browse by category)
- INDEX on `status` (filter published courses)
- INDEX on `average_rating` (sort by rating)
- FULLTEXT INDEX on `title, description` (search)
- INDEX on `created_at` (recent courses)

### Relationships

- **Many-to-One** with User (instructor)
- **One-to-Many** with Section
- **One-to-Many** with Enrollment
- **One-to-Many** with Transaction
- **One-to-Many** with Review
- **One-to-Many** with Discussion

### State Transitions

```
draft → published → unpublished
  ↑                      ↓
  └──────────────────────┘
```

- **draft → published**: Requires validation (sections, lessons, thumbnail)
- **published → unpublished**: Instructor can hide course (enrolled students retain access)
- **unpublished → published**: Re-publish course

---

## 3. Section

**Purpose**: Represents a course module/chapter containing lessons

### Schema

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| id | UUID | PRIMARY KEY | Unique section identifier |
| course_id | UUID | FOREIGN KEY (courses.id), NOT NULL | Parent course |
| title | VARCHAR(255) | NOT NULL | Section title |
| description | TEXT | NULLABLE | Section overview |
| order | INTEGER | NOT NULL | Display order (1, 2, 3...) |
| created_at | TIMESTAMP | NOT NULL | Section creation date |
| updated_at | TIMESTAMP | NOT NULL | Last modification date |

### Validation Rules

- **title**: Required, max 255 characters
- **order**: Required, positive integer, unique per course

### Indexes

- PRIMARY KEY on `id`
- INDEX on `course_id, order` (ordered section listing)
- FOREIGN KEY on `course_id` with CASCADE delete

### Relationships

- **Many-to-One** with Course
- **One-to-Many** with Lesson

---

## 4. Lesson

**Purpose**: Represents individual learning content (video, quiz, article)

### Schema

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| id | UUID | PRIMARY KEY | Unique lesson identifier |
| section_id | UUID | FOREIGN KEY (sections.id), NOT NULL | Parent section |
| title | VARCHAR(255) | NOT NULL | Lesson title |
| type | ENUM | NOT NULL | video, quiz, article |
| content | JSONB | NOT NULL | Type-specific data |
| duration | INTEGER | NULLABLE | Duration in seconds (for videos) |
| is_preview | BOOLEAN | NOT NULL, DEFAULT false | Free preview lesson |
| order | INTEGER | NOT NULL | Display order within section |
| created_at | TIMESTAMP | NOT NULL | Lesson creation date |
| updated_at | TIMESTAMP | NOT NULL | Last modification date |

### Content Field Structure

**For video lessons**:
```json
{
  "video_url": "https://cdn.cloudfront.net/videos/course123/lesson456/720p.mp4",
  "video_qualities": ["1080p", "720p", "480p", "360p"],
  "thumbnail_url": "https://cdn.cloudfront.net/thumbnails/lesson456.jpg",
  "captions_url": "https://cdn.cloudfront.net/captions/lesson456.vtt"
}
```

**For quiz lessons**:
```json
{
  "quiz_id": "uuid-of-quiz-record"
}
```

**For article lessons**:
```json
{
  "body": "<p>Markdown or HTML content</p>",
  "attachments": ["url1", "url2"]
}
```

### Validation Rules

- **title**: Required, max 255 characters
- **type**: Required, one of: video, quiz, article
- **content**: Required, valid JSON for lesson type
- **duration**: Required for video lessons, null for others
- **order**: Required, positive integer, unique per section

### Indexes

- PRIMARY KEY on `id`
- INDEX on `section_id, order` (ordered lesson listing)
- FOREIGN KEY on `section_id` with CASCADE delete

### Relationships

- **Many-to-One** with Section
- **One-to-One** with Quiz (if type = quiz)
- **One-to-Many** with Progress

---

## 5. Enrollment

**Purpose**: Represents student enrollment in a course

### Schema

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| id | UUID | PRIMARY KEY | Unique enrollment identifier |
| user_id | UUID | FOREIGN KEY (users.id), NOT NULL | Enrolled student |
| course_id | UUID | FOREIGN KEY (courses.id), NOT NULL | Enrolled course |
| enrolled_at | TIMESTAMP | NOT NULL | Enrollment date |
| last_accessed_at | TIMESTAMP | NULLABLE | Last course access |
| progress_percentage | DECIMAL(5,2) | NOT NULL, DEFAULT 0.00 | Completion % (0-100) |
| is_completed | BOOLEAN | NOT NULL, DEFAULT false | 100% completion flag |
| completed_at | TIMESTAMP | NULLABLE | Course completion date |
| created_at | TIMESTAMP | NOT NULL | Record creation date |
| updated_at | TIMESTAMP | NOT NULL | Last update date |

### Validation Rules

- **user_id + course_id**: Unique together (student can enroll once per course)
- **progress_percentage**: Range 0-100, calculated from lesson completions
- **is_completed**: Auto-set when progress_percentage = 100

### Indexes

- PRIMARY KEY on `id`
- UNIQUE INDEX on `user_id, course_id`
- INDEX on `user_id` (find user's enrollments)
- INDEX on `course_id` (find course enrollments)
- INDEX on `enrolled_at` (recent enrollments)

### Relationships

- **Many-to-One** with User (student)
- **Many-to-One** with Course
- **One-to-Many** with Progress
- **One-to-One** with Certificate (when completed)
- **One-to-Many** with QuizAttempt

---

## 6. Progress

**Purpose**: Tracks student completion of individual lessons

### Schema

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| id | UUID | PRIMARY KEY | Unique progress identifier |
| enrollment_id | UUID | FOREIGN KEY (enrollments.id), NOT NULL | Parent enrollment |
| lesson_id | UUID | FOREIGN KEY (lessons.id), NOT NULL | Tracked lesson |
| is_completed | BOOLEAN | NOT NULL, DEFAULT false | Lesson completion flag |
| video_position | INTEGER | NULLABLE | Last playback position (seconds) |
| completed_at | TIMESTAMP | NULLABLE | Completion timestamp |
| created_at | TIMESTAMP | NOT NULL | First access date |
| updated_at | TIMESTAMP | NOT NULL | Last update (position save) |

### Validation Rules

- **enrollment_id + lesson_id**: Unique together
- **video_position**: Range 0 to lesson.duration
- **is_completed**: Auto-set when video_position >= 95% of duration

### Indexes

- PRIMARY KEY on `id`
- UNIQUE INDEX on `enrollment_id, lesson_id`
- INDEX on `enrollment_id` (find enrollment progress)
- FOREIGN KEY on `enrollment_id` with CASCADE delete
- FOREIGN KEY on `lesson_id` with CASCADE delete

### Relationships

- **Many-to-One** with Enrollment
- **Many-to-One** with Lesson

### Business Logic

- **Mark lesson complete**: When video watched >= 95% of duration
- **Update enrollment progress**: `(completed_lessons / total_lessons) * 100`
- **Trigger course completion**: When enrollment progress = 100%, set `is_completed = true`, create Certificate

---

## 7. Transaction

**Purpose**: Records course purchase payments

### Schema

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| id | UUID | PRIMARY KEY | Unique transaction identifier |
| user_id | UUID | FOREIGN KEY (users.id), NOT NULL | Buyer (student) |
| course_id | UUID | FOREIGN KEY (courses.id), NOT NULL | Purchased course |
| amount | DECIMAL(10,2) | NOT NULL | Total amount paid |
| currency | VARCHAR(3) | NOT NULL, DEFAULT 'USD' | Payment currency |
| platform_fee | DECIMAL(10,2) | NOT NULL | Platform commission (30%) |
| instructor_revenue | DECIMAL(10,2) | NOT NULL | Instructor net earnings (70%) |
| payment_method | VARCHAR(50) | NOT NULL | card, paypal, apple_pay, etc. |
| payment_gateway_id | VARCHAR(255) | NULLABLE | Stripe payment_intent_id |
| status | ENUM | NOT NULL | pending, completed, refunded |
| refunded_at | TIMESTAMP | NULLABLE | Refund timestamp |
| refund_reason | TEXT | NULLABLE | Reason for refund |
| created_at | TIMESTAMP | NOT NULL | Transaction date |
| updated_at | TIMESTAMP | NOT NULL | Last status update |

### Validation Rules

- **amount**: Required, > 0
- **platform_fee**: Calculated as `amount * 0.30`
- **instructor_revenue**: Calculated as `amount - platform_fee`
- **status**: One of: pending, completed, refunded
- **refund_reason**: Required when status = refunded

### Indexes

- PRIMARY KEY on `id`
- INDEX on `user_id` (purchase history)
- INDEX on `course_id` (course sales)
- INDEX on `payment_gateway_id` (Stripe reconciliation)
- INDEX on `status` (pending transactions)
- INDEX on `created_at` (recent transactions)

### Relationships

- **Many-to-One** with User (buyer)
- **Many-to-One** with Course

### State Transitions

```
pending → completed → refunded
```

- **pending → completed**: Payment confirmed by Stripe webhook
- **completed → refunded**: Refund request approved, Stripe refund processed

---

## 8. Review

**Purpose**: Student ratings and reviews of courses

### Schema

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| id | UUID | PRIMARY KEY | Unique review identifier |
| user_id | UUID | FOREIGN KEY (users.id), NOT NULL | Reviewer (student) |
| course_id | UUID | FOREIGN KEY (courses.id), NOT NULL | Reviewed course |
| rating | INTEGER | NOT NULL, CHECK (1-5) | Star rating (1-5) |
| review_text | TEXT | NULLABLE | Written review |
| helpful_votes | INTEGER | NOT NULL, DEFAULT 0 | Helpful vote count |
| instructor_response | TEXT | NULLABLE | Instructor reply |
| responded_at | TIMESTAMP | NULLABLE | Response timestamp |
| is_flagged | BOOLEAN | NOT NULL, DEFAULT false | Flagged for moderation |
| created_at | TIMESTAMP | NOT NULL | Review submission date |
| updated_at | TIMESTAMP | NOT NULL | Last edit date |

### Validation Rules

- **user_id + course_id**: Unique together (one review per student per course)
- **rating**: Required, integer 1-5
- **review_text**: Optional, max 1000 characters
- **Student must be enrolled**: Check enrollment exists before allowing review

### Indexes

- PRIMARY KEY on `id`
- UNIQUE INDEX on `user_id, course_id`
- INDEX on `course_id` (course reviews)
- INDEX on `created_at` (recent reviews)
- INDEX on `is_flagged` (moderation queue)

### Relationships

- **Many-to-One** with User (reviewer)
- **Many-to-One** with Course

### Business Logic

- **Update course average_rating**: Recalculate on insert/update/delete
  ```sql
  UPDATE courses 
  SET average_rating = (SELECT AVG(rating) FROM reviews WHERE course_id = ?),
      total_reviews = (SELECT COUNT(*) FROM reviews WHERE course_id = ?)
  WHERE id = ?
  ```

---

## 9. Quiz

**Purpose**: Assessments within lessons

### Schema

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| id | UUID | PRIMARY KEY | Unique quiz identifier |
| lesson_id | UUID | FOREIGN KEY (lessons.id), NOT NULL | Parent lesson |
| title | VARCHAR(255) | NOT NULL | Quiz title |
| passing_score | INTEGER | NOT NULL, DEFAULT 80 | Minimum score % to pass |
| allow_retake | BOOLEAN | NOT NULL, DEFAULT true | Allow multiple attempts |
| created_at | TIMESTAMP | NOT NULL | Quiz creation date |
| updated_at | TIMESTAMP | NOT NULL | Last modification date |

### Validation Rules

- **title**: Required, max 255 characters
- **passing_score**: Integer 0-100
- **Must have at least 1 question** before lesson can be marked complete

### Indexes

- PRIMARY KEY on `id`
- UNIQUE INDEX on `lesson_id` (one quiz per lesson)
- FOREIGN KEY on `lesson_id` with CASCADE delete

### Relationships

- **One-to-One** with Lesson
- **One-to-Many** with Question
- **One-to-Many** with QuizAttempt

---

## 10. Question

**Purpose**: Individual quiz questions

### Schema

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| id | UUID | PRIMARY KEY | Unique question identifier |
| quiz_id | UUID | FOREIGN KEY (quizzes.id), NOT NULL | Parent quiz |
| question_text | TEXT | NOT NULL | Question prompt |
| question_type | ENUM | NOT NULL | multiple_choice, true_false |
| options | JSONB | NOT NULL | Answer options array |
| correct_answer | VARCHAR(255) | NOT NULL | Correct option key/value |
| explanation | TEXT | NULLABLE | Explanation for correct answer |
| order | INTEGER | NOT NULL | Display order in quiz |
| created_at | TIMESTAMP | NOT NULL | Question creation date |
| updated_at | TIMESTAMP | NOT NULL | Last modification date |

### Options Field Structure

```json
{
  "A": "Option 1 text",
  "B": "Option 2 text",
  "C": "Option 3 text",
  "D": "Option 4 text"
}
```

### Validation Rules

- **question_text**: Required, min 10 characters
- **question_type**: One of: multiple_choice, true_false
- **options**: For multiple_choice: 2-6 options; For true_false: 2 options (True/False)
- **correct_answer**: Must be a key in options object
- **order**: Positive integer, unique per quiz

### Indexes

- PRIMARY KEY on `id`
- INDEX on `quiz_id, order`
- FOREIGN KEY on `quiz_id` with CASCADE delete

### Relationships

- **Many-to-One** with Quiz

---

## 11. QuizAttempt

**Purpose**: Records student quiz submissions and scores

### Schema

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| id | UUID | PRIMARY KEY | Unique attempt identifier |
| enrollment_id | UUID | FOREIGN KEY (enrollments.id), NOT NULL | Student enrollment |
| quiz_id | UUID | FOREIGN KEY (quizzes.id), NOT NULL | Attempted quiz |
| attempt_number | INTEGER | NOT NULL | Attempt count (1, 2, 3...) |
| answers | JSONB | NOT NULL | Student answers |
| score | DECIMAL(5,2) | NOT NULL | Score percentage (0-100) |
| is_passed | BOOLEAN | NOT NULL | Passed flag (score >= passing_score) |
| submitted_at | TIMESTAMP | NOT NULL | Submission timestamp |
| created_at | TIMESTAMP | NOT NULL | Attempt start date |

### Answers Field Structure

```json
{
  "question_uuid_1": "A",
  "question_uuid_2": "C",
  "question_uuid_3": "B"
}
```

### Validation Rules

- **attempt_number**: Auto-increment per enrollment+quiz combination
- **score**: Calculated as `(correct_answers / total_questions) * 100`
- **is_passed**: `score >= quiz.passing_score`

### Indexes

- PRIMARY KEY on `id`
- INDEX on `enrollment_id, quiz_id, attempt_number`
- INDEX on `quiz_id` (quiz analytics)
- FOREIGN KEY on `enrollment_id` with CASCADE delete
- FOREIGN KEY on `quiz_id` with CASCADE delete

### Relationships

- **Many-to-One** with Enrollment
- **Many-to-One** with Quiz

### Business Logic

- **Limit retakes**: If `quiz.allow_retake = false`, only allow 1 attempt
- **Mark lesson complete**: If `is_passed = true`, mark associated lesson as complete in Progress

---

## 12. Discussion

**Purpose**: Course Q&A posts

### Schema

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| id | UUID | PRIMARY KEY | Unique discussion identifier |
| course_id | UUID | FOREIGN KEY (courses.id), NOT NULL | Parent course |
| lesson_id | UUID | FOREIGN KEY (lessons.id), NULLABLE | Specific lesson context |
| user_id | UUID | FOREIGN KEY (users.id), NOT NULL | Question author |
| question | TEXT | NOT NULL | Question text |
| upvotes | INTEGER | NOT NULL, DEFAULT 0 | Upvote count |
| is_answered | BOOLEAN | NOT NULL, DEFAULT false | Has accepted/instructor answer |
| created_at | TIMESTAMP | NOT NULL | Post creation date |
| updated_at | TIMESTAMP | NOT NULL | Last modification date |

### Validation Rules

- **question**: Required, min 10 characters, max 1000 characters
- **User must be enrolled**: Check enrollment exists

### Indexes

- PRIMARY KEY on `id`
- INDEX on `course_id, created_at` (recent discussions)
- INDEX on `lesson_id` (lesson-specific Q&A)
- INDEX on `user_id` (user's questions)
- INDEX on `is_answered` (unanswered filter)
- FOREIGN KEY on `course_id` with CASCADE delete

### Relationships

- **Many-to-One** with Course
- **Many-to-One** with Lesson (optional)
- **Many-to-One** with User (author)
- **One-to-Many** with Reply

---

## 13. Reply

**Purpose**: Answers to discussion questions

### Schema

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| id | UUID | PRIMARY KEY | Unique reply identifier |
| discussion_id | UUID | FOREIGN KEY (discussions.id), NOT NULL | Parent question |
| user_id | UUID | FOREIGN KEY (users.id), NOT NULL | Reply author |
| reply_text | TEXT | NOT NULL | Answer text |
| upvotes | INTEGER | NOT NULL, DEFAULT 0 | Upvote count |
| is_instructor_reply | BOOLEAN | NOT NULL, DEFAULT false | Instructor badge |
| created_at | TIMESTAMP | NOT NULL | Reply creation date |
| updated_at | TIMESTAMP | NOT NULL | Last modification date |

### Validation Rules

- **reply_text**: Required, min 10 characters, max 1000 characters
- **is_instructor_reply**: Auto-set if `user.role = instructor` AND user owns course

### Indexes

- PRIMARY KEY on `id`
- INDEX on `discussion_id, created_at` (ordered replies)
- INDEX on `user_id` (user's replies)
- INDEX on `is_instructor_reply` (instructor responses)
- FOREIGN KEY on `discussion_id` with CASCADE delete

### Relationships

- **Many-to-One** with Discussion
- **Many-to-One** with User (author)

---

## 14. Certificate

**Purpose**: Course completion certificates

### Schema

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| id | UUID | PRIMARY KEY | Unique certificate identifier |
| enrollment_id | UUID | FOREIGN KEY (enrollments.id), NOT NULL | Completed enrollment |
| user_id | UUID | FOREIGN KEY (users.id), NOT NULL | Certificate recipient |
| course_id | UUID | FOREIGN KEY (courses.id), NOT NULL | Completed course |
| verification_code | VARCHAR(32) | UNIQUE, NOT NULL | Public verification code |
| certificate_url | VARCHAR(255) | NULLABLE | S3 URL to PDF certificate |
| issued_at | TIMESTAMP | NOT NULL | Issue date |
| created_at | TIMESTAMP | NOT NULL | Record creation date |

### Validation Rules

- **enrollment_id**: Unique (one certificate per enrollment)
- **verification_code**: Unique, auto-generated (random 32-char alphanumeric)
- **enrollment.is_completed**: Must be true before issuing certificate

### Indexes

- PRIMARY KEY on `id`
- UNIQUE INDEX on `enrollment_id`
- UNIQUE INDEX on `verification_code` (public verification)
- INDEX on `user_id` (user's certificates)
- INDEX on `course_id` (course completions)
- FOREIGN KEY on `enrollment_id` with CASCADE delete

### Relationships

- **One-to-One** with Enrollment
- **Many-to-One** with User
- **Many-to-One** with Course

### Business Logic

- **Auto-issue**: When `enrollment.is_completed` set to true, create certificate
- **PDF Generation**: Laravel job generates PDF with student name, course title, completion date, verification code
- **Public Verification**: Endpoint `/api/v1/certificates/verify/{code}` returns certificate details

---

## Database Migrations Order

Execute migrations in this order to satisfy foreign key dependencies:

1. `users` (no dependencies)
2. `courses` (depends on users)
3. `sections` (depends on courses)
4. `lessons` (depends on sections)
5. `quizzes` (depends on lessons)
6. `questions` (depends on quizzes)
7. `enrollments` (depends on users, courses)
8. `progress` (depends on enrollments, lessons)
9. `quiz_attempts` (depends on enrollments, quizzes)
10. `transactions` (depends on users, courses)
11. `reviews` (depends on users, courses)
12. `discussions` (depends on courses, lessons, users)
13. `replies` (depends on discussions, users)
14. `certificates` (depends on enrollments, users, courses)

---

## Summary Statistics

- **Total Entities**: 14
- **Total Relationships**: 25
- **Normalized**: 3NF (Third Normal Form)
- **Soft Deletes**: User, Course
- **Timestamps**: All entities have `created_at`, `updated_at`
- **UUID Primary Keys**: All entities (prevents enumeration, enables distributed systems)

All entities support the 9 user stories defined in the feature specification and align with the constitution's requirements for security, scalability, and data integrity.

