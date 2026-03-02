<?php

namespace App\Services;

use App\Models\ChangeRequest;
use App\Models\ChangeRequestActivity;

class ChangeRequestService
{
    public function schedule(ChangeRequest $cr, string $scheduledAt, int $userId): ChangeRequest
    {
        $cr->update(['scheduled_at' => $scheduledAt, 'status' => 'scheduled']);

        ChangeRequestActivity::create([
            'change_request_id' => $cr->id,
            'user_id' => $userId,
            'action' => 'scheduled',
            'description' => 'Scheduled for ' . $scheduledAt,
        ]);

        return $cr;
    }

    public function markImplemented(ChangeRequest $cr, int $userId): ChangeRequest
    {
        $cr->update(['status' => 'implemented', 'implemented_at' => now()]);

        ChangeRequestActivity::create([
            'change_request_id' => $cr->id,
            'user_id' => $userId,
            'action' => 'implemented',
            'description' => 'Change request implemented',
        ]);

        return $cr;
    }

    public function markFailed(ChangeRequest $cr, int $userId, string $reason): ChangeRequest
    {
        $cr->update(['status' => 'failed']);

        ChangeRequestActivity::create([
            'change_request_id' => $cr->id,
            'user_id' => $userId,
            'action' => 'failed',
            'description' => 'Implementation failed: ' . $reason,
        ]);

        return $cr;
    }

    public function close(ChangeRequest $cr, int $userId, ?string $postReviewNotes = null): ChangeRequest
    {
        $cr->update(['status' => 'closed', 'post_review_notes' => $postReviewNotes]);

        ChangeRequestActivity::create([
            'change_request_id' => $cr->id,
            'user_id' => $userId,
            'action' => 'closed',
            'description' => 'Change request closed',
        ]);

        return $cr;
    }

    public function getConflicts(int $systemId, string $scheduledAt): \Illuminate\Support\Collection
    {
        $start = \Carbon\Carbon::parse($scheduledAt)->subHours(2);
        $end = \Carbon\Carbon::parse($scheduledAt)->addHours(2);

        return ChangeRequest::where('system_id', $systemId)
            ->whereNotIn('status', ['closed', 'failed', 'draft'])
            ->whereBetween('scheduled_at', [$start, $end])
            ->get();
    }
}
