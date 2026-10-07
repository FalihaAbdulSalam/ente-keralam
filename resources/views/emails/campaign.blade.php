<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $subject ?? 'Email Campaign' }}</title>
    </head>
    <body style="margin:0; padding:0; background-color:#f4f4f0;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f0;">
            <tr>
                <td align="center" style="padding:24px 12px;">
                    <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="width:600px; max-width:600px; background-color:#ffffff; border:1px solid #e3e3e3; border-radius:16px;">
                        <tr>
                            <td style="padding:24px 24px 8px; text-align:center;">
                                <img src="{{ $logoUrl }}" alt="Ente Keralam" width="160" style="display:block; margin:0 auto; width:160px; max-width:100%; height:auto; border:0;">
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:8px 28px 20px; font-family: Arial, sans-serif; color:#1f2933; font-size:14px; line-height:1.6;">
                                {!! $bodyHtml !!}
                                @if (!empty($buttonEnabled))
                                    <table role="presentation" cellpadding="0" cellspacing="0" style="margin:16px 0 8px;">
                                        <tr>
                                            <td align="center">
                                                <a href="{{ $buttonUrl }}"
                                                   style="background-color:#ff0099; color:#ffffff; text-decoration:none; padding:12px 20px; border-radius:6px; display:inline-block; font-weight:700; font-size:14px;">
                                                    {{ $buttonLabel }}
                                                </a>
                                            </td>
                                        </tr>
                                    </table>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:0 28px 24px; font-family: Arial, sans-serif; color:#6b7280; font-size:12px;">
                                Regards<br>
                                Ente Keralam Portal
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
</html>
