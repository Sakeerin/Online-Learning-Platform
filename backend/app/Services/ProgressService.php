<?php

namespace App\Services;

use App\Actions\CalculateProgressAction;
use App\Events\LessonCompleted;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Progress;
use Illuminate\Support\Facades\DB;

class ProgressService
{
    public function __construct(
        private CalculateProgressAction $calculateProgressAction
    ) {}

    /**
     * Update video playback position for a lesson.
     */
    public function updatePosition(Enrollment $enrollment, Lesson $lesson, int $position): Progress
    {
        return DB::transaction(function () use ($enrollment, $lesson, $position) {
            // Validate position is within lesson duration
            if ($lesson->duration && $position > $lesson->duration) {
                $position = $lesson->duration;
            }

            $progress = Progress::firstOrCreate(
                [
                    'enrollment_id' => $enrollment->id,
                    'lesson_id' => $lesson->id,
                ],
                [
                    'is_completed' => false,
                    'video_position' => $position,
                ]
            );

            $progress->update(['video_position' => $position]);

            // T160: Auto-mark complete when video reaches 95% watched
            if ($lesson->duration && $position >= ($lesson->duration * 0.95)) {
                $this->markComplete($enrollment, $lesson);
            }

            return $progress->fresh();
        });
    }

    /**
     * Mark lesson as completed.
     */
    public function markComplete(Enrollment $enrollment, Lesson $lesson): Progress
    {
        return DB::transaction(function () use ($enrollment, $lesson) {
            $progress = Progress::firstOrCreate(
                [
                    'enrollment_id' => $enrollment->id,
                    'lesson_id' => $lesson->id,
                ],
                [
                    'is_completed' => false,
                    'video_position' => $lesson->duration ?? 0,
                ]
            );

            if (!$progress->is_completed) {
                $progress->markCompleted();

                // Dispatch event
                event(new LessonCompleted($enrollment, $lesson));

                // Recalculate enrollment progress
                $this->calculateProgressAction->execute($enrollment);
            }

            return $progress->fresh();
        });
    }

    /**
     * Get progress for a specific lesson.
     */
    public function getLessonProgress(Enrollment $enrollment, Lesson $lesson): ?Progress
    {
        return Progress::where('enrollment_id', $enrollment->id)
            ->where('lesson_id', $lesson->id)
            ->first();
    }

    /**
     * Get all progress for an enrollment.
     */
    public function getEnrollmentProgress(Enrollment $enrollment): \Illuminate\Database\Eloquent\Collection
    {
        return Progress::where('enrollment_id', $enrollment->id)
            ->with('lesson')
            ->get();
    }
}

