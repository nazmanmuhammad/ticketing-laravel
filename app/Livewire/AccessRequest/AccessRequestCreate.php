<?php

namespace App\Livewire\AccessRequest;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use App\Models\System;
use App\Models\User;
use App\Models\Team;
use App\Actions\AccessRequest\SubmitAccessRequestAction;

#[Layout('layouts.master')]
class AccessRequestCreate extends Component
{
    use WithFileUploads;

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

    public function save()
    {
        $this->validate();

        $action = app(SubmitAccessRequestAction::class);
        $request = $action->execute([
            'requester_id' => auth()->id(),
            'system_id' => $this->system_id,
            'access_type' => $this->access_type,
            'custom_access_type' => $this->access_type === 'custom' ? $this->custom_access_type : null,
            'reason' => $this->reason,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date ?: null,
            'assigned_to' => $this->assign_type === 'member' ? $this->assigned_to : null,
            'assigned_team_id' => $this->assign_type === 'team' ? $this->assigned_team_id : null,
        ], $this->attachments);

        $this->dispatch('toast', type: 'success', message: 'Access request submitted: ' . $request->request_number);
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
