@component('mail::message')
# Your Course Is Now Live!

Hello {{ $instructor->name }},

Great news! Your course **{{ $course->title }}** has been published and is now available to students on our platform.

**Course Details:**
- **Title:** {{ $course->title }}
- **Category:** {{ $course->category }}
- **Difficulty Level:** {{ ucfirst($course->difficulty_level) }}
- **Price:** {{ $course->currency }} {{ number_format($course->price, 2) }}
- **Published At:** {{ $course->published_at->format('F j, Y') }}

Students can now discover and enroll in your course. Make sure to share it with your audience to maximize enrollments.

@component('mail::button', ['url' => config('app.frontend_url') . '/courses/' . $course->id])
View Your Course
@endcomponent

Thank you for being an instructor on our platform!

Regards,<br>
{{ config('app.name') }}
@endcomponent
