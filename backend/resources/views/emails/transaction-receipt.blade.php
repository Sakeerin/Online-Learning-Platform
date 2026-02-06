@component('mail::message')
# Payment Receipt

Hello {{ $user->name }},

Thank you for your purchase! Here is your payment receipt.

@component('mail::table')
| Detail | Value |
|:-------|:------|
| **Transaction ID** | {{ $transaction->id }} |
| **Course** | {{ $course->title }} |
| **Date** | {{ $transaction->created_at->format('F j, Y \a\t g:i A') }} |
| **Payment Method** | {{ ucfirst($transaction->payment_method) }} |
| **Amount** | {{ $transaction->currency }} {{ number_format($transaction->amount, 2) }} |
| **Status** | {{ ucfirst($transaction->status) }} |
@endcomponent

If you have any questions about this transaction, please contact our support team with your Transaction ID.

@component('mail::button', ['url' => config('app.frontend_url') . '/courses/' . $course->id . '/learn'])
Go to Course
@endcomponent

Regards,<br>
{{ config('app.name') }}
@endcomponent
