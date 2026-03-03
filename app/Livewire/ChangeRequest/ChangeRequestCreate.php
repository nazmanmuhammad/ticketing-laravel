<?php

namespace App\Livewire\ChangeRequest;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use App\Models\ChangeRequest;
use App\Models\System;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Team;
use App\Actions\ChangeRequest\SubmitChangeRequestAction;
use App\Mail\ApprovalRequestMail;
use Illuminate\Support\Facades\Mail;

#[Layout('layouts.master')]
class ChangeRequestCreate extends Component
{
    use WithFileUploads;

    public bool $isEdit = false;
    public ?ChangeRequest $changeRequest = null;
    public string $title = '';
    public string $description = '';
    public string $change_type = 'normal';
    public ?int $system_id = null;
    public string $impact = '';
    public string $risk = '';
    public string $rollback_plan = '';
    public string $scheduled_at = '';
    public ?int $related_ticket_id = null;
    public string $assign_type = '';
    public ?int $assigned_to = null;
    public ?int $assigned_team_id = null;
    public array $attachments = [];
    public bool $resolve_immediately = false;
    public string $resolution_notes = '';
    public string $needs_approval = 'no';
    public array $approvers = [];

    public function mount(?ChangeRequest $changeRequest = null): void
    {
        if ($changeRequest && $changeRequest->exists) {
            $this->isEdit = true;
            $this->changeRequest = $changeRequest;
            $this->title = $changeRequest->title;
            $this->description = $changeRequest->description;
            $this->change_type = $changeRequest->change_type;
            $this->system_id = $changeRequest->system_id;
            $this->impact = $changeRequest->impact ?? '';
            $this->risk = $changeRequest->risk ?? '';
            $this->rollback_plan = $changeRequest->rollback_plan ?? '';
            $this->scheduled_at = $changeRequest->scheduled_at?->format('Y-m-d\TH:i') ?? '';
            $this->related_ticket_id = $changeRequest->related_ticket_id;
            $this->assigned_to = $changeRequest->assigned_to;
            $this->assigned_team_id = $changeRequest->assigned_team_id;
            if ($this->assigned_to) $this->assign_type = 'member';
            elseif ($this->assigned_team_id) $this->assign_type = 'team';
        }
    }

    protected function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'change_type' => 'required|in:standard,normal,emergency',
            'system_id' => 'required|exists:systems,id',
            'impact' => 'nullable|string',
            'risk' => 'nullable|string',
            'rollback_plan' => 'nullable|string',
            'scheduled_at' => 'nullable|date|after:now',
            'related_ticket_id' => 'nullable|exists:tickets,id',
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
            'title' => $this->title,
            'description' => $this->description,
            'change_type' => $this->change_type,
            'system_id' => $this->system_id,
            'impact' => $this->impact ?: null,
            'risk' => $this->risk ?: null,
            'rollback_plan' => $this->rollback_plan ?: null,
            'scheduled_at' => $this->scheduled_at ?: null,
            'related_ticket_id' => $this->related_ticket_id,
            'assigned_to' => $this->assign_type === 'member' ? $this->assigned_to : null,
            'assigned_team_id' => $this->assign_type === 'team' ? $this->assigned_team_id : null,
        ];

        if ($this->isEdit) {
            $this->changeRequest->update($data);

            // Handle new attachments
            if (!empty($this->attachments)) {
                foreach ($this->attachments as $file) {
                    $path = $file->store('change-request-attachments', 'public');
                    $this->changeRequest->attachments()->create([
                        'file_path' => $path,
                        'file_name' => $file->getClientOriginalName(),
                    ]);
                }
            }

            $this->dispatch('toast', type: 'success', message: 'Change request updated: ' . $this->changeRequest->request_number);
            return redirect()->route('change-requests.show', $this->changeRequest);
        }

        $data['requester_id'] = auth()->id();
        $action = app(SubmitChangeRequestAction::class);
        $cr = $action->execute($data, $this->attachments, $this->needs_approval === 'yes' ? $this->approvers : []);

        // Send approval email to level 1 approvers only
        if ($this->needs_approval === 'yes' && !empty($this->approvers)) {
            $minLevel = min(array_column($this->approvers, 'level'));
            $level1Approvers = array_filter($this->approvers, fn($a) => (int)$a['level'] === $minLevel);
            foreach ($level1Approvers as $approver) {
                $approverUser = User::find($approver['user_id']);
                if ($approverUser) {
                    try {
                        Mail::to($approverUser->email)->send(new ApprovalRequestMail(
                            $cr, 'change_request', $approverUser->name, $minLevel,
                            route('change-requests.show', $cr)
                        ));
                    } catch (\Throwable $e) {
                        \Log::warning('Failed to send CR approval email: ' . $e->getMessage());
                    }
                }
            }
        }

        if ($this->resolve_immediately) {
            $cr->update(['status' => 'closed', 'implemented_at' => now()]);
            \App\Models\ChangeRequestActivity::create([
                'change_request_id' => $cr->id,
                'user_id' => auth()->id(),
                'action' => 'closed',
                'description' => 'Resolved immediately upon creation' . ($this->resolution_notes ? ': ' . $this->resolution_notes : ''),
            ]);
            $this->dispatch('toast', type: 'success', message: 'Change request submitted & closed: ' . $cr->request_number);
        } else {
            $this->dispatch('toast', type: 'success', message: 'Change request submitted: ' . $cr->request_number);
        }

        return redirect()->route('change-requests.show', $cr);
    }

    public function render()
    {
        $systems = System::where('is_active', true)->get();
        $tickets = Ticket::whereNotIn('status', ['closed'])->orderByDesc('created_at')->limit(50)->get();
        $users = User::orderBy('name')->get();
        $teams = Team::where('is_active', true)->orderBy('name')->get();
        return view('livewire.change-request.change-request-create', compact('systems', 'tickets', 'users', 'teams'));
    }
}
