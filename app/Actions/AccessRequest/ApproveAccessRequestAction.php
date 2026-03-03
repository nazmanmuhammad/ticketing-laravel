<?php

namespace App\Actions\AccessRequest;

use App\Models\AccessRequest;
use App\Models\AccessRequestApproval;
use App\Models\User;
use App\Mail\ApprovalRequestMail;
use Illuminate\Support\Facades\Mail;

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
            $pendingAtSameOrLower = $request->approvals()
                ->where('level', '<=', $approval->level)
                ->where('status', 'pending')
                ->count();

            if ($pendingAtSameOrLower === 0) {
                $nextLevelApprovals = $request->approvals()
                    ->where('level', '>', $approval->level)
                    ->where('status', 'pending')
                    ->get();

                if ($nextLevelApprovals->isEmpty()) {
                    $request->update(['status' => 'approved']);
                } else {
                    $nextMinLevel = $nextLevelApprovals->min('level');
                    $request->update(['current_approval_level' => $nextMinLevel]);
                    $nextLevelOnly = $nextLevelApprovals->where('level', $nextMinLevel);
                    foreach ($nextLevelOnly as $nextApproval) {
                        $nextApproval->load('approver');
                        if ($nextApproval->approver) {
                            try {
                                Mail::to($nextApproval->approver->email)->send(new ApprovalRequestMail(
                                    $request, 'access_request', $nextApproval->approver->name, $nextMinLevel,
                                    route('access-requests.show', $request)
                                ));
                            } catch (\Throwable $e) {
                                \Log::warning('Failed to send access request approval email: ' . $e->getMessage());
                            }
                        }
                    }
                }
            }
        } elseif ($action === 'rejected') {
            $request->update(['status' => 'rejected']);
        } elseif ($action === 'info_requested') {
            $request->update(['status' => 'info_requested']);
        }

        return $request->fresh();
    }
}
