<?php

namespace App\Livewire\ChangeRequest;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use App\Models\System;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Team;
use App\Actions\ChangeRequest\SubmitChangeRequestAction;

#[Layout('layouts.master')]
class ChangeRequestCreate extends Component
{
    use WithFileUploads;

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

    public function save()
    {
        $this->validate();

        $action = app(SubmitChangeRequestAction::class);
        $cr = $action->execute([
            'requester_id' => auth()->id(),
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
        ], $this->attachments);

        $this->dispatch('toast', type: 'success', message: 'Change request submitted: ' . $cr->request_number);
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
