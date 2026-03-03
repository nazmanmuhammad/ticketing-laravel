@extends('emails.layout')

@section('preheader', 'Approval Required: ' . $number . ' - ' . $title)

@section('content')
<!-- Icon + Title -->
<table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 24px;">
<tr>
<td valign="top" style="padding-right: 14px;">
    <div style="width: 42px; height: 42px; background-color: #FEF9C3; border-radius: 12px; text-align: center; line-height: 42px; font-size: 18px;">&#x2705;</div>
</td>
<td valign="middle">
    <h2 style="margin: 0 0 2px; color: #080C1A; font-size: 20px; font-weight: 700; letter-spacing: -0.3px;">Approval Required</h2>
    <p style="margin: 0; color: #6A7686; font-size: 13px;">Hello <strong style="color: #080C1A;">{{ $approverName }}</strong>, you have a new item awaiting your approval.</p>
</td>
</tr>
</table>

<!-- Level Badge -->
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 20px;">
<tr>
<td style="background-color: #FFFBEB; border-radius: 10px; padding: 14px 18px; border-left: 4px solid #F59E0B;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
    <td>
        <span style="font-size: 11px; color: #92400E; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">Your Role</span><br>
        <span style="font-size: 16px; color: #92400E; font-weight: 700;">Level {{ $level }} Approver</span>
    </td>
    <td align="right" valign="middle">
        <span style="display: inline-block; padding: 4px 14px; border-radius: 20px; font-size: 12px; font-weight: 700; color: #165DFF; background-color: #DBEAFE;">{{ str_replace('_', ' ', ucfirst($type)) }}</span>
    </td>
    </tr>
    </table>
</td>
</tr>
</table>

<!-- Details Card -->
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #F8FAFC; border-radius: 12px; border: 1px solid #E5E7EB; margin-bottom: 24px;">
<tr>
<td style="padding: 20px;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td style="padding: 7px 0; color: #9CA3AF; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.4px; width: 100px; vertical-align: top;">Number</td>
        <td style="padding: 7px 0; color: #165DFF; font-size: 14px; font-weight: 700; font-family: 'Courier New', Courier, monospace; letter-spacing: 0.5px;">{{ $number }}</td>
    </tr>
    <tr><td colspan="2" style="border-bottom: 1px solid #E5E7EB; padding: 0; height: 1px; font-size: 0; line-height: 0;">&nbsp;</td></tr>
    <tr>
        <td style="padding: 7px 0; color: #9CA3AF; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.4px; vertical-align: top;">Title</td>
        <td style="padding: 7px 0; color: #080C1A; font-size: 14px; font-weight: 600;">{{ $title }}</td>
    </tr>
    </table>
</td>
</tr>
</table>

<!-- Message -->
<p style="margin: 0 0 28px; color: #6A7686; font-size: 14px; line-height: 1.6;">Please review the details and take appropriate action on this request.</p>

<!-- CTA Button -->
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
<td align="center" style="padding-top: 4px;">
    <!--[if mso]>
    <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" href="{{ $url }}" style="height:44px;v-text-anchor:middle;width:220px;" arcsize="23%" fillcolor="#165DFF" stroke="f">
    <center style="color:#ffffff;font-family:Arial,sans-serif;font-size:14px;font-weight:bold;">Review &amp; Approve</center>
    </v:roundrect>
    <![endif]-->
    <!--[if !mso]><!-->
    <a href="{{ $url }}" style="display: inline-block; padding: 12px 36px; background-color: #165DFF; color: #ffffff; font-size: 14px; font-weight: 600; text-decoration: none; border-radius: 10px; mso-padding-alt: 0;">Review & Approve &rarr;</a>
    <!--<![endif]-->
</td>
</tr>
</table>
@endsection
