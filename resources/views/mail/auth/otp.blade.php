<x-mail::message>
# {{ $purpose ?? 'Your One Time Password' }}

Hi {{ $name ?? 'User' }},

Your one-time password (OTP) is:

<x-mail::panel>
<strong>{{ $otp ?? '' }}</strong>
</x-mail::panel>

This code expires in **10 minutes**. If you did not request this, you can ignore this email.

Thanks,<br>
Ente Keralam Team
</x-mail::message>
