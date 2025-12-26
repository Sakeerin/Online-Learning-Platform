<?php

namespace App\Services;

use App\Events\LessonCompleted;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Progress;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Actions\CalculateProgressAction;
use Illuminate\Support\Facades\DB;

class QuizService
{
    public function __construct(
        private CalculateProgressAction $calculateProgressAction
    ) {}

    /**
     * Create a quiz with questions for a lesson.
     */
    public function create(Lesson $lesson, array $data): Quiz
    {
        return DB::transaction(function () use ($lesson, $data) {
            // Ensure lesson is of type quiz
            if ($lesson->type !== 'quiz') {
                throw new \Exception('Lesson must be of type quiz to create a quiz.');
            }

            // Check if quiz already exists
            if ($lesson->quiz) {
                throw new \Exception('Quiz already exists for this lesson.');
            }

            // Create quiz
            $quiz = Quiz::create([
                'lesson_id' => $lesson->id,
                'title' => $data['title'],
                'passing_score' => $data['passing_score'] ?? 80,
                'allow_retake' => $data['allow_retake'] ?? true,
            ]);

            // Create questions if provided
            if (isset($data['questions']) && is_array($data['questions'])) {
                foreach ($data['questions'] as $index => $questionData) {
                    Question::create([
                        'quiz_id' => $quiz->id,
                        'question_text' => $questionData['question_text'],
                        'question_type' => $questionData['question_type'],
                        'options' => $questionData['options'],
                        'correct_answer' => $questionData['correct_answer'],
                        'explanation' => $questionData['explanation'] ?? null,
                        'order' => $questionData['order'] ?? ($index + 1),
                    ]);
                }
            }

            return $quiz->load('questions');
        });
    }

    /**
     * Update a quiz and its questions.
     */
    public function update(Quiz $quiz, array $data): Quiz
    {
        return DB::transaction(function () use ($quiz, $data) {
            // Update quiz attributes
            if (isset($data['title'])) {
                $quiz->title = $data['title'];
            }
            if (isset($data['passing_score'])) {
                $quiz->passing_score = $data['passing_score'];
            }
            if (isset($data['allow_retake'])) {
                $quiz->allow_retake = $data['allow_retake'];
            }
            $quiz->save();

            // Update questions if provided
            if (isset($data['questions']) && is_array($data['questions'])) {
                // Delete existing questions
                $quiz->questions()->delete();

                // Create new questions
                foreach ($data['questions'] as $index => $questionData) {
                    Question::create([
                        'quiz_id' => $quiz->id,
                        'question_text' => $questionData['question_text'],
                        'question_type' => $questionData['question_type'],
                        'options' => $questionData['options'],
                        'correct_answer' => $questionData['correct_answer'],
                        'explanation' => $questionData['explanation'] ?? null,
                        'order' => $questionData['order'] ?? ($index + 1),
                    ]);
                }
            }

            return $quiz->fresh('questions');
        });
    }

    /**
     * Evaluate quiz answers and calculate score.
     * 
     * @param array $answers Array of question_id => answer pairs
     * @return array ['score' => float, 'correct' => int, 'total' => int, 'is_passed' => bool]
     */
    public function evaluate(Quiz $quiz, array $answers): array
    {
        $questions = $quiz->questions;
        $totalQuestions = $questions->count();
        $correctAnswers = 0;

        foreach ($questions as $question) {
            $studentAnswer = $answers[$question->id] ?? null;
            if ($studentAnswer && $question->isCorrect($studentAnswer)) {
                $correctAnswers++;
            }
        }

        // Calculate score: (correct / total) * 100
        $score = $totalQuestions > 0 ? ($correctAnswers / $totalQuestions) * 100 : 0;
        $isPassed = $score >= $quiz->passing_score;

        return [
            'score' => round($score, 2),
            'correct' => $correctAnswers,
            'total' => $totalQuestions,
            'is_passed' => $isPassed,
        ];
    }

    /**
     * Record a quiz attempt for a student.
     */
    public function record(Enrollment $enrollment, Quiz $quiz, array $answers): QuizAttempt
    {
        return DB::transaction(function () use ($enrollment, $quiz, $answers) {
            // Check if retake is allowed
            $existingAttempts = QuizAttempt::where('enrollment_id', $enrollment->id)
                ->where('quiz_id', $quiz->id)
                ->count();

            if (!$quiz->allow_retake && $existingAttempts > 0) {
                throw new \Exception('Retakes are not allowed for this quiz.');
            }

            // Calculate next attempt number
            $attemptNumber = $existingAttempts + 1;

            // Evaluate answers
            $evaluation = $this->evaluate($quiz, $answers);

            // Create attempt record
            $attempt = QuizAttempt::create([
                'enrollment_id' => $enrollment->id,
                'quiz_id' => $quiz->id,
                'attempt_number' => $attemptNumber,
                'answers' => $answers,
                'score' => $evaluation['score'],
                'is_passed' => $evaluation['is_passed'],
                'submitted_at' => now(),
            ]);

            // If passed, mark lesson as complete
            if ($evaluation['is_passed']) {
                $lesson = $quiz->lesson;
                $progress = Progress::firstOrCreate(
                    [
                        'enrollment_id' => $enrollment->id,
                        'lesson_id' => $lesson->id,
                    ],
                    [
                        'is_completed' => false,
                        'video_position' => 0,
                    ]
                );

                if (!$progress->is_completed) {
                    $progress->markCompleted();

                    // Dispatch event
                    event(new LessonCompleted($enrollment, $lesson));

                    // Recalculate enrollment progress
                    $this->calculateProgressAction->execute($enrollment);
                }
            }

            return $attempt->load(['quiz.questions', 'enrollment']);
        });
    }

    /**
     * Get quiz with questions for a student (without correct answers).
     */
    public function getQuizForStudent(Quiz $quiz): Quiz
    {
        $quiz->load('questions');
        
        // Remove correct_answer and explanation from questions for students
        $quiz->questions->each(function ($question) {
            unset($question->correct_answer);
            unset($question->explanation);
        });

        return $quiz;
    }

    /**
     * Get quiz attempt with detailed results.
     */
    public function getAttemptResults(QuizAttempt $attempt): array
    {
        $quiz = $attempt->quiz->load('questions');
        $answers = $attempt->answers;
        $results = [];

        foreach ($quiz->questions as $question) {
            $studentAnswer = $answers[$question->id] ?? null;
            $isCorrect = $studentAnswer && $question->isCorrect($studentAnswer);

            $results[] = [
                'question_id' => $question->id,
                'question_text' => $question->question_text,
                'question_type' => $question->question_type,
                'options' => $question->options,
                'student_answer' => $studentAnswer,
                'correct_answer' => $question->correct_answer,
                'is_correct' => $isCorrect,
                'explanation' => $question->explanation,
            ];
        }

        return [
            'attempt' => $attempt,
            'score' => $attempt->score,
            'is_passed' => $attempt->is_passed,
            'passing_score' => $quiz->passing_score,
            'results' => $results,
        ];
    }

    /**
     * Get all attempts for a student's enrollment.
     */
    public function getStudentAttempts(Enrollment $enrollment, Quiz $quiz): \Illuminate\Database\Eloquent\Collection
    {
        return QuizAttempt::where('enrollment_id', $enrollment->id)
            ->where('quiz_id', $quiz->id)
            ->orderBy('attempt_number', 'desc')
            ->get();
    }

    /**
     * Check if student can retake quiz.
     */
    public function canRetake(Enrollment $enrollment, Quiz $quiz): bool
    {
        if (!$quiz->allow_retake) {
            return false;
        }

        return true;
    }
}

