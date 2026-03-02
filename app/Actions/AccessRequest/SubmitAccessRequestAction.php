<?php

namespace App\Actions\AccessRequest;

use App\Models\AccessRequest;
use App\Models\AccessRequestAttachment;
use App\Models\ApprovalWorkflow;
use App\Models\AccessRequestApproval;
use Illuminate\Support\Facades\DB;

class SubmitAccessRequestAction
{
    public function execute(array $data, array $attachments = []): AccessRequest
    {
        return DB::transaction(function () use ($data, $attachments) {
            $data['request_number'] = AccessRequest::generateNumber();
            $data['status'] = 'submitted';
            $data['current_approval_level'] = 1;

            $request = AccessRequest::create($data);

            foreach ($attachments as $file) {
                $path = $file->store('access-requests/' . $request->id, 'public');
                AccessRequestAttachment::create([
                    'access_request_id' => $request->id,
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                ]);
            }

            $workflows = ApprovalWorkflow::where('module', 'access_request')
                ->where(function ($q) use ($data) {
                    $q->where('system_id', $data['system_id'])->orWhereNull('system_id');
                })
                ->orderBy('level')
                ->get();

            if ($workflows->isEmpty()) {
                $workflows = ApprovalWorkflow::where('module', 'access_request')
                    ->whereNull('system_id')
                    ->orderBy('level')
                    ->get();
            }

            foreach ($workflows as $wf) {
                AccessRequestApproval::create([
                    'access_request_id' => $request->id,
                    'approver_id' => $wf->approver_id,
                    'level' => $wf->level,
                    'status' => 'pending',
                ]);
            }

            // Always set to pending_approval — users with approve permission can approve even without workflow
            $request->update(['status' => 'pending_approval']);

            return $request;
        });
    }
}
