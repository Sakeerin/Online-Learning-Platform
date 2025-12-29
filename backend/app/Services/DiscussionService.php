<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Discussion;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Reply;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DiscussionService
{
    /**
     * Create a new discussion/question for a course.
     */
    public function create(User $user, Course $course, array $data): Discussion
    {
        return DB::transaction(function () use ($user, $course, $data) {
            // Check if user is enrolled
            $enrollment = Enrollment::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->first();

            if (!$enrollment) {
                throw new \Exception('You must be enrolled in this course to post a question.');
            }

            // Validate lesson_id if provided
            $lessonId = $data['lesson_id'] ?? null;
            if ($lessonId) {
                $lesson = Lesson::findOrFail($lessonId);
                // Verify lesson belongs to course
                if ($lesson->section->course_id !== $course->id) {
                    throw new \Exception('Lesson does not belong to this course.');
                }
            }

            // Create discussion
            $discussion = Discussion::create([
                'course_id' => $course->id,
                'lesson_id' => $lessonId,
                'user_id' => $user->id,
                'question' => $data['question'],
                'upvotes' => 0,
                'is_answered' => false,
            ]);

            return $discussion->load(['user', 'course', 'lesson']);
        });
    }

    /**
     * Create a reply to a discussion.
     */
    public function createReply(User $user, Discussion $discussion, array $data): Reply
    {
        return DB::transaction(function () use ($user, $discussion, $data) {
            // Check if user is enrolled in the course
            $enrollment = Enrollment::where('user_id', $user->id)
                ->where('course_id', $discussion->course_id)
                ->first();

            if (!$enrollment) {
                throw new \Exception('You must be enrolled in this course to reply.');
            }

            // Determine if this is an instructor reply
            $isInstructorReply = false;
            if ($user->isInstructor()) {
                // Check if instructor owns the course
                if ($discussion->course->instructor_id === $user->id) {
                    $isInstructorReply = true;
                    // Mark discussion as answered if instructor replies
                    $discussion->markAsAnswered();
                }
            }

            // Create reply
            $reply = Reply::create([
                'discussion_id' => $discussion->id,
                'user_id' => $user->id,
                'reply_text' => $data['reply_text'],
                'upvotes' => 0,
                'is_instructor_reply' => $isInstructorReply,
            ]);

            return $reply->load(['user', 'discussion']);
        });
    }

    /**
     * Upvote a discussion.
     */
    public function upvoteDiscussion(User $user, Discussion $discussion): Discussion
    {
        // Note: In a production system, you'd want to track which users have upvoted
        // to prevent duplicate upvotes. For now, we'll just increment.
        $discussion->upvote();
        return $discussion->fresh(['user', 'course', 'lesson']);
    }

    /**
     * Upvote a reply.
     */
    public function upvoteReply(User $user, Reply $reply): Reply
    {
        // Note: In a production system, you'd want to track which users have upvoted
        // to prevent duplicate upvotes. For now, we'll just increment.
        $reply->upvote();
        return $reply->fresh(['user', 'discussion']);
    }

    /**
     * Get discussions for a course with filtering and pagination.
     */
    public function getCourseDiscussions(Course $course, array $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = Discussion::where('course_id', $course->id)
            ->with(['user:id,name,profile_photo', 'lesson:id,title']);

        // Filter by lesson
        if (isset($filters['lesson_id'])) {
            $query->where('lesson_id', $filters['lesson_id']);
        }

        // Filter by answered status
        if (isset($filters['is_answered'])) {
            $query->where('is_answered', filter_var($filters['is_answered'], FILTER_VALIDATE_BOOLEAN));
        }

        // Search by question text
        if (isset($filters['search'])) {
            $query->where('question', 'like', '%' . $filters['search'] . '%');
        }

        // Sort by upvotes or date
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';

        if ($sortBy === 'upvotes') {
            $query->orderBy('upvotes', $sortOrder);
        } else {
            $query->orderBy('created_at', $sortOrder);
        }

        $perPage = $filters['per_page'] ?? 10;

        return $query->paginate($perPage);
    }

    /**
     * Get a specific discussion with replies.
     */
    public function getDiscussionWithReplies(string $discussionId): Discussion
    {
        return Discussion::with([
            'user:id,name,profile_photo',
            'course:id,title',
            'lesson:id,title',
            'replies.user:id,name,profile_photo',
        ])
            ->findOrFail($discussionId);
    }

    /**
     * Get replies for a discussion.
     */
    public function getDiscussionReplies(Discussion $discussion, array $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = Reply::where('discussion_id', $discussion->id)
            ->with(['user:id,name,profile_photo']);

        // Filter by instructor replies
        if (isset($filters['instructor_only'])) {
            $query->where('is_instructor_reply', filter_var($filters['instructor_only'], FILTER_VALIDATE_BOOLEAN));
        }

        // Sort by upvotes or date
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';

        if ($sortBy === 'upvotes') {
            $query->orderBy('upvotes', $sortOrder);
        } else {
            $query->orderBy('created_at', $sortOrder);
        }

        $perPage = $filters['per_page'] ?? 20;

        return $query->paginate($perPage);
    }
}

