<?php

namespace App\Services;

use App\Events\StudentEnrolled;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class EnrollmentService
{
    /**
     * Enroll a student in a course.
     */
    public function enroll(User $student, Course $course): Enrollment
    {
        return DB::transaction(function () use ($student, $course) {
            // Check if already enrolled
            $existingEnrollment = Enrollment::where('user_id', $student->id)
                ->where('course_id', $course->id)
                ->first();

            if ($existingEnrollment) {
                return $existingEnrollment;
            }

            // Check if course is published
            if (!$course->isPublished()) {
                throw new \Exception('Cannot enroll in unpublished course.');
            }

            // Check if course requires payment
            if ($course->price > 0) {
                // Check for valid completed transaction
                $transaction = \App\Models\Transaction::where('user_id', $student->id)
                    ->where('course_id', $course->id)
                    ->where('status', 'completed')
                    ->first();

                if (!$transaction) {
                    throw new \Exception('Payment required to enroll in this course.');
                }
            }

            $enrollment = Enrollment::create([
                'user_id' => $student->id,
                'course_id' => $course->id,
                'enrolled_at' => now(),
                'last_accessed_at' => now(),
                'progress_percentage' => 0.00,
                'is_completed' => false,
            ]);

            // Update course enrollment count
            $course->increment('total_enrollments');

            // Dispatch event
            event(new StudentEnrolled($enrollment));

            return $enrollment->load(['course', 'student']);
        });
    }

    /**
     * Check if student has access to course.
     */
    public function checkAccess(User $student, Course $course): bool
    {
        // Check if course is published
        if (!$course->isPublished()) {
            return false;
        }

        // Check if course is free (preview lessons are always accessible)
        if ($course->price == 0) {
            return true;
        }

        // Check if student is enrolled
        return Enrollment::where('user_id', $student->id)
            ->where('course_id', $course->id)
            ->exists();
    }

    /**
     * Get enrollment for student and course.
     */
    public function getEnrollment(User $student, Course $course): ?Enrollment
    {
        return Enrollment::where('user_id', $student->id)
            ->where('course_id', $course->id)
            ->with(['course', 'progress.lesson'])
            ->first();
    }

    /**
     * Get all enrollments for a student.
     */
    public function getStudentEnrollments(User $student, array $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = Enrollment::where('user_id', $student->id)
            ->with(['course.instructor', 'course.sections'])
            ->latest('enrolled_at');

        if (isset($filters['is_completed'])) {
            $query->where('is_completed', $filters['is_completed']);
        }

        return $query->paginate($filters['per_page'] ?? 12);
    }
}

