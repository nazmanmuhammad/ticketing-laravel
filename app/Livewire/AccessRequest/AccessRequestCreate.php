<?php

namespace App\Livewire\AccessRequest;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use App\Models\AccessRequest;
use App\Models\System;
use App\Models\User;
use App\Models\Team;
use App\Actions\AccessRequest\SubmitAccessRequestAction;
use App\Mail\ApprovalRequestMail;
use Illuminate\Support\Facades\Mail;

#[Layout('layouts.master')]
class AccessRequestCreate extends Component
{
    use WithFileUploads;

    public bool $isEdit = false;
    public ?AccessRequest $accessRequest = null;
    public ?int $system_id = null;
    public string $access_type = 'read';
    public string $custom_access_type = '';
    public string $reason = '';
    public string $start_date = '';
    public string $end_date = '';
    public string $assign_type = '';
    public ?int $assigned_to = null;
    public ?int $assigned_team_id = null;
    public array $attachments = [];
    public bool $resolve_immediately = false;
    public string $resolution_notes = '';
    public string $needs_approval = 'no';
    public array $approvers = [];

    public function mount(?AccessRequest $accessRequest = null): void
    {
        if ($accessRequest && $accessRequest->exists) {
            $this->isEdit = true;
            $this->accessRequest = $accessRequest;
            $this->system_id = $accessRequest->system_id;
            $this->access_type = $accessRequest->access_type;
            $this->custom_access_type = $accessRequest->custom_access_type ?? '';
            $this->reason = $accessRequest->reason;
            $this->start_date = $accessRequest->start_date->format('Y-m-d');
            $this->end_date = $accessRequest->end_date?->format('Y-m-d') ?? '';
            $this->assigned_to = $accessRequest->assigned_to;
            $this->assigned_team_id = $accessRequest->assigned_team_id;
            if ($this->assigned_to) $this->assign_type = 'member';
            elseif ($this->assigned_team_id) $this->assign_type = 'team';
        }
    }

    protected function rules(): array
    {
        return [
            'system_id' => 'required|exists:systems,id',
            'access_type' => 'required|in:read,write,admin,custom',
            'custom_access_type' => 'required_if:access_type,custom|nullable|string|max:255',
            'reason' => 'required|string',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'nullable|date|after:start_date',
            'assign_type' => 'nullable|in:member,team',
            'assigned_to' => 'nullable|required_if:assign_type,member|exists:users,id',
            'assigned_team_id' => 'nullable|required_if:assign_type,team|exists:teams,id',
            'attachments.*' => 'nullable|file|max:10240',
        ];
    }

    public function updatedAssignType(): void
    {
        $this->assigned_to = null;
        $this->assigned_team_id = null;
    }

    public function removeAttachment(int $index): void
    {
        unset($this->attachments[$index]);
        $this->attachments = array_values($this->attachments);
    }

    public function updatedNeedsApproval(): void
    {
        if ($this->needs_approval === 'no') {
            $this->approvers = [];
        } elseif (empty($this->approvers)) {
            $this->approvers = [['user_id' => '', 'level' => 1]];
        }
    }

    public function addApprover(): void
    {
        $maxLevel = !empty($this->approvers) ? max(array_column($this->approvers, 'level')) : 0;
        $this->approvers[] = ['user_id' => '', 'level' => $maxLevel + 1];
    }

    public function removeApprover(int $index): void
    {
        unset($this->approvers[$index]);
        $this->approvers = array_values($this->approvers);
    }

    public function save()
    {
        $rules = $this->rules();
        if (!$this->isEdit && $this->needs_approval === 'yes') {
            $rules['approvers'] = 'required|array|min:1';
            $rules['approvers.*.user_id'] = 'required|exists:users,id';
            $rules['approvers.*.level'] = 'required|integer|min:1';
        }
        $this->validate($rules);

        $data = [
            'system_id' => $this->system_id,
            'access_type' => $this->access_type,
            'custom_access_type' => $this->access_type === 'custom' ? $this->custom_access_type : null,
            'reason' => $this->reason,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date ?: null,
            'assigned_to' => $this->assign_type === 'member' ? $this->assigned_to : null,
            'assigned_team_id' => $this->assign_type === 'team' ? $this->assigned_team_id : null,
        ];

        if ($this->isEdit) {
            $this->accessRequest->update($data);

            // Handle new attachments
            if (!empty($this->attachments)) {
                foreach ($this->attachments as $file) {
                    $path = $file->store('access-request-attachments', 'public');
                    $this->accessRequest->attachments()->create([
                        'file_path' => $path,
                        'file_name' => $file->getClientOriginalName(),
                    ]);
                }
            }

            $this->dispatch('toast', type: 'success', message: 'Access request updated: ' . $this->accessRequest->request_number);
            return redirect()->route('access-requests.show', $this->accessRequest);
        }

        $data['requester_id'] = auth()->id();
        $action = app(SubmitAccessRequestAction::class);
        $request = $action->execute($data, $this->attachments, $this->needs_approval === 'yes' ? $this->approvers : []);

        // Send approval email to level 1 approvers only
        if ($this->needs_approval === 'yes' && !empty($this->approvers)) {
            $minLevel = min(array_column($this->approvers, 'level'));
            $level1Approvers = array_filter($this->approvers, fn($a) => (int)$a['level'] === $minLevel);
            foreach ($level1Approvers as $approver) {
                $approverUser = User::find($approver['user_id']);
                if ($approverUser) {
                    try {
                        Mail::to($approverUser->email)->send(new ApprovalRequestMail(
                            $request, 'access_request', $approverUser->name, $minLevel,
                            route('access-requests.show', $request)
                        ));
                    } catch (\Throwable $e) {
                        \Log::warning('Failed to send AR approval email: ' . $e->getMessage());
                    }
                }
            }
        }

        if ($this->resolve_immediately) {
            $request->update(['status' => 'implemented']);
            $this->dispatch('toast', type: 'success', message: 'Access request submitted & implemented: ' . $request->request_number);
        } else {
            $this->dispatch('toast', type: 'success', message: 'Access request submitted: ' . $request->request_number);
        }

        return redirect()->route('access-requests.show', $request);
    }

    public function render()
    {
        $systems = System::where('is_active', true)->get();
        $users = User::orderBy('name')->get();
        $teams = Team::where('is_active', true)->orderBy('name')->get();
        return view('livewire.access-request.access-request-create', compact('systems', 'users', 'teams'));
    }
}
