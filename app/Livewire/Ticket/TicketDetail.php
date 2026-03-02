<?php

namespace App\Livewire\Ticket;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use App\Models\Ticket;
use App\Models\CannedResponse;
use App\Models\User;
use App\Models\Team;
use App\Services\TicketService;
use App\Actions\Ticket\AssignTicketAction;
use App\Actions\Ticket\CloseTicketAction;

#[Layout('layouts.master')]
class TicketDetail extends Component
{
    use WithFileUploads;

    public Ticket $ticket;
    public string $commentBody = '';
    public bool $isInternal = false;
    public ?int $assignToUser = null;
    public ?int $assignToTeam = null;
    public string $newStatus = '';
    public string $newPriority = '';
    public bool $showAssignModal = false;

    public function mount(Ticket $ticket): void
    {
        $this->ticket = $ticket->load(['requester', 'assignee', 'team', 'category', 'subCategory', 'comments.user', 'attachments', 'activities.user']);
        $this->newStatus = $ticket->status;
        $this->newPriority = $ticket->priority;
        $this->assignToUser = $ticket->assigned_to;
        $this->assignToTeam = $ticket->assigned_team_id;
    }

    public function addComment(): void
    {
        $this->validate(['commentBody' => 'required|string']);

        $service = app(TicketService::class);
        $service->addComment($this->ticket, auth()->id(), $this->commentBody, $this->isInternal);

        $this->commentBody = '';
        $this->isInternal = false;
        $this->ticket->refresh();
        $this->ticket->load(['comments.user', 'activities.user']);
        $this->dispatch('toast', type: 'success', message: 'Comment added');
    }

    public function applyCannedResponse(int $id): void
    {
        $canned = CannedResponse::find($id);
        if ($canned) {
            $this->commentBody = $canned->body;
        }
    }

    public function updateStatus(): void
    {
        if ($this->newStatus === $this->ticket->status) return;

        $service = app(TicketService::class);
        $service->changeStatus($this->ticket, $this->newStatus, auth()->id());
        $this->ticket->refresh();
        $this->dispatch('toast', type: 'success', message: 'Status updated');
    }

    public function updatePriority(): void
    {
        if ($this->newPriority === $this->ticket->priority) return;

        $service = app(TicketService::class);
        $service->changePriority($this->ticket, $this->newPriority, auth()->id());
        $this->ticket->refresh();
        $this->dispatch('toast', type: 'success', message: 'Priority updated');
    }

    public function assignTicket(): void
    {
        $action = app(AssignTicketAction::class);
        $action->execute($this->ticket, $this->assignToUser, $this->assignToTeam, auth()->id());
        $this->ticket->refresh();
        $this->ticket->load(['assignee', 'team']);
        $this->showAssignModal = false;
        $this->dispatch('toast', type: 'success', message: 'Ticket assigned');
    }

    public function closeTicket(): void
    {
        $action = app(CloseTicketAction::class);
        $action->execute($this->ticket, auth()->id());
        $this->ticket->refresh();
        $this->newStatus = 'closed';
        $this->dispatch('toast', type: 'success', message: 'Ticket closed');
    }

    public function render()
    {
        $cannedResponses = CannedResponse::all();
        $agents = User::role(['Agent', 'Admin', 'Super Admin'])->get();
        $teams = Team::where('is_active', true)->get();
        
        // Check if user can work on this ticket
        $user = auth()->user();
        $teamIds = $user->teams()->pluck('teams.id')->toArray();
        $canWork = $this->ticket->assigned_to === $user->id
            || ($this->ticket->assigned_team_id && in_array($this->ticket->assigned_team_id, $teamIds))
            || $user->can('tickets.assign');

        return view('livewire.ticket.ticket-detail', compact('cannedResponses', 'agents', 'teams', 'canWork'));
    }
}
