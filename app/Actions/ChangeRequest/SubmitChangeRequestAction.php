<?php

namespace App\Actions\ChangeRequest;

use App\Models\ChangeRequest;
use App\Models\ChangeRequestAttachment;
use App\Models\ChangeRequestActivity;
use App\Models\ChangeRequestApproval;
use App\Models\ApprovalWorkflow;
use Illuminate\Support\Facades\DB;

class SubmitChangeRequestAction
{
    public function execute(array $data, array $attachments = [], array $perRequestApprovers = []): ChangeRequest
    {
        return DB::transaction(function () use ($data, $attachments, $perRequestApprovers) {
            $data['request_number'] = ChangeRequest::generateNumber();
            $data['status'] = 'submitted';

            $cr = ChangeRequest::create($data);

            foreach ($attachments as $file) {
                $path = $file->store('change-requests/' . $cr->id, 'public');
                ChangeRequestAttachment::create([
                    'change_request_id' => $cr->id,
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                ]);
            }

            ChangeRequestActivity::create([
                'change_request_id' => $cr->id,
                'user_id' => $data['requester_id'],
                'action' => 'submitted',
                'description' => 'Change request submitted',
            ]);

            if (!empty($perRequestApprovers)) {
                $cr->update(['status' => 'under_review']);
                foreach ($perRequestApprovers as $approver) {
                    ChangeRequestApproval::create([
                        'change_request_id' => $cr->id,
                        'approver_id' => $approver['user_id'],
                        'level' => $approver['level'],
                        'status' => 'pending',
                    ]);
                }
            } elseif ($data['change_type'] === 'standard') {
                $cr->update(['status' => 'approved']);
            } else {
                $cr->update(['status' => 'under_review']);
                $workflows = ApprovalWorkflow::where('module', 'change_request')
                    ->where(function ($q) use ($data) {
                        $q->where('system_id', $data['system_id'])->orWhereNull('system_id');
                    })
                    ->orderBy('level')
                    ->get();

                foreach ($workflows as $wf) {
                    ChangeRequestApproval::create([
                        'change_request_id' => $cr->id,
                        'approver_id' => $wf->approver_id,
                        'level' => $wf->level,
                        'status' => 'pending',
                    ]);
                }
            }

            return $cr;
        });
    }
}
