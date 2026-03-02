<?php

namespace App\Livewire\Role;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

#[Layout('layouts.master')]
class RoleForm extends Component
{
    public ?Role $role = null;
    public string $name = '';
    public array $selectedPermissions = [];
    public bool $isEdit = false;

    public function mount(?Role $role = null): void
    {
        if ($role && $role->exists) {
            $this->role = $role;
            $this->name = $role->name;
            $this->selectedPermissions = $role->permissions->pluck('id')->map(fn ($id) => (string) $id)->toArray();
            $this->isEdit = true;
        }
    }

    public function togglePermission(int $id): void
    {
        $key = (string) $id;
        if (in_array($key, $this->selectedPermissions)) {
            $this->selectedPermissions = array_values(array_diff($this->selectedPermissions, [$key]));
        } else {
            $this->selectedPermissions[] = $key;
        }
    }

    public function selectAll(): void
    {
        $this->selectedPermissions = Permission::pluck('id')->map(fn ($id) => (string) $id)->toArray();
    }

    public function deselectAll(): void
    {
        $this->selectedPermissions = [];
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:roles,name' . ($this->isEdit ? ',' . $this->role->id : ''),
        ]);

        if ($this->isEdit) {
            $this->role->update(['name' => $this->name]);
            $this->role->syncPermissions(Permission::whereIn('id', $this->selectedPermissions)->get());
            $this->dispatch('toast', type: 'success', message: 'Role updated');
        } else {
            $role = Role::create(['name' => $this->name]);
            $role->syncPermissions(Permission::whereIn('id', $this->selectedPermissions)->get());
            $this->dispatch('toast', type: 'success', message: 'Role created');
        }

        return redirect()->route('roles.index');
    }

    public function render()
    {
        $permissions = Permission::all()->groupBy(fn ($p) => explode('.', $p->name)[0]);
        return view('livewire.role.role-form', compact('permissions'));
    }
}
