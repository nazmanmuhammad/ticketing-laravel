@extends('emails.layout')

@section('preheader', 'You have a new notification')

@section('content')
<!-- Icon + Title -->
<table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 24px;">
<tr>
<td valign="top" style="padding-right: 14px;">
    <div style="width: 42px; height: 42px; background-color: #DBEAFE; border-radius: 12px; text-align: center; line-height: 42px; font-size: 18px;">&#x1F514;</div>
</td>
<td valign="middle">
    <h2 style="margin: 0 0 2px; color: #080C1A; font-size: 20px; font-weight: 700; letter-spacing: -0.3px;">Notification</h2>
    <p style="margin: 0; color: #6A7686; font-size: 13px;">You have a new notification from the system.</p>
</td>
</tr>
</table>

<!-- Body -->
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
<td style="background-color: #F8FAFC; border-radius: 12px; border: 1px solid #E5E7EB; padding: 20px;">
    <div style="color: #374151; font-size: 14px; line-height: 1.7;">
        {!! nl2br(e($emailBody)) !!}
    </div>
</td>
</tr>
</table>
@endsection
