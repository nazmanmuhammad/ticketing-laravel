<?php

namespace App\Livewire\Ticket;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use App\Models\Category;
use App\Models\User;
use App\Models\Team;
use App\Actions\Ticket\CreateTicketAction;

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

    public function save()
    {
        $this->validate();

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

        $this->dispatch('toast', type: 'success', message: 'Ticket created: ' . $ticket->ticket_number);
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
