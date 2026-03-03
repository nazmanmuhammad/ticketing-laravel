<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApprovalRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $type;
    public string $number;
    public string $title;
    public string $approverName;
    public int $level;
    public string $url;

    public function __construct(
        public Model $request,
        string $type,
        string $approverName,
        int $level,
        string $url
    ) {
        $this->type = $type;
        $this->approverName = $approverName;
        $this->level = $level;
        $this->url = $url;

        if ($type === 'ticket') {
            $this->number = $request->ticket_number;
            $this->title = $request->title;
        } elseif ($type === 'access_request') {
            $this->number = $request->request_number;
            $this->title = 'Access Request - ' . ($request->system?->name ?? 'N/A');
        } elseif ($type === 'change_request') {
            $this->number = $request->request_number ?? $request->cr_number ?? '';
            $this->title = $request->title ?? 'Change Request';
        }
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "[{$this->number}] Approval Required (Level {$this->level}): {$this->title}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.approval-request',
        );
    }
}
