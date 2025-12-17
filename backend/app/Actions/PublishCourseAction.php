<?php

namespace App\Actions;

use App\Events\CoursePublished;
use App\Models\Course;
use App\Services\CourseService;
use Illuminate\Support\Facades\Log;

class PublishCourseAction
{
    public function __construct(
        private CourseService $courseService
    ) {}

    /**
     * Execute the action to publish a course.
     */
    public function execute(Course $course): Course
    {
        // Validate course can be published
        if (!$course->canBePublished()) {
            $errors = [];

            if (!$course->thumbnail) {
                $errors[] = 'Course must have a thumbnail image.';
            }

            if (empty($course->description) || strlen($course->description) < 100) {
                $errors[] = 'Course description must be at least 100 characters.';
            }

            $hasVideoLesson = $course->sections()
                ->whereHas('lessons', function ($query) {
                    $query->where('type', 'video');
                })
                ->exists();

            if (!$hasVideoLesson) {
                $errors[] = 'Course must have at least one section with a video lesson.';
            }

            throw new \Exception('Course cannot be published: ' . implode(' ', $errors));
        }

        try {
            $publishedCourse = $this->courseService->publish($course);

            // Dispatch event
            event(new CoursePublished($publishedCourse));

            Log::info('Course published', [
                'course_id' => $course->id,
                'instructor_id' => $course->instructor_id,
            ]);

            return $publishedCourse;
        } catch (\Exception $e) {
            Log::error('Failed to publish course', [
                'course_id' => $course->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}

