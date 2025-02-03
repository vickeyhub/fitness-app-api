@component('mail::message')
# Password Reset Request

Dear User,

We received a request to verify your email address: **{{ $email }}**.

Your email verification code is:

@component('mail::panel')
{{ $otp }}
@endcomponent

Please use this code to verify your email. If you did not request email verification, please ignore this email.

If you need further assistance, feel free to contact our support team.

Thanks,<br>
{{ config('app.name') }}
@endcomponent

