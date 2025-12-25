<?php

namespace App\Actions;

use App\Events\CourseCompleted;
use App\Models\Enrollment;
use Illuminate\Support\Facades\DB;

class CalculateProgressAction
{
    /**
     * Calculate and update enrollment progress percentage.
     */
    public function execute(Enrollment $enrollment): Enrollment
    {
        return DB::transaction(function () use ($enrollment) {
            $course = $enrollment->course;

            // Get total number of lessons in the course
            $totalLessons = $course->sections()
                ->withCount('lessons')
                ->get()
                ->sum('lessons_count');

            if ($totalLessons === 0) {
                $enrollment->update([
                    'progress_percentage' => 0.00,
                    'is_completed' => false,
                ]);
                return $enrollment->fresh();
            }

            // Get number of completed lessons
            $completedLessons = $enrollment->progress()
                ->where('is_completed', true)
                ->count();

            // Calculate percentage
            $progressPercentage = ($completedLessons / $totalLessons) * 100;

            // Update enrollment
            $isCompleted = $progressPercentage >= 100.00;
            $wasCompleted = $enrollment->is_completed;
            
            $enrollment->update([
                'progress_percentage' => round($progressPercentage, 2),
                'is_completed' => $isCompleted,
                'completed_at' => $isCompleted && !$enrollment->completed_at ? now() : $enrollment->completed_at,
            ]);

            // Dispatch CourseCompleted event if course just reached 100%
            if ($isCompleted && !$wasCompleted) {
                event(new CourseCompleted($enrollment->fresh()));
            }

            return $enrollment->fresh();
        });
    }
}

