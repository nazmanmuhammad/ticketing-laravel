<?php

namespace App\Livewire\User;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\User;
use Spatie\Permission\Models\Role;

#[Layout('layouts.master')]
class UserList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $roleFilter = '';

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingRoleFilter(): void { $this->resetPage(); }

    public function deleteUser(int $id): void
    {
        $user = User::findOrFail($id);
        if ($user->hasRole('Super Admin')) {
            $this->dispatch('toast', type: 'error', message: 'Cannot delete Super Admin');
            return;
        }
        $user->delete();
        $this->dispatch('toast', type: 'success', message: 'User deleted');
    }

    public function render()
    {
        $users = User::with('roles', 'teams')
            ->when($this->search, fn ($q) => $q->where(fn ($q2) => $q2->where('name', 'like', "%{$this->search}%")->orWhere('email', 'like', "%{$this->search}%")))
            ->when($this->roleFilter, fn ($q) => $q->role($this->roleFilter))
            ->orderBy('name')
            ->paginate(15);

        $roles = Role::orderBy('name')->get();

        return view('livewire.user.user-list', compact('users', 'roles'));
    }
}
