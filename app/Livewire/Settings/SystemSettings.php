<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\System;

#[Layout('layouts.master')]
class SystemSettings extends Component
{
    public string $name = '';
    public string $description = '';
    public bool $is_active = true;
    public ?int $editingId = null;
    public bool $showForm = false;

    public function create(): void
    {
        $this->reset(['name', 'description', 'is_active', 'editingId']);
        $this->is_active = true;
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $sys = System::findOrFail($id);
        $this->editingId = $sys->id;
        $this->name = $sys->name;
        $this->description = $sys->description ?? '';
        $this->is_active = $sys->is_active;
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($this->editingId) {
            System::findOrFail($this->editingId)->update([
                'name' => $this->name,
                'description' => $this->description ?: null,
                'is_active' => $this->is_active,
            ]);
            $this->dispatch('toast', type: 'success', message: 'System updated');
        } else {
            System::create([
                'name' => $this->name,
                'description' => $this->description ?: null,
                'is_active' => $this->is_active,
            ]);
            $this->dispatch('toast', type: 'success', message: 'System created');
        }

        $this->reset(['name', 'description', 'is_active', 'editingId', 'showForm']);
    }

    public function delete(int $id): void
    {
        System::findOrFail($id)->delete();
        $this->dispatch('toast', type: 'success', message: 'System deleted');
    }

    public function render()
    {
        $systems = System::orderBy('name')->get();
        return view('livewire.settings.system-settings', compact('systems'));
    }
}
