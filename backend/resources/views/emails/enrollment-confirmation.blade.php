@component('mail::message')
# Welcome to {{ $course->title }}!

Hello {{ $student->name }},

You have successfully enrolled in **{{ $course->title }}**. You are now ready to start learning!

**Enrollment Details:**
- **Course:** {{ $course->title }}
- **Instructor:** {{ $course->instructor->name }}
- **Difficulty Level:** {{ ucfirst($course->difficulty_level) }}
- **Enrolled On:** {{ $enrollment->enrolled_at->format('F j, Y') }}

Here are some tips to get the most out of your learning experience:

1. Set a regular study schedule
2. Take notes as you progress through the lessons
3. Participate in course discussions and Q&A
4. Complete all quizzes and assignments

@component('mail::button', ['url' => config('app.frontend_url') . '/courses/' . $course->id . '/learn'])
Start Learning
@endcomponent

Happy learning!

Regards,<br>
{{ config('app.name') }}
@endcomponent
