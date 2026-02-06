<?php

namespace App\Services;

use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
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
            CacheService::invalidateCourse($course->id);

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
                throw new \App\Exceptions\CourseNotPublishableException();
            }

            $course->update([
                'status' => 'published',
                'published_at' => $course->published_at ?? now(),
            ]);

            CacheService::invalidateCourse($course->id);

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

            CacheService::invalidateCourse($course->id);

            return $course->fresh(['instructor', 'sections.lessons']);
        });
    }

    /**
     * Delete a course.
     */
    public function delete(Course $course): bool
    {
        return DB::transaction(function () use ($course) {
            $courseId = $course->id;
            $result = $course->delete();
            CacheService::invalidateCourse($courseId);
            return $result;
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

    /**
     * Browse published courses with filters and pagination.
     */
    public function browseCourses(array $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        // T107: Cache key excludes pagination to cache the query, not the paginated results
        // Note: Paginated results are not cached directly, but query optimization is applied
        $query = Course::where('status', 'published')
            ->with(['instructor']) // T106: Eager load instructor to prevent N+1
            ->withCount(['sections']);

        // Filter by category
        if (isset($filters['category']) && !empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        // Filter by subcategory
        if (isset($filters['subcategory']) && !empty($filters['subcategory'])) {
            $query->where('subcategory', $filters['subcategory']);
        }

        // Filter by difficulty level
        if (isset($filters['difficulty_level']) && !empty($filters['difficulty_level'])) {
            $query->where('difficulty_level', $filters['difficulty_level']);
        }

        // Filter by price range
        if (isset($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }
        if (isset($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        // Filter free courses
        if (isset($filters['free_only']) && $filters['free_only']) {
            $query->where('price', 0);
        }

        // Filter by rating
        if (isset($filters['min_rating'])) {
            $query->where('average_rating', '>=', $filters['min_rating']);
        }

        // Sort by
        $sortBy = $filters['sort_by'] ?? 'relevance';
        switch ($sortBy) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'rating':
                $query->orderBy('average_rating', 'desc');
                break;
            case 'enrollments':
                $query->orderBy('total_enrollments', 'desc');
                break;
            case 'newest':
                $query->orderBy('published_at', 'desc');
                break;
            case 'relevance':
            default:
                // T105: Ranking algorithm (relevance, rating, enrollments)
                if (config('database.default') === 'pgsql') {
                    $query->orderByRaw('
                        (COALESCE(average_rating, 0) * 0.4 + 
                         (total_enrollments / 100.0) * 0.3 + 
                         (CASE WHEN published_at > NOW() - INTERVAL \'30 days\' THEN 1 ELSE 0 END) * 0.3) DESC
                    ')
                    ->orderBy('published_at', 'desc');
                } else {
                    // Fallback for MySQL/SQLite
                    $query->orderBy('average_rating', 'desc')
                        ->orderBy('total_enrollments', 'desc')
                        ->orderBy('published_at', 'desc');
                }
                break;
        }

        return $query->paginate($filters['per_page'] ?? 12);
    }

    /**
     * Search courses using full-text search.
     */
    public function searchCourses(string $query, array $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        // T107: Note: Paginated results are not cached directly, but query optimization is applied
        $dbQuery = Course::where('status', 'published')
            ->with(['instructor']) // T106: Eager load instructor
            ->withCount(['sections']);

        // PostgreSQL full-text search
        if (config('database.default') === 'pgsql') {
            $dbQuery->whereRaw("
                to_tsvector('english', coalesce(title, '') || ' ' || coalesce(description, '')) 
                @@ plainto_tsquery('english', ?)
            ", [$query]);
        } else {
            // Fallback for MySQL/SQLite
            $dbQuery->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhere('subtitle', 'like', "%{$query}%");
            });
        }

        // Apply filters
        if (isset($filters['category']) && !empty($filters['category'])) {
            $dbQuery->where('category', $filters['category']);
        }
        if (isset($filters['difficulty_level']) && !empty($filters['difficulty_level'])) {
            $dbQuery->where('difficulty_level', $filters['difficulty_level']);
        }
        if (isset($filters['min_price'])) {
            $dbQuery->where('price', '>=', $filters['min_price']);
        }
        if (isset($filters['max_price'])) {
            $dbQuery->where('price', '<=', $filters['max_price']);
        }
        if (isset($filters['min_rating'])) {
            $dbQuery->where('average_rating', '>=', $filters['min_rating']);
        }

        // Sort by relevance (full-text search ranking)
        if (config('database.default') === 'pgsql') {
            $dbQuery->orderByRaw("
                ts_rank(
                    to_tsvector('english', coalesce(title, '') || ' ' || coalesce(description, '')),
                    plainto_tsquery('english', ?)
                ) DESC
            ", [$query])
            ->orderBy('average_rating', 'desc')
            ->orderBy('total_enrollments', 'desc');
        } else {
            $dbQuery->orderBy('average_rating', 'desc')
                ->orderBy('total_enrollments', 'desc');
        }

        return $dbQuery->paginate($filters['per_page'] ?? 12);
    }

    /**
     * Get featured courses (top rated, most enrolled).
     */
    public function getFeaturedCourses(int $limit = 6): \Illuminate\Database\Eloquent\Collection
    {
        // T107: Cache featured courses for 1 hour
        $cacheKey = 'courses:featured:' . $limit;
        
        return Cache::remember($cacheKey, 3600, function () use ($limit) {
            if (config('database.default') === 'pgsql') {
                return Course::where('status', 'published')
                    ->with(['instructor'])
                    ->orderByRaw('(COALESCE(average_rating, 0) * 0.5 + total_enrollments * 0.5) DESC')
                    ->limit($limit)
                    ->get();
            } else {
                return Course::where('status', 'published')
                    ->with(['instructor'])
                    ->orderBy('average_rating', 'desc')
                    ->orderBy('total_enrollments', 'desc')
                    ->limit($limit)
                    ->get();
            }
        });
    }

    /**
     * Get course by ID for public viewing.
     */
    public function getPublicCourse(string $courseId): ?Course
    {
        return Cache::remember("course:public:{$courseId}", CacheService::TTL_MEDIUM, function () use ($courseId) {
            return Course::where('id', $courseId)
                ->where('status', 'published')
                ->with(['instructor', 'sections.lessons'])
                ->first();
        });
    }
}

