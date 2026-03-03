@php
    $appName = \App\Models\Setting::getValue('app_name', config('app.name', 'Helpdesk'));
    $appLogo = \App\Models\Setting::getValue('app_logo');
    $logoUrl = $appLogo ? url('storage/' . $appLogo) : null;
    $appUrl = config('app.url', '#');
@endphp
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="x-apple-disable-message-reformatting">
<title>{{ $appName }}</title>
<!--[if mso]>
<style>table,td,div,p,a,span{font-family:Arial,Helvetica,sans-serif !important;}</style>
<![endif]-->
</head>
<body style="margin: 0; padding: 0; width: 100%; background-color: #EFF2F7; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased;">

<!-- Preheader (hidden) -->
<div style="display: none; max-height: 0; overflow: hidden; mso-hide: all;">@yield('preheader', $appName . ' - Notification')</div>

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #EFF2F7;">
<tr>
<td align="center" style="padding: 32px 16px 40px;">

    <!-- Outer container -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width: 560px;">

    <!-- Logo Row -->
    <tr>
    <td align="center" style="padding-bottom: 24px;">
        <a href="{{ $appUrl }}" style="text-decoration: none;">
            <table role="presentation" cellpadding="0" cellspacing="0" border="0">
            <tr>
            <td align="center" valign="middle">
                @if($logoUrl)
                <img src="{{ $logoUrl }}" alt="{{ $appName }}" width="44" height="44" style="display: block; max-height: 44px; width: auto; border: 0;">
                @else
                <div style="width: 44px; height: 44px; background-color: #165DFF; border-radius: 12px; text-align: center; line-height: 44px; color: #ffffff; font-size: 20px; font-weight: 700;">{{ strtoupper(substr($appName, 0, 1)) }}</div>
                @endif
            </td>
            <td style="padding-left: 12px;">
                <span style="font-size: 18px; font-weight: 700; color: #165DFF; text-decoration: none; letter-spacing: -0.3px;">{{ $appName }}</span>
            </td>
            </tr>
            </table>
        </a>
    </td>
    </tr>

    <!-- Card -->
    <tr>
    <td>
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; border-radius: 16px; border: 1px solid #E5E7EB; overflow: hidden;">
        <!-- Accent bar -->
        <tr><td style="height: 4px; background-color: #165DFF; font-size: 0; line-height: 0;">&nbsp;</td></tr>
        <!-- Content -->
        <tr>
        <td style="padding: 36px 36px 32px;">
            @yield('content')
        </td>
        </tr>
        </table>
    </td>
    </tr>

    <!-- Footer -->
    <tr>
    <td style="padding: 28px 8px 0; text-align: center;">
        <p style="margin: 0 0 6px; color: #9CA3AF; font-size: 12px; line-height: 1.5;">
            This is an automated message from <span style="color: #6A7686; font-weight: 600;">{{ $appName }}</span>.
        </p>
        <p style="margin: 0 0 6px; color: #9CA3AF; font-size: 12px;">
            Please do not reply directly to this email.
        </p>
        <p style="margin: 12px 0 0; color: #C7CCD4; font-size: 11px;">
            &copy; {{ date('Y') }} {{ $appName }}. All rights reserved.
        </p>
    </td>
    </tr>

    </table>

</td>
</tr>
</table>
</body>
</html>
