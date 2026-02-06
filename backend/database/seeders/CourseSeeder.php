<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Section;
use App\Models\User;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Seed the courses table with 10 published courses (5 per instructor),
     * each with sections and lessons.
     */
    public function run(): void
    {
        $instructor1 = User::where('email', 'instructor@example.com')->first();
        $instructor2 = User::where('email', 'instructor2@example.com')->first();

        // Instructor 1 courses (Development & Technical)
        $instructor1Courses = [
            [
                'title' => 'Complete Web Development Bootcamp 2025',
                'subtitle' => 'Learn HTML, CSS, JavaScript, React, Node.js and more from scratch',
                'description' => 'This comprehensive web development bootcamp takes you from absolute beginner to job-ready full-stack developer. You will learn front-end technologies including HTML5, CSS3, and modern JavaScript, then progress to React for building dynamic user interfaces. The back-end portion covers Node.js, Express, and database design with both SQL and NoSQL databases. By the end of this course, you will have built multiple real-world projects for your portfolio.',
                'category' => 'Development',
                'subcategory' => 'Web Development',
                'difficulty_level' => 'beginner',
                'price' => 1499.00,
                'sections' => [
                    ['title' => 'HTML & CSS Fundamentals', 'lessons' => ['Introduction to HTML', 'CSS Styling Basics', 'Responsive Design']],
                    ['title' => 'JavaScript Essentials', 'lessons' => ['Variables and Data Types', 'Functions and Scope']],
                    ['title' => 'React Framework', 'lessons' => ['Components and Props', 'State Management', 'Building a Project']],
                ],
            ],
            [
                'title' => 'Python for Data Science and Machine Learning',
                'subtitle' => 'Master Python, Pandas, NumPy, Matplotlib, Scikit-Learn, and TensorFlow',
                'description' => 'Dive deep into the world of data science with Python. This course covers everything from Python programming fundamentals to advanced machine learning algorithms. You will work with real-world datasets, learn data visualization techniques, and build predictive models using industry-standard libraries. Topics include regression, classification, clustering, neural networks, and natural language processing. Perfect for aspiring data scientists and analysts.',
                'category' => 'Development',
                'subcategory' => 'Data Science',
                'difficulty_level' => 'intermediate',
                'price' => 1799.00,
                'sections' => [
                    ['title' => 'Python Foundations', 'lessons' => ['Python Setup and Basics', 'Data Structures in Python']],
                    ['title' => 'Data Analysis with Pandas', 'lessons' => ['DataFrames and Series', 'Data Cleaning Techniques', 'Exploratory Data Analysis']],
                    ['title' => 'Machine Learning Basics', 'lessons' => ['Introduction to ML Algorithms', 'Building Your First Model']],
                    ['title' => 'Deep Learning with TensorFlow', 'lessons' => ['Neural Network Fundamentals', 'Training Deep Models']],
                ],
            ],
            [
                'title' => 'Docker and Kubernetes: The Complete Guide',
                'subtitle' => 'Build, deploy, and scale containerized applications with confidence',
                'description' => 'Learn containerization and orchestration from the ground up. This course begins with Docker fundamentals including images, containers, volumes, and networking. You will then advance to multi-container applications with Docker Compose before diving into Kubernetes for container orchestration at scale. The course includes hands-on labs with real deployment scenarios, CI/CD pipeline integration, and production best practices for cloud-native applications.',
                'category' => 'Development',
                'subcategory' => 'DevOps',
                'difficulty_level' => 'advanced',
                'price' => 1299.00,
                'sections' => [
                    ['title' => 'Docker Fundamentals', 'lessons' => ['What is Docker?', 'Building Docker Images', 'Docker Networking']],
                    ['title' => 'Docker Compose', 'lessons' => ['Multi-Container Apps', 'Environment Configuration']],
                    ['title' => 'Kubernetes Core Concepts', 'lessons' => ['Pods and Deployments', 'Services and Ingress', 'Scaling Applications']],
                ],
            ],
            [
                'title' => 'Introduction to Cloud Computing with AWS',
                'subtitle' => 'A free beginner-friendly guide to Amazon Web Services',
                'description' => 'Get started with cloud computing using Amazon Web Services. This free introductory course covers the core AWS services including EC2, S3, RDS, Lambda, and IAM. You will understand cloud computing concepts, the shared responsibility model, and how to navigate the AWS Management Console. By the end, you will be prepared to pursue the AWS Cloud Practitioner certification and have a solid foundation for more advanced cloud courses.',
                'category' => 'Development',
                'subcategory' => 'Cloud Computing',
                'difficulty_level' => 'beginner',
                'price' => 0.00,
                'sections' => [
                    ['title' => 'Cloud Computing Overview', 'lessons' => ['What is Cloud Computing?', 'AWS Global Infrastructure']],
                    ['title' => 'Core AWS Services', 'lessons' => ['EC2 and Compute', 'S3 and Storage', 'RDS and Databases']],
                    ['title' => 'Security and Best Practices', 'lessons' => ['IAM and Access Control', 'Cost Optimization']],
                ],
            ],
            [
                'title' => 'Mobile App Development with Flutter',
                'subtitle' => 'Build beautiful cross-platform apps for iOS and Android',
                'description' => 'Master Flutter and Dart to create stunning cross-platform mobile applications from a single codebase. This course covers the Dart programming language, Flutter widget system, state management solutions, navigation patterns, and integration with backend services and APIs. You will build multiple complete applications including a weather app, a chat application, and an e-commerce store. Learn to publish your apps to both the App Store and Google Play.',
                'category' => 'Development',
                'subcategory' => 'Mobile Development',
                'difficulty_level' => 'intermediate',
                'price' => 999.00,
                'sections' => [
                    ['title' => 'Dart Programming Language', 'lessons' => ['Dart Basics', 'Object-Oriented Dart']],
                    ['title' => 'Flutter Widgets', 'lessons' => ['Stateless vs Stateful Widgets', 'Layout and Navigation', 'Forms and Input']],
                    ['title' => 'State Management', 'lessons' => ['Provider Pattern', 'Building a Complete App']],
                ],
            ],
        ];

        // Instructor 2 courses (Design, Business, Marketing)
        $instructor2Courses = [
            [
                'title' => 'UI/UX Design Masterclass',
                'subtitle' => 'Learn user interface and user experience design from scratch',
                'description' => 'Transform your design skills with this comprehensive UI/UX masterclass. Starting with design thinking principles, you will learn user research methodologies, wireframing, prototyping, and visual design. The course covers industry-standard tools including Figma, and teaches you to create design systems, conduct usability testing, and present your work to stakeholders. Build a professional portfolio with real project case studies that showcase your design process.',
                'category' => 'Design',
                'subcategory' => 'UI/UX Design',
                'difficulty_level' => 'beginner',
                'price' => 1199.00,
                'sections' => [
                    ['title' => 'Design Thinking Foundations', 'lessons' => ['What is UX Design?', 'User Research Methods', 'Creating Personas']],
                    ['title' => 'Wireframing and Prototyping', 'lessons' => ['Low-Fidelity Wireframes', 'Interactive Prototypes']],
                    ['title' => 'Visual Design Principles', 'lessons' => ['Color Theory and Typography', 'Design Systems', 'Portfolio Project']],
                ],
            ],
            [
                'title' => 'Digital Marketing Strategy',
                'subtitle' => 'Master SEO, social media, email marketing, and paid advertising',
                'description' => 'Develop a complete digital marketing skill set with this all-in-one course. Learn search engine optimization to drive organic traffic, master social media marketing across major platforms, create effective email campaigns that convert, and run profitable paid advertising on Google and Facebook. The course includes real campaign case studies, marketing analytics, and a framework for building comprehensive marketing strategies for any business or product.',
                'category' => 'Marketing',
                'subcategory' => 'Digital Marketing',
                'difficulty_level' => 'intermediate',
                'price' => 899.00,
                'sections' => [
                    ['title' => 'SEO Fundamentals', 'lessons' => ['Keyword Research', 'On-Page SEO', 'Link Building Strategies']],
                    ['title' => 'Social Media Marketing', 'lessons' => ['Content Strategy', 'Platform-Specific Tactics']],
                    ['title' => 'Paid Advertising', 'lessons' => ['Google Ads Basics', 'Facebook Ads Manager']],
                    ['title' => 'Analytics and Optimization', 'lessons' => ['Google Analytics', 'Conversion Rate Optimization']],
                ],
            ],
            [
                'title' => 'Business Strategy and Entrepreneurship',
                'subtitle' => 'From idea validation to scaling your startup',
                'description' => 'Learn the essential frameworks for building and growing a successful business. This course covers idea validation, business model canvas, lean startup methodology, financial planning, fundraising, and growth strategies. You will study real-world case studies of successful startups and learn from common pitfalls. Whether you are launching a side project or building the next unicorn, this course provides the strategic thinking tools you need to make informed decisions.',
                'category' => 'Business',
                'subcategory' => 'Entrepreneurship',
                'difficulty_level' => 'intermediate',
                'price' => 1599.00,
                'sections' => [
                    ['title' => 'Idea Validation', 'lessons' => ['Market Research Techniques', 'Building an MVP']],
                    ['title' => 'Business Models', 'lessons' => ['Business Model Canvas', 'Revenue Streams', 'Pricing Strategies']],
                    ['title' => 'Growth and Scaling', 'lessons' => ['Growth Hacking Techniques', 'Building a Team']],
                ],
            ],
            [
                'title' => 'Graphic Design Essentials with Figma',
                'subtitle' => 'A free course to kickstart your design career',
                'description' => 'Start your graphic design journey with this free introductory course. Learn the fundamental principles of design including composition, color theory, typography, and visual hierarchy. The course uses Figma, a free industry-standard design tool, so you can follow along without any software costs. You will complete hands-on projects including a logo design, social media graphics, and a poster layout. Perfect for beginners who want to explore the world of design.',
                'category' => 'Design',
                'subcategory' => 'Graphic Design',
                'difficulty_level' => 'beginner',
                'price' => 0.00,
                'sections' => [
                    ['title' => 'Design Principles', 'lessons' => ['Composition and Layout', 'Color Theory Basics']],
                    ['title' => 'Working with Figma', 'lessons' => ['Figma Interface Tour', 'Shapes, Text, and Layers', 'Creating Your First Design']],
                    ['title' => 'Practical Projects', 'lessons' => ['Logo Design Workshop', 'Social Media Graphics']],
                ],
            ],
            [
                'title' => 'Financial Planning and Investment Basics',
                'subtitle' => 'Take control of your personal and business finances',
                'description' => 'Build a solid foundation in financial planning and investment. This free course covers personal budgeting, understanding financial statements, investment vehicles including stocks, bonds, and ETFs, risk management, and retirement planning. You will learn how to evaluate investment opportunities, build a diversified portfolio, and make informed financial decisions. The course is designed for anyone who wants to improve their financial literacy regardless of their current knowledge level.',
                'category' => 'Business',
                'subcategory' => 'Finance',
                'difficulty_level' => 'beginner',
                'price' => 0.00,
                'sections' => [
                    ['title' => 'Personal Finance Foundations', 'lessons' => ['Budgeting Strategies', 'Emergency Fund Planning', 'Understanding Credit']],
                    ['title' => 'Investment Fundamentals', 'lessons' => ['Stocks and Bonds', 'Mutual Funds and ETFs']],
                    ['title' => 'Planning for the Future', 'lessons' => ['Retirement Planning', 'Tax-Efficient Investing']],
                ],
            ],
        ];

        $this->createCoursesForInstructor($instructor1, $instructor1Courses);
        $this->createCoursesForInstructor($instructor2, $instructor2Courses);
    }

    /**
     * Create courses with sections and lessons for a given instructor.
     */
    private function createCoursesForInstructor(User $instructor, array $coursesData): void
    {
        foreach ($coursesData as $courseData) {
            $sectionsData = $courseData['sections'];
            unset($courseData['sections']);

            $course = Course::create([
                'instructor_id' => $instructor->id,
                'title' => $courseData['title'],
                'subtitle' => $courseData['subtitle'],
                'description' => $courseData['description'],
                'learning_objectives' => [
                    'Understand core concepts and best practices',
                    'Build real-world projects from scratch',
                    'Gain practical, hands-on experience',
                    'Prepare for professional opportunities in the field',
                ],
                'category' => $courseData['category'],
                'subcategory' => $courseData['subcategory'],
                'difficulty_level' => $courseData['difficulty_level'],
                'thumbnail' => 'https://picsum.photos/seed/' . urlencode($courseData['title']) . '/640/480',
                'price' => $courseData['price'],
                'currency' => 'THB',
                'status' => 'published',
                'published_at' => now()->subDays(rand(7, 90)),
                'average_rating' => null,
                'total_enrollments' => 0,
                'total_reviews' => 0,
            ]);

            foreach ($sectionsData as $sectionIndex => $sectionData) {
                $section = Section::create([
                    'course_id' => $course->id,
                    'title' => $sectionData['title'],
                    'description' => 'In this section you will learn about ' . strtolower($sectionData['title']) . '.',
                    'order' => $sectionIndex + 1,
                ]);

                foreach ($sectionData['lessons'] as $lessonIndex => $lessonTitle) {
                    // Make most lessons video type; a few as quiz or article
                    $type = 'video';
                    if ($lessonIndex === count($sectionData['lessons']) - 1 && $sectionIndex > 0) {
                        // Last lesson in non-first sections: alternate between quiz and article
                        $type = $sectionIndex % 2 === 0 ? 'quiz' : 'article';
                    }

                    $content = match ($type) {
                        'video' => [
                            'video_url' => 'https://example.com/videos/' . fake()->uuid() . '.mp4',
                            'video_path' => 'videos/' . fake()->uuid() . '.mp4',
                            'thumbnail' => 'https://picsum.photos/seed/' . urlencode($lessonTitle) . '/640/360',
                        ],
                        'quiz' => [
                            'instructions' => 'Complete this quiz to test your understanding of ' . strtolower($sectionData['title']) . '.',
                        ],
                        'article' => [
                            'content' => 'This article covers the key concepts of ' . strtolower($lessonTitle) . '. It provides detailed explanations, code examples, and practical tips to help you master the topic. Take your time reading through the material and try the exercises at the end.',
                        ],
                    };

                    Lesson::create([
                        'section_id' => $section->id,
                        'title' => $lessonTitle,
                        'type' => $type,
                        'content' => $content,
                        'duration' => $type === 'video' ? fake()->numberBetween(300, 2400) : null,
                        'is_preview' => $sectionIndex === 0 && $lessonIndex === 0,
                        'order' => $lessonIndex + 1,
                    ]);
                }
            }
        }
    }
}
