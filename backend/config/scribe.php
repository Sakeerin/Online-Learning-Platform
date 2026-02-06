<?php

return [
    'title' => 'Course Marketplace API',
    'description' => 'API documentation for the Online Learning Platform - a full-featured course marketplace with instructor tools, student learning, payments, and analytics.',
    'base_url' => env('APP_URL', 'http://localhost:8000'),

    'routes' => [
        [
            'match' => [
                'prefixes' => ['api/v1'],
                'domains' => ['*'],
            ],
            'include' => [],
            'exclude' => [],
        ],
    ],

    'type' => 'static',
    'static' => [
        'output_path' => 'public/docs',
    ],

    'auth' => [
        'enabled' => true,
        'default' => false,
        'in' => 'bearer',
        'name' => 'Authorization',
        'use_value' => 'Bearer {token}',
        'placeholder' => '{token}',
        'extra_info' => 'Obtain a token by calling the login endpoint. Include it in the Authorization header as "Bearer {token}".',
    ],

    'intro_text' => <<<INTRO
## Welcome to the Course Marketplace API

This API powers the Online Learning Platform. It provides endpoints for:

- **Authentication** - Register, login, password reset
- **Course Discovery** - Browse, search, and view courses
- **Instructor Tools** - Create and manage courses, sections, lessons, quizzes
- **Student Learning** - Enroll, track progress, take quizzes, earn certificates
- **Payments** - Checkout, verify payments, request refunds
- **Reviews & Discussions** - Rate courses, ask questions, participate in Q&A
- **Analytics** - View enrollment trends, revenue, and course performance

### Authentication
Most endpoints require authentication via Laravel Sanctum Bearer tokens. Public endpoints (course browsing, certificate verification) do not require authentication.

### Rate Limiting
- Authenticated endpoints: 60 requests/minute per user
- Public endpoints: 30 requests/minute per IP
INTRO,

    'examples' => [
        'faker_seed' => 12345,
        'models_source' => ['factoryCreate', 'factoryMake', 'databaseFirst'],
    ],

    'fractal' => [
        'serializer' => null,
    ],

    'try_it_out' => [
        'enabled' => true,
        'base_url' => null,
    ],

    'logo' => false,

    'last_updated' => 'Last updated: ' . date('F j, Y'),

    'groups' => [
        'default' => 'Other',
        'order' => [
            'Authentication',
            'Course Discovery',
            'Instructor - Course Management',
            'Instructor - Sections',
            'Instructor - Lessons',
            'Instructor - Quizzes',
            'Instructor - Analytics',
            'Student - Enrollments',
            'Student - Learning',
            'Student - Progress',
            'Student - Quizzes',
            'Student - Reviews',
            'Student - Discussions',
            'Student - Certificates',
            'Payments',
            'Certificate Verification',
        ],
    ],
];
