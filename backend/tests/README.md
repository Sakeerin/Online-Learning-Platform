# Testing Guide for Phase 3: User Story 1

This directory contains integration and validation tests for the Instructor Course Creation feature.

## Running Tests

### Run all tests
```bash
php artisan test
```

### Run specific test suite
```bash
# Run all instructor tests
php artisan test --testsuite=Feature --filter=Instructor

# Run course creation tests
php artisan test tests/Feature/Instructor/CourseCreationTest.php

# Run section management tests
php artisan test tests/Feature/Instructor/SectionManagementTest.php

# Run lesson management tests
php artisan test tests/Feature/Instructor/LessonManagementTest.php
```

### Run specific test method
```bash
php artisan test --filter test_course_creation_flow_end_to_end
```

## Test Coverage

### T095: Course Creation Flow End-to-End
- **Test**: `test_course_creation_flow_end_to_end()`
- **Coverage**: Creates course → adds section → adds lesson → uploads video → publishes course
- **Verifies**: Complete workflow from creation to publication

### T096: Video Upload and Thumbnail Generation
- **Test**: `test_video_upload_and_thumbnail_generation()`
- **Coverage**: Video file upload, storage verification, metadata update
- **Verifies**: Video files are properly stored and accessible

### T097: Course Publishing Validation
- **Test**: `test_course_publishing_validation()`
- **Coverage**: 
  - Course without thumbnail cannot be published
  - Course without video lesson cannot be published
  - Course with short description cannot be published
  - Valid course can be published
- **Verifies**: All validation rules are enforced

### T098: Course Edit and Republish
- **Test**: `test_course_edit_and_republish_without_affecting_enrolled_students()`
- **Coverage**: Course editing, unpublishing, republishing
- **Verifies**: Published date remains unchanged, course can be edited and republished

## Additional Tests

### Authorization Tests
- Non-instructors cannot create courses
- Instructors can only edit their own courses

### Section Management Tests
- Create, update, delete sections
- Reorder sections

### Lesson Management Tests
- Create, update, delete lessons
- Upload videos to lessons
- Reorder lessons
- Video upload validation

## Test Data

Tests use Laravel factories to generate test data:
- `UserFactory` - Creates test users (instructors/students)
- `CourseFactory` - Creates test courses
- `SectionFactory` - Creates test sections
- `LessonFactory` - Creates test lessons

## Environment Setup

Tests run in a separate testing environment:
- Database: Uses in-memory SQLite or test database
- Storage: Uses fake storage driver
- Queue: Uses sync driver (no background jobs)
- Cache: Uses array driver

## Notes

- All tests use `RefreshDatabase` trait to ensure clean state
- Authentication is handled via Sanctum tokens
- File uploads use Laravel's `UploadedFile::fake()`
- Storage uses `Storage::fake()` for testing file operations

