<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\SlaSetting;

#[Layout('layouts.master')]
class SlaSettings extends Component
{
    public array $slaData = [];

    public function mount(): void
    {
        $this->loadData();
    }

    private function loadData(): void
    {
        $this->slaData = SlaSetting::all()->map(fn ($s) => [
            'id' => $s->id,
            'priority' => $s->priority,
            'response_hours' => $s->response_hours,
            'resolution_hours' => $s->resolution_hours,
        ])->toArray();
    }

    public function save(): void
    {
        foreach ($this->slaData as $item) {
            SlaSetting::where('id', $item['id'])->update([
                'response_hours' => max(1, (int) $item['response_hours']),
                'resolution_hours' => max(1, (int) $item['resolution_hours']),
            ]);
        }

        $this->loadData();
        $this->dispatch('toast', type: 'success', message: 'SLA settings updated');
    }

    public function render()
    {
        return view('livewire.settings.sla-settings');
    }
}
