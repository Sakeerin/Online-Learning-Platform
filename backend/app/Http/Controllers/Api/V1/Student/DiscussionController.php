<?php

namespace App\Http\Controllers\Api\V1\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\DiscussionRequest;
use App\Http\Requests\ReplyRequest;
use App\Http\Resources\DiscussionResource;
use App\Http\Resources\ReplyResource;
use App\Models\Course;
use App\Models\Discussion;
use App\Models\Reply;
use App\Services\DiscussionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Student - Discussions
 * @authenticated
 *
 * Endpoints for course discussions, Q&A, replies, and upvoting.
 */
class DiscussionController extends Controller
{
    public function __construct(
        private DiscussionService $discussionService
    ) {}

    /**
     * Get all discussions for a course.
     */
    public function index(Request $request, string $courseId): JsonResponse
    {
        $course = Course::findOrFail($courseId);

        $filters = [
            'lesson_id' => $request->query('lesson_id'),
            'is_answered' => $request->query('is_answered'),
            'search' => $request->query('search'),
            'sort_by' => $request->query('sort_by', 'created_at'),
            'sort_order' => $request->query('sort_order', 'desc'),
            'per_page' => $request->query('per_page', 10),
        ];

        $discussions = $this->discussionService->getCourseDiscussions($course, $filters);

        return response()->json([
            'data' => DiscussionResource::collection($discussions->items()),
            'meta' => [
                'current_page' => $discussions->currentPage(),
                'last_page' => $discussions->lastPage(),
                'per_page' => $discussions->perPage(),
                'total' => $discussions->total(),
            ],
        ]);
    }

    /**
     * Get a specific discussion with replies.
     */
    public function show(string $courseId, string $discussionId): JsonResponse
    {
        $discussion = $this->discussionService->getDiscussionWithReplies($discussionId);

        // Verify discussion belongs to course
        if ($discussion->course_id !== $courseId) {
            return response()->json([
                'message' => 'Discussion not found for this course.',
            ], 404);
        }

        return response()->json([
            'data' => new DiscussionResource($discussion),
        ]);
    }

    /**
     * Create a new discussion/question.
     */
    public function store(DiscussionRequest $request, string $courseId): JsonResponse
    {
        $course = Course::findOrFail($courseId);

        try {
            $discussion = $this->discussionService->create(
                $request->user(),
                $course,
                $request->validated()
            );

            return response()->json([
                'message' => 'Question posted successfully',
                'data' => new DiscussionResource($discussion),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Upvote a discussion.
     */
    public function upvote(Request $request, string $courseId, string $discussionId): JsonResponse
    {
        $discussion = Discussion::findOrFail($discussionId);

        // Verify discussion belongs to course
        if ($discussion->course_id !== $courseId) {
            return response()->json([
                'message' => 'Discussion not found for this course.',
            ], 404);
        }

        try {
            $discussion = $this->discussionService->upvoteDiscussion($request->user(), $discussion);

            return response()->json([
                'message' => 'Discussion upvoted successfully',
                'data' => new DiscussionResource($discussion),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Get replies for a discussion.
     */
    public function replies(Request $request, string $courseId, string $discussionId): JsonResponse
    {
        $discussion = Discussion::findOrFail($discussionId);

        // Verify discussion belongs to course
        if ($discussion->course_id !== $courseId) {
            return response()->json([
                'message' => 'Discussion not found for this course.',
            ], 404);
        }

        $filters = [
            'instructor_only' => $request->query('instructor_only'),
            'sort_by' => $request->query('sort_by', 'created_at'),
            'sort_order' => $request->query('sort_order', 'desc'),
            'per_page' => $request->query('per_page', 20),
        ];

        $replies = $this->discussionService->getDiscussionReplies($discussion, $filters);

        return response()->json([
            'data' => ReplyResource::collection($replies->items()),
            'meta' => [
                'current_page' => $replies->currentPage(),
                'last_page' => $replies->lastPage(),
                'per_page' => $replies->perPage(),
                'total' => $replies->total(),
            ],
        ]);
    }

    /**
     * Create a reply to a discussion.
     */
    public function reply(ReplyRequest $request, string $courseId, string $discussionId): JsonResponse
    {
        $discussion = Discussion::findOrFail($discussionId);

        // Verify discussion belongs to course
        if ($discussion->course_id !== $courseId) {
            return response()->json([
                'message' => 'Discussion not found for this course.',
            ], 404);
        }

        try {
            $reply = $this->discussionService->createReply(
                $request->user(),
                $discussion,
                $request->validated()
            );

            return response()->json([
                'message' => 'Reply posted successfully',
                'data' => new ReplyResource($reply),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Upvote a reply.
     */
    public function upvoteReply(Request $request, string $courseId, string $discussionId, string $replyId): JsonResponse
    {
        $discussion = Discussion::findOrFail($discussionId);
        $reply = Reply::findOrFail($replyId);

        // Verify discussion belongs to course
        if ($discussion->course_id !== $courseId) {
            return response()->json([
                'message' => 'Discussion not found for this course.',
            ], 404);
        }

        // Verify reply belongs to discussion
        if ($reply->discussion_id !== $discussionId) {
            return response()->json([
                'message' => 'Reply not found for this discussion.',
            ], 404);
        }

        try {
            $reply = $this->discussionService->upvoteReply($request->user(), $reply);

            return response()->json([
                'message' => 'Reply upvoted successfully',
                'data' => new ReplyResource($reply),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}

