@extends('emails.layout')

@section('preheader', 'Ticket ' . $ticket->ticket_number . ' has been created')

@section('content')
@php
    $prioStyles = [
        'low'      => ['color' => '#16A34A', 'bg' => '#DCFCE7'],
        'medium'   => ['color' => '#2563EB', 'bg' => '#DBEAFE'],
        'high'     => ['color' => '#D97706', 'bg' => '#FEF9C3'],
        'critical' => ['color' => '#DC2626', 'bg' => '#FEE2E2'],
    ];
    $prio = $prioStyles[$ticket->priority] ?? ['color' => '#6A7686', 'bg' => '#F1F3F6'];
@endphp

<!-- Icon + Title -->
<table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 24px;">
<tr>
<td valign="top" style="padding-right: 14px;">
    <div style="width: 42px; height: 42px; background-color: #DBEAFE; border-radius: 12px; text-align: center; line-height: 42px; font-size: 18px;">&#x1F3AB;</div>
</td>
<td valign="middle">
    <h2 style="margin: 0 0 2px; color: #080C1A; font-size: 20px; font-weight: 700; letter-spacing: -0.3px;">New Ticket Created</h2>
    <p style="margin: 0; color: #6A7686; font-size: 13px;">A new support ticket has been submitted and requires attention.</p>
</td>
</tr>
</table>

<!-- Ticket Number Highlight -->
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 20px;">
<tr>
<td style="background-color: #F0F5FF; border-radius: 10px; padding: 14px 18px; border-left: 4px solid #165DFF;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
    <td>
        <span style="font-size: 11px; color: #6A7686; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">Ticket Number</span><br>
        <span style="font-size: 18px; color: #165DFF; font-weight: 700; font-family: 'Courier New', Courier, monospace; letter-spacing: 0.5px;">{{ $ticket->ticket_number }}</span>
    </td>
    <td align="right" valign="middle">
        <span style="display: inline-block; padding: 4px 14px; border-radius: 20px; font-size: 12px; font-weight: 700; color: {{ $prio['color'] }}; background-color: {{ $prio['bg'] }};">{{ ucfirst($ticket->priority) }}</span>
    </td>
    </tr>
    </table>
</td>
</tr>
</table>

<!-- Details Card -->
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #F8FAFC; border-radius: 12px; border: 1px solid #E5E7EB; margin-bottom: 20px;">
<tr>
<td style="padding: 20px;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td style="padding: 7px 0; color: #9CA3AF; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.4px; width: 120px; vertical-align: top;">Title</td>
        <td style="padding: 7px 0; color: #080C1A; font-size: 14px; font-weight: 600;">{{ $ticket->title }}</td>
    </tr>
    <tr><td colspan="2" style="border-bottom: 1px solid #E5E7EB; padding: 0; height: 1px; font-size: 0; line-height: 0;">&nbsp;</td></tr>
    <tr>
        <td style="padding: 7px 0; color: #9CA3AF; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.4px; vertical-align: top;">Status</td>
        <td style="padding: 7px 0;">
            <span style="display: inline-block; padding: 3px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; color: #165DFF; background-color: #DBEAFE;">{{ str_replace('_', ' ', ucfirst($ticket->status)) }}</span>
        </td>
    </tr>
    <tr><td colspan="2" style="border-bottom: 1px solid #E5E7EB; padding: 0; height: 1px; font-size: 0; line-height: 0;">&nbsp;</td></tr>
    <tr>
        <td style="padding: 7px 0; color: #9CA3AF; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.4px; vertical-align: top;">Requester</td>
        <td style="padding: 7px 0; color: #080C1A; font-size: 13px; font-weight: 500;">{{ $ticket->requester?->name }}</td>
    </tr>
    <tr><td colspan="2" style="border-bottom: 1px solid #E5E7EB; padding: 0; height: 1px; font-size: 0; line-height: 0;">&nbsp;</td></tr>
    <tr>
        <td style="padding: 7px 0; color: #9CA3AF; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.4px; vertical-align: top;">Created</td>
        <td style="padding: 7px 0; color: #6A7686; font-size: 13px;">{{ $ticket->created_at->format('M d, Y \a\t H:i') }}</td>
    </tr>
    </table>
</td>
</tr>
</table>

<!-- Description -->
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 28px;">
<tr>
<td>
    <p style="margin: 0 0 8px; color: #9CA3AF; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Description</p>
    <div style="color: #374151; font-size: 14px; line-height: 1.65; background-color: #F8FAFC; border-radius: 10px; padding: 16px; border: 1px solid #E5E7EB;">
        {{ Str::limit($ticket->description, 500) }}
    </div>
</td>
</tr>
</table>

<!-- CTA Button -->
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
<td align="center" style="padding-top: 4px;">
    <!--[if mso]>
    <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" href="{{ route('tickets.show', $ticket) }}" style="height:44px;v-text-anchor:middle;width:200px;" arcsize="23%" fillcolor="#165DFF" stroke="f">
    <center style="color:#ffffff;font-family:Arial,sans-serif;font-size:14px;font-weight:bold;">View Ticket</center>
    </v:roundrect>
    <![endif]-->
    <!--[if !mso]><!-->
    <a href="{{ route('tickets.show', $ticket) }}" style="display: inline-block; padding: 12px 36px; background-color: #165DFF; color: #ffffff; font-size: 14px; font-weight: 600; text-decoration: none; border-radius: 10px; mso-padding-alt: 0;">View Ticket &rarr;</a>
    <!--<![endif]-->
</td>
</tr>
</table>
@endsection
