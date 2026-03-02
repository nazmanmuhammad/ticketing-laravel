<?php

namespace App\Services;

use App\Models\EmailTemplate;
use Illuminate\Support\Facades\Mail;
use App\Mail\HelpdeskNotification;

class NotificationService
{
    public function send(string $event, string $toEmail, array $replacements = []): void
    {
        $template = EmailTemplate::where('event', $event)->where('is_active', true)->first();
        if (!$template) return;

        $subject = $this->replacePlaceholders($template->subject, $replacements);
        $body = $this->replacePlaceholders($template->body, $replacements);

        Mail::to($toEmail)->queue(new HelpdeskNotification($subject, $body));
    }

    private function replacePlaceholders(string $text, array $replacements): string
    {
        foreach ($replacements as $key => $value) {
            $text = str_replace('{' . $key . '}', $value, $text);
        }
        return $text;
    }
}
