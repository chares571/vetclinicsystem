@component('mail::message')
# Password Reset Verification Code

Hello,

Your verification code for the New Creation Animal Clinic and Diagnostic Center system is:

@component('mail::panel')
## {{ $code }}
@endcomponent

Enter this code to reset your password. It expires in {{ $expiresInHuman }}.

If you did not request a reset, you can ignore this email.

Thanks,<br>
Pet Infirmary Clinic
@endcomponent
