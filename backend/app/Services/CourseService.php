<?php

namespace App\Services;

use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CourseService
{
    /**
     * Create a new course.
     */
    public function create(User $instructor, array $data): Course
    {
        return DB::transaction(function () use ($instructor, $data) {
            $course = Course::create([
                'instructor_id' => $instructor->id,
                'title' => $data['title'],
                'subtitle' => $data['subtitle'] ?? null,
                'description' => $data['description'],
                'learning_objectives' => $data['learning_objectives'] ?? null,
                'category' => $data['category'],
                'subcategory' => $data['subcategory'] ?? null,
                'difficulty_level' => $data['difficulty_level'],
                'thumbnail' => $data['thumbnail'] ?? null,
                'price' => $data['price'] ?? 0.00,
                'currency' => $data['currency'] ?? 'THB',
                'status' => 'draft',
            ]);

            return $course->load('instructor');
        });
    }

    /**
     * Update an existing course.
     */
    public function update(Course $course, array $data): Course
    {
        return DB::transaction(function () use ($course, $data) {
            $course->update(array_filter($data, fn ($value) => $value !== null));

            return $course->fresh(['instructor', 'sections.lessons']);
        });
    }

    /**
     * Publish a course.
     */
    public function publish(Course $course): Course
    {
        return DB::transaction(function () use ($course) {
            if (!$course->canBePublished()) {
                throw new \Exception('Course cannot be published. It must have at least one section with a video lesson, a thumbnail, and a description of at least 100 characters.');
            }

            $course->update([
                'status' => 'published',
                'published_at' => $course->published_at ?? now(),
            ]);

            return $course->fresh(['instructor', 'sections.lessons']);
        });
    }

    /**
     * Unpublish a course.
     */
    public function unpublish(Course $course): Course
    {
        return DB::transaction(function () use ($course) {
            $course->update([
                'status' => 'unpublished',
            ]);

            return $course->fresh(['instructor', 'sections.lessons']);
        });
    }

    /**
     * Delete a course.
     */
    public function delete(Course $course): bool
    {
        return DB::transaction(function () use ($course) {
            return $course->delete();
        });
    }

    /**
     * Get courses for an instructor.
     */
    public function getInstructorCourses(User $instructor, array $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = Course::where('instructor_id', $instructor->id)
            ->with(['instructor', 'sections'])
            ->latest();

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->paginate($filters['per_page'] ?? 15);
    }
}

