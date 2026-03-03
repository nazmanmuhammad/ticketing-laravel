@extends('emails.layout')

@section('preheader', 'Feedback Review: ' . $number . ' - ' . $requestTitle)

@section('content')
<!-- Icon + Title -->
<table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 24px;">
<tr>
<td valign="top" style="padding-right: 14px;">
    <div style="width: 42px; height: 42px; background-color: #DBEAFE; border-radius: 12px; text-align: center; line-height: 42px; font-size: 18px;">&#x1F4AC;</div>
</td>
<td valign="middle">
    <h2 style="margin: 0 0 2px; color: #080C1A; font-size: 20px; font-weight: 700; letter-spacing: -0.3px;">Feedback Review</h2>
    <p style="margin: 0; color: #6A7686; font-size: 13px;">A new comment has been posted on <strong style="color: #080C1A;">{{ $number }}</strong></p>
</td>
</tr>
</table>

<!-- Request Info Card -->
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #F8FAFC; border-radius: 12px; border: 1px solid #E5E7EB; margin-bottom: 20px;">
<tr>
<td style="padding: 16px 20px;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td style="padding: 5px 0; color: #9CA3AF; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.4px; width: 80px; vertical-align: top;">Number</td>
        <td style="padding: 5px 0; color: #165DFF; font-size: 14px; font-weight: 700; font-family: 'Courier New', Courier, monospace;">{{ $number }}</td>
    </tr>
    <tr><td colspan="2" style="border-bottom: 1px solid #E5E7EB; padding: 0; height: 1px; font-size: 0; line-height: 0;">&nbsp;</td></tr>
    <tr>
        <td style="padding: 5px 0; color: #9CA3AF; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.4px; vertical-align: top;">Title</td>
        <td style="padding: 5px 0; color: #080C1A; font-size: 14px; font-weight: 600;">{{ $requestTitle }}</td>
    </tr>
    <tr><td colspan="2" style="border-bottom: 1px solid #E5E7EB; padding: 0; height: 1px; font-size: 0; line-height: 0;">&nbsp;</td></tr>
    <tr>
        <td style="padding: 5px 0; color: #9CA3AF; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.4px; vertical-align: top;">Type</td>
        <td style="padding: 5px 0; color: #080C1A; font-size: 14px;">
            <span style="display: inline-block; padding: 3px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; color: #165DFF; background-color: #DBEAFE;">{{ str_replace('_', ' ', ucfirst($type)) }}</span>
        </td>
    </tr>
    </table>
</td>
</tr>
</table>

<!-- Latest Comment Highlight -->
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 20px;">
<tr>
<td style="background-color: #EFF6FF; border-radius: 10px; padding: 14px 18px; border-left: 4px solid #165DFF;">
    <span style="font-size: 11px; color: #1E40AF; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">New Comment by {{ $commenterName }}</span>
    <p style="margin: 8px 0 0; color: #1E3A5F; font-size: 14px; line-height: 1.6;">{{ $commentBody }}</p>
</td>
</tr>
</table>

<!-- Comments History -->
@if(count($publicComments) > 0)
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 24px;">
<tr>
<td>
    <p style="margin: 0 0 12px; color: #6A7686; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Comment History</p>
</td>
</tr>
@foreach($publicComments as $c)
<tr>
<td style="padding: 10px 14px; border-bottom: 1px solid #F3F4F6;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
    <td>
        <span style="font-size: 13px; font-weight: 600; color: #080C1A;">{{ $c['user'] }}</span>
        <span style="font-size: 11px; color: #9CA3AF; margin-left: 8px;">{{ $c['date'] }}</span>
    </td>
    </tr>
    <tr>
    <td style="padding-top: 4px;">
        <p style="margin: 0; font-size: 13px; color: #4B5563; line-height: 1.5;">{{ Str::limit($c['body'], 300) }}</p>
    </td>
    </tr>
    </table>
</td>
</tr>
@endforeach
</table>
@endif

<!-- CTA Button -->
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
<td align="center" style="padding-top: 4px;">
    <!--[if mso]>
    <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" href="{{ $url }}" style="height:44px;v-text-anchor:middle;width:220px;" arcsize="23%" fillcolor="#165DFF" stroke="f">
    <center style="color:#ffffff;font-family:Arial,sans-serif;font-size:14px;font-weight:bold;">View Details</center>
    </v:roundrect>
    <![endif]-->
    <!--[if !mso]><!-->
    <a href="{{ $url }}" style="display: inline-block; padding: 12px 36px; background-color: #165DFF; color: #ffffff; font-size: 14px; font-weight: 600; text-decoration: none; border-radius: 10px; mso-padding-alt: 0;">View Details &rarr;</a>
    <!--<![endif]-->
</td>
</tr>
</table>
@endsection
