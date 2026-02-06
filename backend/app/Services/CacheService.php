<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CacheService
{
    /**
     * Cache TTLs in seconds.
     */
    const TTL_SHORT = 900;      // 15 minutes
    const TTL_MEDIUM = 1800;    // 30 minutes
    const TTL_LONG = 3600;      // 1 hour

    /**
     * Invalidate all cached data for a specific course.
     */
    public static function invalidateCourse(string $courseId): void
    {
        Cache::forget("course:public:{$courseId}");
        Cache::forget("course:rating:{$courseId}");
        Cache::forget('courses:categories');
        self::invalidateFeaturedCourses();
    }

    /**
     * Invalidate course rating cache.
     */
    public static function invalidateCourseRating(string $courseId): void
    {
        Cache::forget("course:rating:{$courseId}");
        self::invalidateFeaturedCourses();
    }

    /**
     * Invalidate featured courses cache.
     */
    public static function invalidateFeaturedCourses(): void
    {
        Cache::forget('courses:featured:6');
        Cache::forget('courses:featured:12');
    }

    /**
     * Invalidate instructor analytics cache.
     */
    public static function invalidateInstructorAnalytics(string $instructorId): void
    {
        Cache::forget("analytics:instructor:{$instructorId}");
    }
}
