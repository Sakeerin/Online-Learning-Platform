<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Discussion;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Reply;
use App\Models\User;
use Illuminate\Database\Seeder;

class DiscussionSeeder extends Seeder
{
    /**
     * Seed 10-15 discussions with replies across published courses.
     */
    public function run(): void
    {
        $courses = Course::where('status', 'published')->with('sections.lessons')->get();
        $students = User::where('role', 'student')->get();
        $instructors = User::where('role', 'instructor')->get();

        $discussionQuestions = [
            'How do I debug this error when running the project locally?',
            'Can you explain the difference between these two approaches in more detail?',
            'What are the best practices for structuring a project of this size?',
            'I am getting unexpected results in the exercise. Is this the correct output?',
            'Could you recommend additional resources for learning this topic?',
            'How does this concept apply in a real production environment?',
            'What is the performance impact of using this technique at scale?',
            'Is there a simpler way to achieve the same result shown in this lesson?',
            'I noticed a discrepancy between the video and the documentation. Which is correct?',
            'How do you handle error handling and edge cases in this scenario?',
            'What version of the framework should I use to follow along with this course?',
            'Can you walk through the deployment process step by step?',
        ];

        $studentReplies = [
            'I had the same question! Following this thread for updates.',
            'I figured it out by checking the documentation. The key is to make sure your dependencies are up to date.',
            'Great question! I spent hours on this before finding the solution in the course resources section.',
            'Try restarting your development server. That fixed the same issue for me.',
            'This approach worked for me. Make sure to also check the configuration file.',
        ];

        $instructorReplies = [
            'Great question! The key difference is in how they handle state management. Let me break it down further in my response.',
            'Thanks for bringing this up. I have updated the lesson notes to clarify this point. The correct approach is described in the documentation.',
            'This is a common challenge. The best practice is to follow the repository pattern as demonstrated in Section 2. Let me know if you need more clarification.',
            'I appreciate you catching that discrepancy. The video is correct and I will update the written materials to match. In the meantime, follow the video instructions.',
            'For production environments, you would want to add caching and optimize the queries as shown in the advanced section. I will add a supplementary video on this topic.',
            'Good catch! Make sure you are using the version specified in the course requirements. I have pinned the compatible versions in the README.',
        ];

        $discussionCount = 0;
        $targetDiscussions = 12;

        foreach ($courses as $course) {
            if ($discussionCount >= $targetDiscussions) {
                break;
            }

            // Get students enrolled in this course
            $enrolledStudentIds = Enrollment::where('course_id', $course->id)->pluck('user_id');
            $enrolledStudents = $students->whereIn('id', $enrolledStudentIds);

            if ($enrolledStudents->isEmpty()) {
                continue;
            }

            // Get the instructor for this course
            $courseInstructor = $instructors->firstWhere('id', $course->instructor_id);

            // Get lessons from the course for optional lesson_id references
            $courseLessons = $course->sections->flatMap->lessons;

            // Create 1-2 discussions per course
            $discussionsForCourse = min(rand(1, 2), $targetDiscussions - $discussionCount);

            for ($i = 0; $i < $discussionsForCourse; $i++) {
                $student = $enrolledStudents->random();
                $lesson = fake()->boolean(50) && $courseLessons->isNotEmpty()
                    ? $courseLessons->random()
                    : null;

                $isAnswered = fake()->boolean(60);
                $question = $discussionQuestions[array_rand($discussionQuestions)];

                $discussion = Discussion::create([
                    'course_id' => $course->id,
                    'lesson_id' => $lesson?->id,
                    'user_id' => $student->id,
                    'question' => $question,
                    'upvotes' => fake()->numberBetween(0, 15),
                    'is_answered' => $isAnswered,
                ]);

                // Add 1-3 replies per discussion
                $replyCount = rand(1, 3);

                for ($r = 0; $r < $replyCount; $r++) {
                    // Alternate between student and instructor replies
                    $isInstructorReply = ($r === $replyCount - 1 && $isAnswered && $courseInstructor);

                    if ($isInstructorReply) {
                        Reply::create([
                            'discussion_id' => $discussion->id,
                            'user_id' => $courseInstructor->id,
                            'reply_text' => $instructorReplies[array_rand($instructorReplies)],
                            'upvotes' => fake()->numberBetween(2, 20),
                            'is_instructor_reply' => true,
                        ]);
                    } else {
                        $replyStudent = $enrolledStudents->random();
                        Reply::create([
                            'discussion_id' => $discussion->id,
                            'user_id' => $replyStudent->id,
                            'reply_text' => $studentReplies[array_rand($studentReplies)],
                            'upvotes' => fake()->numberBetween(0, 10),
                            'is_instructor_reply' => false,
                        ]);
                    }
                }

                $discussionCount++;
            }
        }
    }
}
