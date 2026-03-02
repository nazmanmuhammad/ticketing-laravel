<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Team;
use App\Models\User;

#[Layout('layouts.master')]
class TeamSettings extends Component
{
    public string $name = '';
    public string $description = '';
    public bool $is_active = true;
    public array $selectedMembers = [];
    public ?int $editingId = null;
    public bool $showForm = false;

    public function create(): void
    {
        $this->reset(['name', 'description', 'is_active', 'selectedMembers', 'editingId']);
        $this->is_active = true;
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $team = Team::with('members')->findOrFail($id);
        $this->editingId = $team->id;
        $this->name = $team->name;
        $this->description = $team->description ?? '';
        $this->is_active = $team->is_active;
        $this->selectedMembers = $team->members->pluck('id')->map(fn ($id) => (string) $id)->toArray();
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'selectedMembers' => 'required|array|min:1',
            'selectedMembers.*' => 'exists:users,id',
        ]);

        if ($this->editingId) {
            $team = Team::findOrFail($this->editingId);
            $team->update([
                'name' => $this->name,
                'description' => $this->description ?: null,
                'is_active' => $this->is_active,
            ]);
            $team->members()->sync($this->selectedMembers);
            $this->dispatch('toast', type: 'success', message: 'Team updated');
        } else {
            $team = Team::create([
                'name' => $this->name,
                'description' => $this->description ?: null,
                'is_active' => $this->is_active,
            ]);
            $team->members()->sync($this->selectedMembers);
            $this->dispatch('toast', type: 'success', message: 'Team created');
        }

        $this->reset(['name', 'description', 'is_active', 'selectedMembers', 'editingId', 'showForm']);
    }

    public function delete(int $id): void
    {
        Team::findOrFail($id)->delete();
        $this->dispatch('toast', type: 'success', message: 'Team deleted');
    }

    public function render()
    {
        $teams = Team::with('members')->orderBy('name')->get();
        $users = User::orderBy('name')->get();
        return view('livewire.settings.team-settings', compact('teams', 'users'));
    }
}
