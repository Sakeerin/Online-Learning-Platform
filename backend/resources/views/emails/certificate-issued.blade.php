@component('mail::message')
# Congratulations, {{ $user->name }}!

You have successfully completed **{{ $course->title }}** and earned your certificate of completion!

**Certificate Details:**
- **Course:** {{ $course->title }}
- **Issued On:** {{ $certificate->issued_at->format('F j, Y') }}
- **Verification Code:** {{ $certificate->verification_code }}

Your achievement is a testament to your dedication and hard work. This certificate can be shared with employers and added to your professional profile.

@if($certificate->certificate_url)
@component('mail::button', ['url' => $certificate->certificate_url])
Download Certificate
@endcomponent
@else
@component('mail::button', ['url' => config('app.frontend_url') . '/certificates/' . $certificate->verification_code])
View Certificate
@endcomponent
@endif

Keep up the great work and continue your learning journey!

Regards,<br>
{{ config('app.name') }}
@endcomponent
