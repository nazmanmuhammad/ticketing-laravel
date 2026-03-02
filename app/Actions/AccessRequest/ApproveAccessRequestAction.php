<?php

namespace App\Actions\AccessRequest;

use App\Models\AccessRequest;
use App\Models\AccessRequestApproval;
use App\Models\User;

class ApproveAccessRequestAction
{
    public function execute(AccessRequest $request, int $approverId, string $action, ?string $notes = null): AccessRequest
    {
        // Try to find existing approval entry for this user
        $approval = $request->approvals()
            ->where('approver_id', $approverId)
            ->where('status', 'pending')
            ->first();

        // If no workflow entry exists, check if user has permission to approve directly
        if (!$approval) {
            $user = User::find($approverId);
            if ($user && $user->canAny(['access_requests.approve', 'access_requests.reject'])) {
                // Create ad-hoc approval record for permission-based approval
                $currentLevel = $request->current_approval_level ?? 1;
                $approval = AccessRequestApproval::create([
                    'access_request_id' => $request->id,
                    'approver_id' => $approverId,
                    'level' => $currentLevel,
                    'status' => 'pending',
                ]);
            } else {
                return $request;
            }
        }

        $approval->update([
            'status' => $action,
            'notes' => $notes,
            'acted_at' => now(),
        ]);

        if ($action === 'approved') {
            $nextLevel = $request->approvals()
                ->where('level', '>', $approval->level)
                ->where('status', 'pending')
                ->first();
            if ($nextLevel) {
                $request->update(['current_approval_level' => $nextLevel->level]);
            } else {
                $request->update(['status' => 'approved']);
            }
        } elseif ($action === 'rejected') {
            $request->update(['status' => 'rejected']);
        } elseif ($action === 'info_requested') {
            $request->update(['status' => 'pending_approval']);
        }

        return $request->fresh();
    }
}
