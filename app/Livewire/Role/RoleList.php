<?php

namespace App\Livewire\Role;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Spatie\Permission\Models\Role;

#[Layout('layouts.master')]
class RoleList extends Component
{
    public string $search = '';

    public function deleteRole(int $id): void
    {
        $role = Role::findOrFail($id);
        if (in_array($role->name, ['Super Admin'])) {
            $this->dispatch('toast', type: 'error', message: 'Cannot delete Super Admin role');
            return;
        }
        $role->delete();
        $this->dispatch('toast', type: 'success', message: 'Role deleted');
    }

    public function render()
    {
        $roles = Role::withCount('permissions', 'users')
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy('name')
            ->get();

        return view('livewire.role.role-list', compact('roles'));
    }
}
