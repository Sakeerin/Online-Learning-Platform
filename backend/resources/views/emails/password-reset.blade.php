@component('mail::message')
# Reset Your Password

Hello {{ $user->name }},

We received a request to reset the password for your account associated with **{{ $user->email }}**.

Click the button below to reset your password:

@component('mail::button', ['url' => $resetUrl, 'color' => 'primary'])
Reset Password
@endcomponent

This password reset link will expire in 60 minutes.

If you did not request a password reset, no further action is required. Your account remains secure.

**Security Tip:** Never share your password or this reset link with anyone.

Regards,<br>
{{ config('app.name') }}
@endcomponent
