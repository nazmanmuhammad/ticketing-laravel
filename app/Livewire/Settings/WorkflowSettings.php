<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\ApprovalWorkflow;
use App\Models\System;
use App\Models\User;
use Spatie\Permission\Models\Role;

#[Layout('layouts.master')]
class WorkflowSettings extends Component
{
    public string $module = 'access_request';
    public ?int $system_id = null;
    public int $level = 1;
    public ?int $approver_id = null;
    public string $approver_role = '';
    public int $sla_hours = 24;
    public ?int $editingId = null;
    public bool $showForm = false;

    public function create(): void
    {
        $this->reset(['module', 'system_id', 'level', 'approver_id', 'approver_role', 'sla_hours', 'editingId']);
        $this->module = 'access_request';
        $this->level = 1;
        $this->sla_hours = 24;
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $wf = ApprovalWorkflow::findOrFail($id);
        $this->editingId = $wf->id;
        $this->module = $wf->module;
        $this->system_id = $wf->system_id;
        $this->level = $wf->level;
        $this->approver_id = $wf->approver_id;
        $this->approver_role = $wf->approver_role ?? '';
        $this->sla_hours = $wf->sla_hours;
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate([
            'module' => 'required|in:access_request,change_request',
            'system_id' => 'nullable|exists:systems,id',
            'level' => 'required|integer|min:1',
            'approver_id' => 'nullable|exists:users,id',
            'approver_role' => 'nullable|string|max:255',
            'sla_hours' => 'required|integer|min:1',
        ]);

        $data = [
            'module' => $this->module,
            'system_id' => $this->system_id,
            'level' => $this->level,
            'approver_id' => $this->approver_id,
            'approver_role' => $this->approver_role ?: null,
            'sla_hours' => $this->sla_hours,
        ];

        if ($this->editingId) {
            ApprovalWorkflow::findOrFail($this->editingId)->update($data);
            $this->dispatch('toast', type: 'success', message: 'Workflow updated');
        } else {
            ApprovalWorkflow::create($data);
            $this->dispatch('toast', type: 'success', message: 'Workflow created');
        }

        $this->reset(['module', 'system_id', 'level', 'approver_id', 'approver_role', 'sla_hours', 'editingId', 'showForm']);
    }

    public function delete(int $id): void
    {
        ApprovalWorkflow::findOrFail($id)->delete();
        $this->dispatch('toast', type: 'success', message: 'Workflow deleted');
    }

    public function render()
    {
        $workflows = ApprovalWorkflow::with(['system', 'approver'])->orderBy('module')->orderBy('level')->get();
        $systems = System::where('is_active', true)->get();
        $users = User::orderBy('name')->get();
        $roles = Role::orderBy('name')->get();

        return view('livewire.settings.workflow-settings', compact('workflows', 'systems', 'users', 'roles'));
    }
}
