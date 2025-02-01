@component('mail::message')
# Password Reset Request

We received a request to reset the password associated with this email: **{{ $email }}**.

Your password reset code is:

@component('mail::panel')
{{ $token }}
@endcomponent

If you did not request a password reset, please ignore this email.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
