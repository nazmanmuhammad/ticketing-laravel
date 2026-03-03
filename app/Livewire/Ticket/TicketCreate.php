<?php

namespace App\Livewire\Ticket;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use App\Models\Category;
use App\Models\User;
use App\Models\Team;
use App\Models\TicketApproval;
use App\Models\TicketActivity;
use App\Actions\Ticket\CreateTicketAction;
use App\Mail\TicketCreatedMail;
use App\Mail\ApprovalRequestMail;
use Illuminate\Support\Facades\Mail;

#[Layout('layouts.master')]
class TicketCreate extends Component
{
    use WithFileUploads;

    public string $title = '';
    public string $description = '';
    public ?int $category_id = null;
    public ?int $sub_category_id = null;
    public string $priority = 'medium';
    public string $assign_type = '';
    public ?int $assigned_to = null;
    public ?int $assigned_team_id = null;
    public array $attachments = [];
    public bool $resolve_immediately = false;
    public string $resolution_notes = '';
    public string $needs_approval = 'no';
    public array $approvers = [];

    protected function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'nullable|exists:categories,id',
            'priority' => 'required|in:low,medium,high,critical',
            'assign_type' => 'nullable|in:member,team',
            'assigned_to' => 'nullable|required_if:assign_type,member|exists:users,id',
            'assigned_team_id' => 'nullable|required_if:assign_type,team|exists:teams,id',
            'attachments.*' => 'nullable|file|max:10240',
        ];
    }

    public function updatedCategoryId(): void
    {
        $this->sub_category_id = null;
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
        if ($this->needs_approval === 'yes') {
            $rules['approvers'] = 'required|array|min:1';
            $rules['approvers.*.user_id'] = 'required|exists:users,id';
            $rules['approvers.*.level'] = 'required|integer|min:1';
        }
        $this->validate($rules);

        $data = [
            'title' => $this->title,
            'description' => $this->description,
            'category_id' => $this->category_id,
            'sub_category_id' => $this->sub_category_id,
            'priority' => $this->priority,
            'requester_id' => auth()->id(),
        ];

        if ($this->assign_type === 'member' && $this->assigned_to) {
            $data['assigned_to'] = $this->assigned_to;
        }
        if ($this->assign_type === 'team' && $this->assigned_team_id) {
            $data['assigned_team_id'] = $this->assigned_team_id;
        }

        $action = app(CreateTicketAction::class);
        $ticket = $action->execute($data, $this->attachments);

        if ($this->needs_approval === 'yes' && !empty($this->approvers)) {
            foreach ($this->approvers as $approver) {
                TicketApproval::create([
                    'ticket_id' => $ticket->id,
                    'approver_id' => $approver['user_id'],
                    'level' => $approver['level'],
                    'status' => 'pending',
                ]);
            }
            $ticket->update(['status' => 'pending_approval']);
            TicketActivity::create([
                'ticket_id' => $ticket->id,
                'user_id' => auth()->id(),
                'action' => 'approval_requested',
                'description' => 'Internal approval requested (' . count($this->approvers) . ' approver(s))',
            ]);
        }

        // Send ticket created email to requester
        try {
            Mail::to($ticket->requester->email)->send(new TicketCreatedMail($ticket));
        } catch (\Throwable $e) {
            \Log::warning('Failed to send ticket created email: ' . $e->getMessage());
        }

        // Send approval email to level 1 approvers only
        if ($this->needs_approval === 'yes' && !empty($this->approvers)) {
            $minLevel = min(array_column($this->approvers, 'level'));
            $level1Approvers = array_filter($this->approvers, fn($a) => (int)$a['level'] === $minLevel);
            foreach ($level1Approvers as $approver) {
                $approverUser = User::find($approver['user_id']);
                if ($approverUser) {
                    try {
                        Mail::to($approverUser->email)->send(new ApprovalRequestMail(
                            $ticket, 'ticket', $approverUser->name, $minLevel,
                            route('tickets.show', $ticket)
                        ));
                    } catch (\Throwable $e) {
                        \Log::warning('Failed to send approval email: ' . $e->getMessage());
                    }
                }
            }
        }

        if ($this->resolve_immediately) {
            $ticket->update(['status' => 'closed', 'closed_at' => now()]);
            \App\Models\TicketActivity::create([
                'ticket_id' => $ticket->id,
                'user_id' => auth()->id(),
                'action' => 'closed',
                'description' => 'Resolved immediately upon creation' . ($this->resolution_notes ? ': ' . $this->resolution_notes : ''),
            ]);
            $this->dispatch('toast', type: 'success', message: 'Ticket created & closed: ' . $ticket->ticket_number);
        } else {
            $this->dispatch('toast', type: 'success', message: 'Ticket created: ' . $ticket->ticket_number);
        }

        return redirect()->route('tickets.show', $ticket);
    }

    public function render()
    {
        $categories = Category::parents()->get();
        $subCategories = $this->category_id
            ? Category::where('parent_id', $this->category_id)->get()
            : collect();
        $users = User::orderBy('name')->get();
        $teams = Team::where('is_active', true)->orderBy('name')->get();

        return view('livewire.ticket.ticket-create', compact('categories', 'subCategories', 'users', 'teams'));
    }
}
