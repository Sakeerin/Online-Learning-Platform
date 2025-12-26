<?php

namespace App\Http\Controllers\Api\V1\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubmitQuizRequest;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Services\EnrollmentService;
use App\Services\QuizService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function __construct(
        private QuizService $quizService,
        private EnrollmentService $enrollmentService
    ) {}

    /**
     * Get quiz for student (without correct answers).
     */
    public function show(Request $request, string $courseId, string $lessonId): JsonResponse
    {
        $course = Course::findOrFail($courseId);
        $lesson = Lesson::findOrFail($lessonId);

        // Verify enrollment
        if (!$this->enrollmentService->checkAccess($request->user(), $course)) {
            return response()->json([
                'message' => 'You must be enrolled in this course to access this quiz.',
            ], 403);
        }

        // Verify lesson belongs to course
        $section = $lesson->section;
        if ($section->course_id !== $course->id) {
            return response()->json([
                'message' => 'Lesson not found in this course.',
            ], 404);
        }

        // Verify lesson is a quiz
        if (!$lesson->isQuiz()) {
            return response()->json([
                'message' => 'This lesson is not a quiz.',
            ], 422);
        }

        // Get quiz
        $quiz = $lesson->quiz;
        if (!$quiz) {
            return response()->json([
                'message' => 'Quiz not found for this lesson.',
            ], 404);
        }

        // Get quiz without correct answers for student
        $quizForStudent = $this->quizService->getQuizForStudent($quiz);

        return response()->json([
            'data' => $quizForStudent,
        ]);
    }

    /**
     * Submit quiz answers.
     */
    public function submit(SubmitQuizRequest $request, string $courseId, string $lessonId): JsonResponse
    {
        $course = Course::findOrFail($courseId);
        $lesson = Lesson::findOrFail($lessonId);
        $user = $request->user();

        // Verify enrollment
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if (!$enrollment) {
            return response()->json([
                'message' => 'You must be enrolled in this course to take this quiz.',
            ], 403);
        }

        // Verify lesson belongs to course
        $section = $lesson->section;
        if ($section->course_id !== $course->id) {
            return response()->json([
                'message' => 'Lesson not found in this course.',
            ], 404);
        }

        // Verify lesson is a quiz
        if (!$lesson->isQuiz()) {
            return response()->json([
                'message' => 'This lesson is not a quiz.',
            ], 422);
        }

        // Get quiz
        $quiz = $lesson->quiz;
        if (!$quiz) {
            return response()->json([
                'message' => 'Quiz not found for this lesson.',
            ], 404);
        }

        try {
            // Record attempt
            $attempt = $this->quizService->record($enrollment, $quiz, $request->input('answers'));

            // Get detailed results
            $results = $this->quizService->getAttemptResults($attempt);

            return response()->json([
                'message' => 'Quiz submitted successfully',
                'data' => $results,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Get quiz attempt results.
     */
    public function results(Request $request, string $courseId, string $lessonId, string $attemptId): JsonResponse
    {
        $course = Course::findOrFail($courseId);
        $lesson = Lesson::findOrFail($lessonId);
        $user = $request->user();

        // Verify enrollment
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if (!$enrollment) {
            return response()->json([
                'message' => 'You must be enrolled in this course to view quiz results.',
            ], 403);
        }

        // Get attempt
        $attempt = \App\Models\QuizAttempt::where('id', $attemptId)
            ->where('enrollment_id', $enrollment->id)
            ->firstOrFail();

        // Verify attempt belongs to lesson's quiz
        if ($attempt->quiz->lesson_id !== $lesson->id) {
            return response()->json([
                'message' => 'Attempt not found for this quiz.',
            ], 404);
        }

        // Get detailed results
        $results = $this->quizService->getAttemptResults($attempt);

        return response()->json([
            'data' => $results,
        ]);
    }

    /**
     * Get all attempts for a quiz.
     */
    public function attempts(Request $request, string $courseId, string $lessonId): JsonResponse
    {
        $course = Course::findOrFail($courseId);
        $lesson = Lesson::findOrFail($lessonId);
        $user = $request->user();

        // Verify enrollment
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if (!$enrollment) {
            return response()->json([
                'message' => 'You must be enrolled in this course to view quiz attempts.',
            ], 403);
        }

        // Verify lesson is a quiz
        if (!$lesson->isQuiz()) {
            return response()->json([
                'message' => 'This lesson is not a quiz.',
            ], 422);
        }

        // Get quiz
        $quiz = $lesson->quiz;
        if (!$quiz) {
            return response()->json([
                'message' => 'Quiz not found for this lesson.',
            ], 404);
        }

        // Get all attempts
        $attempts = $this->quizService->getStudentAttempts($enrollment, $quiz);

        return response()->json([
            'data' => $attempts,
        ]);
    }

    /**
     * Check if student can retake quiz.
     */
    public function canRetake(Request $request, string $courseId, string $lessonId): JsonResponse
    {
        $course = Course::findOrFail($courseId);
        $lesson = Lesson::findOrFail($lessonId);
        $user = $request->user();

        // Verify enrollment
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if (!$enrollment) {
            return response()->json([
                'message' => 'You must be enrolled in this course.',
            ], 403);
        }

        // Get quiz
        $quiz = $lesson->quiz;
        if (!$quiz) {
            return response()->json([
                'message' => 'Quiz not found for this lesson.',
            ], 404);
        }

        $canRetake = $this->quizService->canRetake($enrollment, $quiz);

        return response()->json([
            'can_retake' => $canRetake,
        ]);
    }
}

