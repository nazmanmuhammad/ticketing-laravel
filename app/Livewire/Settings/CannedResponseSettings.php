<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\CannedResponse;

#[Layout('layouts.master')]
class CannedResponseSettings extends Component
{
    public string $title = '';
    public string $body = '';
    public ?int $editingId = null;
    public bool $showForm = false;

    public function create(): void
    {
        $this->reset(['title', 'body', 'editingId']);
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $cr = CannedResponse::findOrFail($id);
        $this->editingId = $cr->id;
        $this->title = $cr->title;
        $this->body = $cr->body;
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        if ($this->editingId) {
            CannedResponse::findOrFail($this->editingId)->update([
                'title' => $this->title,
                'body' => $this->body,
            ]);
            $this->dispatch('toast', type: 'success', message: 'Canned response updated');
        } else {
            CannedResponse::create([
                'title' => $this->title,
                'body' => $this->body,
            ]);
            $this->dispatch('toast', type: 'success', message: 'Canned response created');
        }

        $this->reset(['title', 'body', 'editingId', 'showForm']);
    }

    public function delete(int $id): void
    {
        CannedResponse::findOrFail($id)->delete();
        $this->dispatch('toast', type: 'success', message: 'Canned response deleted');
    }

    public function render()
    {
        $responses = CannedResponse::orderBy('title')->get();
        return view('livewire.settings.canned-response-settings', compact('responses'));
    }
}
