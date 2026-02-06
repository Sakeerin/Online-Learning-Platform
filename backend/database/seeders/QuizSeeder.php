<?php

namespace Database\Seeders;

use App\Models\Lesson;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Database\Seeder;

class QuizSeeder extends Seeder
{
    /**
     * Seed 5 quizzes on quiz-type lessons, each with 3-5 multiple-choice questions.
     */
    public function run(): void
    {
        // Find all quiz-type lessons
        $quizLessons = Lesson::where('type', 'quiz')->take(5)->get();

        $quizData = [
            [
                'title' => 'HTML & CSS Fundamentals Quiz',
                'passing_score' => 70,
                'questions' => [
                    [
                        'question_text' => 'Which HTML element is used to define the largest heading?',
                        'options' => ['<heading>', '<h6>', '<h1>', '<head>'],
                        'correct_answer' => '<h1>',
                        'explanation' => 'The <h1> element defines the largest and most important heading in HTML.',
                    ],
                    [
                        'question_text' => 'Which CSS property is used to change the text color of an element?',
                        'options' => ['font-color', 'text-color', 'color', 'foreground-color'],
                        'correct_answer' => 'color',
                        'explanation' => 'The "color" property in CSS is used to set the text color of an element.',
                    ],
                    [
                        'question_text' => 'What does CSS stand for?',
                        'options' => ['Creative Style Sheets', 'Cascading Style Sheets', 'Computer Style Sheets', 'Colorful Style Sheets'],
                        'correct_answer' => 'Cascading Style Sheets',
                        'explanation' => 'CSS stands for Cascading Style Sheets, used to style HTML documents.',
                    ],
                    [
                        'question_text' => 'Which CSS property controls the space between elements?',
                        'options' => ['spacing', 'margin', 'padding', 'border'],
                        'correct_answer' => 'margin',
                        'explanation' => 'The margin property controls the space outside an element, between it and other elements.',
                    ],
                ],
            ],
            [
                'title' => 'Python Data Structures Quiz',
                'passing_score' => 60,
                'questions' => [
                    [
                        'question_text' => 'Which data structure in Python uses key-value pairs?',
                        'options' => ['List', 'Tuple', 'Set', 'Dictionary'],
                        'correct_answer' => 'Dictionary',
                        'explanation' => 'Dictionaries in Python store data as key-value pairs using curly braces {}.',
                    ],
                    [
                        'question_text' => 'What is the output of len([1, 2, [3, 4]])?',
                        'options' => ['4', '3', '2', 'Error'],
                        'correct_answer' => '3',
                        'explanation' => 'The list contains three elements: 1, 2, and [3, 4]. The nested list counts as one element.',
                    ],
                    [
                        'question_text' => 'Which of the following is immutable in Python?',
                        'options' => ['List', 'Dictionary', 'Set', 'Tuple'],
                        'correct_answer' => 'Tuple',
                        'explanation' => 'Tuples are immutable sequences in Python, meaning they cannot be changed after creation.',
                    ],
                ],
            ],
            [
                'title' => 'Docker Concepts Quiz',
                'passing_score' => 75,
                'questions' => [
                    [
                        'question_text' => 'What is a Docker container?',
                        'options' => ['A virtual machine', 'A lightweight isolated process', 'A physical server', 'A database'],
                        'correct_answer' => 'A lightweight isolated process',
                        'explanation' => 'Docker containers are lightweight, isolated processes that share the host OS kernel.',
                    ],
                    [
                        'question_text' => 'Which file is used to define a Docker image?',
                        'options' => ['docker-compose.yml', 'Dockerfile', 'package.json', 'config.yml'],
                        'correct_answer' => 'Dockerfile',
                        'explanation' => 'A Dockerfile contains instructions for building a Docker image.',
                    ],
                    [
                        'question_text' => 'What command is used to list running Docker containers?',
                        'options' => ['docker images', 'docker ps', 'docker run', 'docker list'],
                        'correct_answer' => 'docker ps',
                        'explanation' => 'The "docker ps" command lists all currently running containers.',
                    ],
                    [
                        'question_text' => 'What is Docker Compose used for?',
                        'options' => ['Building images', 'Managing multi-container applications', 'Monitoring containers', 'Deploying to production'],
                        'correct_answer' => 'Managing multi-container applications',
                        'explanation' => 'Docker Compose is a tool for defining and running multi-container Docker applications.',
                    ],
                    [
                        'question_text' => 'Which command builds a Docker image from a Dockerfile?',
                        'options' => ['docker create', 'docker build', 'docker make', 'docker compile'],
                        'correct_answer' => 'docker build',
                        'explanation' => 'The "docker build" command reads a Dockerfile and creates an image from it.',
                    ],
                ],
            ],
            [
                'title' => 'UX Design Principles Quiz',
                'passing_score' => 70,
                'questions' => [
                    [
                        'question_text' => 'What is the primary goal of UX design?',
                        'options' => ['Making things look beautiful', 'Improving user satisfaction', 'Writing code', 'Reducing development time'],
                        'correct_answer' => 'Improving user satisfaction',
                        'explanation' => 'UX design focuses on improving user satisfaction by enhancing usability and accessibility.',
                    ],
                    [
                        'question_text' => 'What is a wireframe?',
                        'options' => ['A finished design', 'A low-fidelity layout sketch', 'A type of animation', 'A coding framework'],
                        'correct_answer' => 'A low-fidelity layout sketch',
                        'explanation' => 'A wireframe is a simplified visual guide representing the skeletal framework of a design.',
                    ],
                    [
                        'question_text' => 'Which of the following is a key principle of user-centered design?',
                        'options' => ['Design for developers first', 'Test with real users early and often', 'Prioritize aesthetics over function', 'Follow trends regardless of user needs'],
                        'correct_answer' => 'Test with real users early and often',
                        'explanation' => 'User-centered design emphasizes involving real users throughout the design process.',
                    ],
                ],
            ],
            [
                'title' => 'Digital Marketing Fundamentals Quiz',
                'passing_score' => 60,
                'questions' => [
                    [
                        'question_text' => 'What does SEO stand for?',
                        'options' => ['Social Engine Optimization', 'Search Engine Optimization', 'Site Enhancement Online', 'Search Email Outreach'],
                        'correct_answer' => 'Search Engine Optimization',
                        'explanation' => 'SEO stands for Search Engine Optimization, the practice of improving website visibility in search results.',
                    ],
                    [
                        'question_text' => 'Which metric measures the percentage of visitors who leave after viewing only one page?',
                        'options' => ['Conversion rate', 'Click-through rate', 'Bounce rate', 'Engagement rate'],
                        'correct_answer' => 'Bounce rate',
                        'explanation' => 'Bounce rate measures the percentage of visitors who navigate away after viewing only one page.',
                    ],
                    [
                        'question_text' => 'What is a call-to-action (CTA)?',
                        'options' => ['A phone number on a website', 'A prompt that encourages users to take a specific action', 'An automated email', 'A customer support feature'],
                        'correct_answer' => 'A prompt that encourages users to take a specific action',
                        'explanation' => 'A CTA is a design element that prompts users to take a desired action, like "Sign Up" or "Buy Now".',
                    ],
                    [
                        'question_text' => 'Which platform is primarily used for B2B marketing?',
                        'options' => ['TikTok', 'Instagram', 'LinkedIn', 'Snapchat'],
                        'correct_answer' => 'LinkedIn',
                        'explanation' => 'LinkedIn is the leading platform for B2B (business-to-business) marketing and professional networking.',
                    ],
                ],
            ],
        ];

        foreach ($quizLessons as $index => $lesson) {
            if (!isset($quizData[$index])) {
                break;
            }

            $data = $quizData[$index];

            $quiz = Quiz::create([
                'lesson_id' => $lesson->id,
                'title' => $data['title'],
                'passing_score' => $data['passing_score'],
                'allow_retake' => true,
            ]);

            foreach ($data['questions'] as $qIndex => $questionData) {
                Question::create([
                    'quiz_id' => $quiz->id,
                    'question_text' => $questionData['question_text'],
                    'question_type' => 'multiple_choice',
                    'options' => $questionData['options'],
                    'correct_answer' => $questionData['correct_answer'],
                    'explanation' => $questionData['explanation'],
                    'order' => $qIndex + 1,
                ]);
            }
        }
    }
}
