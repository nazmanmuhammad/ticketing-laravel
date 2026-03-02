<?php

namespace App\Services;

use App\Models\AccessRequest;

class AccessRequestService
{
    public function getMyRequests(int $userId)
    {
        return AccessRequest::where('requester_id', $userId)->latest()->paginate(15);
    }

    public function getPendingApprovals(int $approverId)
    {
        return AccessRequest::where('status', 'pending_approval')
            ->whereHas('approvals', function ($q) use ($approverId) {
                $q->where('approver_id', $approverId)->where('status', 'pending');
            })
            ->with(['requester', 'system'])
            ->latest()
            ->paginate(15);
    }

    public function markImplemented(AccessRequest $request): AccessRequest
    {
        $request->update(['status' => 'implemented']);
        return $request;
    }
}
