<?php

namespace App\Actions\ChangeRequest;

use App\Models\ChangeRequest;
use App\Models\ChangeRequestActivity;
use App\Models\ChangeRequestApproval;
use App\Models\User;

class ApproveChangeRequestAction
{
    public function execute(ChangeRequest $cr, int $approverId, string $action, ?string $notes = null): ChangeRequest
    {
        // Try to find existing approval entry for this user
        $approval = $cr->approvals()->where('approver_id', $approverId)->where('status', 'pending')->first();

        // If no workflow entry exists, check if user has permission to approve directly
        if (!$approval) {
            $user = User::find($approverId);
            if ($user && $user->canAny(['change_requests.approve', 'change_requests.reject'])) {
                $maxLevel = $cr->approvals()->max('level') ?? 0;
                $approval = ChangeRequestApproval::create([
                    'change_request_id' => $cr->id,
                    'approver_id' => $approverId,
                    'level' => $maxLevel + 1,
                    'status' => 'pending',
                ]);
            } else {
                return $cr;
            }
        }

        $approval->update([
            'status' => $action,
            'notes' => $notes,
            'acted_at' => now(),
        ]);

        ChangeRequestActivity::create([
            'change_request_id' => $cr->id,
            'user_id' => $approverId,
            'action' => $action,
            'description' => "Change request {$action}" . ($notes ? ": {$notes}" : ''),
        ]);

        if ($action === 'approved') {
            $pendingApprovals = $cr->approvals()->where('status', 'pending')->count();
            if ($pendingApprovals === 0) {
                $cr->update(['status' => 'approved']);
            }
        } elseif ($action === 'rejected') {
            $cr->update(['status' => 'rejected']);
        } elseif ($action === 'info_requested') {
            $cr->update(['status' => 'under_review']);
        } elseif ($action === 'rescheduled') {
            $cr->update(['status' => 'under_review']);
        }

        return $cr->fresh();
    }
}
