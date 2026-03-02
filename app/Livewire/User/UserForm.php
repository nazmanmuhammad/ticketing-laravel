<?php

namespace App\Livewire\User;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\User;
use App\Models\Team;
use App\Models\Department;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

#[Layout('layouts.master')]
class UserForm extends Component
{
    public ?User $user = null;
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public ?int $role_id = null;
    public array $selectedTeams = [];
    public string $department = '';
    public string $phone = '';
    public bool $isEdit = false;

    public function mount(?User $user = null): void
    {
        if ($user && $user->exists) {
            $this->user = $user;
            $this->name = $user->name;
            $this->email = $user->email;
            $this->selectedTeams = $user->teams->pluck('id')->map(fn ($id) => (string) $id)->toArray();
            $this->department = $user->department ?? '';
            $this->phone = $user->phone ?? '';
            $this->role_id = $user->roles->first()?->id;
            $this->isEdit = true;
        }
    }

    protected function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email' . ($this->isEdit ? ',' . $this->user->id : ''),
            'role_id' => 'required|exists:roles,id',
            'selectedTeams' => 'nullable|array',
            'selectedTeams.*' => 'exists:teams,id',
            'department' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
        ];

        if (!$this->isEdit) {
            $rules['password'] = 'required|string|min:8|confirmed';
        } else {
            $rules['password'] = 'nullable|string|min:8|confirmed';
        }

        return $rules;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'department' => $this->department ?: null,
            'phone' => $this->phone ?: null,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->isEdit) {
            $this->user->update($data);
            $user = $this->user;
        } else {
            $data['email_verified_at'] = now();
            $user = User::create($data);
        }

        $role = Role::findById($this->role_id);
        $user->syncRoles([$role->name]);
        $user->teams()->sync($this->selectedTeams);

        $this->dispatch('toast', type: 'success', message: $this->isEdit ? 'User updated' : 'User created');
        return redirect()->route('users.index');
    }

    public function render()
    {
        $roles = Role::orderBy('name')->get();
        $teams = Team::where('is_active', true)->get();
        $departments = Department::where('is_active', true)->get();

        return view('livewire.user.user-form', compact('roles', 'teams', 'departments'));
    }
}
