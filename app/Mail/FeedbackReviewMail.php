<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FeedbackReviewMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $type;
    public string $number;
    public string $requestTitle;
    public string $url;
    public string $commenterName;
    public string $commentBody;
    public array $publicComments;

    public function __construct(
        public Model $request,
        string $type,
        string $commenterName,
        string $commentBody,
        string $url
    ) {
        $this->type = $type;
        $this->commenterName = $commenterName;
        $this->commentBody = $commentBody;
        $this->url = $url;

        if ($type === 'ticket') {
            $this->number = $request->ticket_number ?? '';
            $this->requestTitle = $request->title ?? '';
        } elseif ($type === 'access_request') {
            $this->number = $request->request_number ?? '';
            $this->requestTitle = 'Access Request - ' . ($request->system?->name ?? 'N/A');
        } elseif ($type === 'change_request') {
            $this->number = $request->request_number ?? '';
            $this->requestTitle = $request->title ?? 'Change Request';
        }

        // Collect non-internal comments
        $comments = $request->comments()
            ->where('is_internal', false)
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        $this->publicComments = $comments->map(fn ($c) => [
            'user' => $c->user->name ?? 'Unknown',
            'body' => $c->body,
            'date' => $c->created_at->format('M d, Y H:i'),
        ])->toArray();
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "[{$this->number}] Feedback Review: {$this->requestTitle}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.feedback-review',
        );
    }
}
