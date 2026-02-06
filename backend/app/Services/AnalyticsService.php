<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Progress;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    /**
     * Get overall analytics for an instructor.
     */
    public function getInstructorAnalytics(User $instructor, array $filters = []): array
    {
        $dateFrom = $filters['date_from'] ?? now()->subDays(30)->startOfDay();
        $dateTo = $filters['date_to'] ?? now()->endOfDay();

        // Get instructor's courses
        $courses = Course::where('instructor_id', $instructor->id)->pluck('id');

        return [
            'total_courses' => $courses->count(),
            'total_enrollments' => $this->getTotalEnrollments($courses, $dateFrom, $dateTo),
            'total_revenue' => $this->getTotalRevenue($courses, $dateFrom, $dateTo),
            'total_platform_fees' => $this->getTotalPlatformFees($courses, $dateFrom, $dateTo),
            'net_revenue' => $this->getNetRevenue($courses, $dateFrom, $dateTo),
            'enrollment_trends' => $this->getEnrollmentTrends($courses, $dateFrom, $dateTo),
            'revenue_trends' => $this->getRevenueTrends($courses, $dateFrom, $dateTo),
            'top_courses' => $this->getTopCourses($instructor, $dateFrom, $dateTo),
            'completion_rate' => $this->getOverallCompletionRate($courses),
        ];
    }

    /**
     * Get analytics for a specific course.
     */
    public function getCourseAnalytics(Course $course, User $instructor, array $filters = []): array
    {
        // Verify instructor owns the course
        if ($course->instructor_id !== $instructor->id) {
            throw new \Exception('You are not authorized to view analytics for this course.');
        }

        $dateFrom = $filters['date_from'] ?? now()->subDays(30)->startOfDay();
        $dateTo = $filters['date_to'] ?? now()->endOfDay();

        return [
            'course_id' => $course->id,
            'course_title' => $course->title,
            'total_enrollments' => $this->getCourseEnrollments($course, $dateFrom, $dateTo),
            'total_revenue' => $this->getCourseRevenue($course, $dateFrom, $dateTo),
            'completion_rate' => $this->getCourseCompletionRate($course),
            'average_rating' => $course->average_rating ?? 0,
            'total_reviews' => $course->total_reviews ?? 0,
            'lesson_analytics' => $this->getLessonAnalytics($course),
            'enrollment_trends' => $this->getCourseEnrollmentTrends($course, $dateFrom, $dateTo),
            'student_demographics' => $this->getStudentDemographics($course),
        ];
    }

    /**
     * Get lesson analytics for a course.
     */
    public function getLessonAnalytics(Course $course): array
    {
        $lessons = Lesson::whereHas('section', function ($query) use ($course) {
            $query->where('course_id', $course->id);
        })
            ->with('section')
            ->get();

        $analytics = [];

        foreach ($lessons as $lesson) {
            $enrollments = Enrollment::where('course_id', $course->id)->pluck('id');
            
            $progressRecords = Progress::whereIn('enrollment_id', $enrollments)
                ->where('lesson_id', $lesson->id)
                ->get();

            $totalViews = $progressRecords->count();
            $completedCount = $progressRecords->where('is_completed', true)->count();
            $completionRate = $totalViews > 0 ? ($completedCount / $totalViews) * 100 : 0;

            // Calculate average watch time (for video lessons)
            $averageWatchTime = 0;
            if ($lesson->type === 'video' && $lesson->duration) {
                $totalWatchTime = $progressRecords->sum('video_position');
                $averageWatchTime = $totalViews > 0 ? ($totalWatchTime / $totalViews) : 0;
                $averageWatchTime = min($averageWatchTime, $lesson->duration); // Cap at lesson duration
            }

            $analytics[] = [
                'lesson_id' => $lesson->id,
                'lesson_title' => $lesson->title,
                'lesson_type' => $lesson->type,
                'section_title' => $lesson->section->title,
                'total_views' => $totalViews,
                'completion_count' => $completedCount,
                'completion_rate' => round($completionRate, 2),
                'average_watch_time' => $averageWatchTime,
                'lesson_duration' => $lesson->duration ?? 0,
            ];
        }

        // Sort by completion rate descending
        usort($analytics, fn($a, $b) => $b['completion_rate'] <=> $a['completion_rate']);

        return $analytics;
    }

    /**
     * Get revenue breakdown for an instructor.
     */
    public function getRevenueBreakdown(User $instructor, array $filters = []): array
    {
        $dateFrom = $filters['date_from'] ?? now()->subDays(30)->startOfDay();
        $dateTo = $filters['date_to'] ?? now()->endOfDay();
        $groupBy = $filters['group_by'] ?? 'course'; // course, day, month

        $courses = Course::where('instructor_id', $instructor->id)->pluck('id');

        $transactions = Transaction::whereIn('course_id', $courses)
            ->where('status', 'completed')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->get();

        if ($groupBy === 'course') {
            return $this->getRevenueByCourse($transactions);
        } elseif ($groupBy === 'day') {
            return $this->getRevenueByDay($transactions);
        } elseif ($groupBy === 'month') {
            return $this->getRevenueByMonth($transactions);
        }

        return [];
    }

    /**
     * Get student demographics for a course.
     */
    public function getStudentDemographics(Course $course): array
    {
        $enrollments = Enrollment::where('course_id', $course->id)
            ->with('student')
            ->get();

        $totalStudents = $enrollments->count();

        if ($totalStudents === 0) {
            return [
                'total_students' => 0,
                'by_country' => [],
                'by_enrollment_date' => [],
            ];
        }

        // Group by country (if available in user model)
        $byCountry = $enrollments->groupBy(function ($enrollment) {
            // For now, we'll use a placeholder since country might not be in user model
            return 'Unknown';
        })->map(function ($group) {
            return $group->count();
        })->toArray();

        // Group by enrollment month
        $byEnrollmentDate = $enrollments->groupBy(function ($enrollment) {
            return $enrollment->enrolled_at->format('Y-m');
        })->map(function ($group) {
            return $group->count();
        })->toArray();

        return [
            'total_students' => $totalStudents,
            'by_country' => $byCountry,
            'by_enrollment_date' => $byEnrollmentDate,
        ];
    }

    // Private helper methods

    private function getTotalEnrollments($courseIds, $dateFrom, $dateTo): int
    {
        return Enrollment::whereIn('course_id', $courseIds)
            ->whereBetween('enrolled_at', [$dateFrom, $dateTo])
            ->count();
    }

    private function getTotalRevenue($courseIds, $dateFrom, $dateTo): float
    {
        return Transaction::whereIn('course_id', $courseIds)
            ->where('status', 'completed')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->sum('amount');
    }

    private function getTotalPlatformFees($courseIds, $dateFrom, $dateTo): float
    {
        return Transaction::whereIn('course_id', $courseIds)
            ->where('status', 'completed')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->sum('platform_fee');
    }

    private function getNetRevenue($courseIds, $dateFrom, $dateTo): float
    {
        return Transaction::whereIn('course_id', $courseIds)
            ->where('status', 'completed')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->sum('instructor_revenue');
    }

    private function getEnrollmentTrends($courseIds, $dateFrom, $dateTo): array
    {
        $enrollments = Enrollment::whereIn('course_id', $courseIds)
            ->whereBetween('enrolled_at', [$dateFrom, $dateTo])
            ->selectRaw('DATE(enrolled_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return $enrollments->map(function ($item) {
            return [
                'date' => $item->date,
                'count' => $item->count,
            ];
        })->toArray();
    }

    private function getRevenueTrends($courseIds, $dateFrom, $dateTo): array
    {
        $transactions = Transaction::whereIn('course_id', $courseIds)
            ->where('status', 'completed')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->selectRaw('DATE(created_at) as date, SUM(instructor_revenue) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return $transactions->map(function ($item) {
            return [
                'date' => $item->date,
                'revenue' => (float) $item->revenue,
            ];
        })->toArray();
    }

    private function getTopCourses(User $instructor, $dateFrom, $dateTo, $limit = 5): array
    {
        $courses = Course::where('instructor_id', $instructor->id)
            ->withCount(['enrollments' => function ($query) use ($dateFrom, $dateTo) {
                $query->whereBetween('enrolled_at', [$dateFrom, $dateTo]);
            }])
            ->withSum(['transactions' => function ($query) use ($dateFrom, $dateTo) {
                $query->where('status', 'completed')
                    ->whereBetween('created_at', [$dateFrom, $dateTo]);
            }], 'instructor_revenue')
            ->orderBy('enrollments_count', 'desc')
            ->limit($limit)
            ->get();

        return $courses->map(function ($course) {
            return [
                'course_id' => $course->id,
                'course_title' => $course->title,
                'enrollments' => $course->enrollments_count ?? 0,
                'revenue' => (float) ($course->transactions_sum_instructor_revenue ?? 0),
            ];
        })->toArray();
    }

    private function getOverallCompletionRate($courseIds): float
    {
        $totalEnrollments = Enrollment::whereIn('course_id', $courseIds)->count();
        $completedEnrollments = Enrollment::whereIn('course_id', $courseIds)
            ->where('is_completed', true)
            ->count();

        if ($totalEnrollments === 0) {
            return 0;
        }

        return round(($completedEnrollments / $totalEnrollments) * 100, 2);
    }

    private function getCourseEnrollments(Course $course, $dateFrom, $dateTo): int
    {
        return Enrollment::where('course_id', $course->id)
            ->whereBetween('enrolled_at', [$dateFrom, $dateTo])
            ->count();
    }

    private function getCourseRevenue(Course $course, $dateFrom, $dateTo): float
    {
        return Transaction::where('course_id', $course->id)
            ->where('status', 'completed')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->sum('instructor_revenue');
    }

    private function getCourseCompletionRate(Course $course): float
    {
        $totalEnrollments = Enrollment::where('course_id', $course->id)->count();
        $completedEnrollments = Enrollment::where('course_id', $course->id)
            ->where('is_completed', true)
            ->count();

        if ($totalEnrollments === 0) {
            return 0;
        }

        return round(($completedEnrollments / $totalEnrollments) * 100, 2);
    }

    private function getCourseEnrollmentTrends(Course $course, $dateFrom, $dateTo): array
    {
        $enrollments = Enrollment::where('course_id', $course->id)
            ->whereBetween('enrolled_at', [$dateFrom, $dateTo])
            ->selectRaw('DATE(enrolled_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return $enrollments->map(function ($item) {
            return [
                'date' => $item->date,
                'count' => $item->count,
            ];
        })->toArray();
    }

    private function getRevenueByCourse($transactions): array
    {
        return $transactions->groupBy('course_id')
            ->map(function ($group, $courseId) {
                $course = Course::find($courseId);
                return [
                    'course_id' => $courseId,
                    'course_title' => $course?->title ?? 'Unknown',
                    'total_revenue' => (float) $group->sum('instructor_revenue'),
                    'total_amount' => (float) $group->sum('amount'),
                    'platform_fees' => (float) $group->sum('platform_fee'),
                    'transaction_count' => $group->count(),
                ];
            })
            ->values()
            ->toArray();
    }

    private function getRevenueByDay($transactions): array
    {
        return $transactions->groupBy(function ($transaction) {
            return $transaction->created_at->format('Y-m-d');
        })
            ->map(function ($group, $date) {
                return [
                    'date' => $date,
                    'revenue' => (float) $group->sum('instructor_revenue'),
                    'amount' => (float) $group->sum('amount'),
                    'platform_fees' => (float) $group->sum('platform_fee'),
                    'transaction_count' => $group->count(),
                ];
            })
            ->values()
            ->toArray();
    }

    private function getRevenueByMonth($transactions): array
    {
        return $transactions->groupBy(function ($transaction) {
            return $transaction->created_at->format('Y-m');
        })
            ->map(function ($group, $month) {
                return [
                    'month' => $month,
                    'revenue' => (float) $group->sum('instructor_revenue'),
                    'amount' => (float) $group->sum('amount'),
                    'platform_fees' => (float) $group->sum('platform_fee'),
                    'transaction_count' => $group->count(),
                ];
            })
            ->values()
            ->toArray();
    }
}

