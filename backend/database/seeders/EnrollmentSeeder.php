<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Database\Seeder;

class EnrollmentSeeder extends Seeder
{
    /**
     * Seed enrollments: each student enrolled in 2-4 courses with varied progress.
     */
    public function run(): void
    {
        $students = User::where('role', 'student')->get();
        $courses = Course::where('status', 'published')->get();

        // Define specific enrollment patterns per student for variety
        $enrollmentPatterns = [
            // student@example.com - 4 courses, 2 completed
            ['count' => 4, 'completed' => 2],
            // student2@example.com - 3 courses, 1 completed
            ['count' => 3, 'completed' => 1],
            // student3@example.com - 2 courses, 0 completed
            ['count' => 2, 'completed' => 0],
            // student4@example.com - 4 courses, 1 completed
            ['count' => 4, 'completed' => 1],
            // student5@example.com - 3 courses, 2 completed
            ['count' => 3, 'completed' => 2],
        ];

        foreach ($students as $index => $student) {
            $pattern = $enrollmentPatterns[$index] ?? ['count' => 2, 'completed' => 0];

            // Pick random courses for the student, avoiding duplicates
            $selectedCourses = $courses->random(min($pattern['count'], $courses->count()));

            foreach ($selectedCourses as $courseIndex => $course) {
                $isCompleted = $courseIndex < $pattern['completed'];
                $enrolledAt = now()->subDays(rand(14, 60));

                if ($isCompleted) {
                    Enrollment::create([
                        'user_id' => $student->id,
                        'course_id' => $course->id,
                        'enrolled_at' => $enrolledAt,
                        'last_accessed_at' => now()->subDays(rand(0, 5)),
                        'progress_percentage' => 100.00,
                        'is_completed' => true,
                        'completed_at' => $enrolledAt->copy()->addDays(rand(7, 30)),
                    ]);
                } else {
                    $progress = fake()->randomFloat(2, 10, 85);

                    Enrollment::create([
                        'user_id' => $student->id,
                        'course_id' => $course->id,
                        'enrolled_at' => $enrolledAt,
                        'last_accessed_at' => now()->subDays(rand(0, 7)),
                        'progress_percentage' => $progress,
                        'is_completed' => false,
                        'completed_at' => null,
                    ]);
                }

                // Update total_enrollments on the course
                $course->increment('total_enrollments');
            }
        }
    }
}
