<?php

namespace App\Livewire\User;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Hash;

#[Layout('layouts.master')]
class UserProfile extends Component
{
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $department = '';
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function mount(): void
    {
        $user = auth()->user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone ?? '';
        $this->department = $user->department ?? '';
    }

    public function updateProfile(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'department' => 'nullable|string|max:255',
        ]);

        auth()->user()->update([
            'name' => $this->name,
            'phone' => $this->phone ?: null,
            'department' => $this->department ?: null,
        ]);

        $this->dispatch('toast', type: 'success', message: 'Profile updated');
    }

    public function updatePassword(): void
    {
        $this->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($this->current_password, auth()->user()->password)) {
            $this->addError('current_password', 'Current password is incorrect');
            return;
        }

        auth()->user()->update(['password' => Hash::make($this->password)]);
        $this->reset(['current_password', 'password', 'password_confirmation']);
        $this->dispatch('toast', type: 'success', message: 'Password updated');
    }

    public function render()
    {
        return view('livewire.user.user-profile');
    }
}
