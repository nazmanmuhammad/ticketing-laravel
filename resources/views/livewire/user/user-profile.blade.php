@section('title', 'Profile')
@section('page-title', 'My Profile')
@section('page-description', 'Manage your account settings')

<div class="max-w-2xl space-y-6">
    <!-- Profile Photo -->
    <div class="bg-white rounded-2xl border border-border p-6">
        <h3 class="font-bold text-foreground mb-4">Profile Photo</h3>
        <div class="flex items-center gap-6">
            <div class="relative">
                @if(auth()->user()->profile_photo)
                <img src="{{ Storage::url(auth()->user()->profile_photo) }}" alt="{{ auth()->user()->name }}" class="size-20 rounded-full object-cover border-2 border-border">
                @else
                <div class="size-20 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-2xl border-2 border-border">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                @endif
            </div>
            <div class="flex-1 space-y-3">
                <div>
                    <input wire:model="photo" type="file" accept="image/*" id="photo-upload" class="hidden">
                    <label for="photo-upload" class="inline-flex items-center gap-2 h-10 px-5 bg-primary hover:bg-primary-hover text-white rounded-xl text-sm font-semibold shadow-md shadow-primary/20 transition-all duration-200 hover:scale-[1.02] active:scale-95 cursor-pointer">
                        <i data-lucide="camera" class="size-4"></i>
                        Choose Photo
                    </label>
                    @if(auth()->user()->profile_photo)
                    <button wire:click="removePhoto" class="inline-flex items-center gap-2 h-10 px-4 rounded-xl border border-error text-error text-sm font-semibold hover:bg-error-light transition-all duration-200 cursor-pointer ml-2">
                        <i data-lucide="trash-2" class="size-4"></i>
                        Remove
                    </button>
                    @endif
                </div>
                @if($photo)
                <div class="flex items-center gap-3">
                    <img src="{{ $photo->temporaryUrl() }}" class="size-12 rounded-full object-cover border border-border">
                    <button wire:click="uploadPhoto" class="h-9 px-4 bg-success text-white rounded-xl text-sm font-semibold hover:opacity-90 transition-all duration-200 cursor-pointer flex items-center gap-2">
                        <svg wire:loading wire:target="uploadPhoto" class="animate-spin size-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        Upload
                    </button>
                </div>
                @endif
                @error('photo') <p class="text-xs text-error">{{ $message }}</p> @enderror
                <p class="text-xs text-secondary">JPG, PNG, or GIF. Max 2MB.</p>
            </div>
        </div>
    </div>

    <!-- Profile Info -->
    <div class="bg-white rounded-2xl border border-border p-6">
        <h3 class="font-bold text-foreground mb-4">Profile Information</h3>
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-foreground mb-1.5">Name</label>
                <input wire:model="name" type="text" class="w-full h-11 px-4 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all duration-200">
                @error('name') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-foreground mb-1.5">Email</label>
                <input type="email" value="{{ $email }}" disabled class="w-full h-11 px-4 rounded-xl border border-border bg-muted text-sm text-secondary">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1.5">Phone</label>
                    <input wire:model="phone" type="text" placeholder="+62..." class="w-full h-11 px-4 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all duration-200">
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1.5">Department</label>
                    <input wire:model="department" type="text" placeholder="e.g. IT" class="w-full h-11 px-4 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all duration-200">
                </div>
            </div>
            <div class="flex items-center gap-3">
                <p class="text-xs text-secondary">Role: <span class="font-semibold text-foreground">{{ auth()->user()->roles->pluck('name')->join(', ') ?: 'None' }}</span></p>
            </div>
            <button wire:click="updateProfile" class="h-10 px-5 bg-primary hover:bg-primary-hover text-white rounded-xl text-sm font-semibold shadow-md shadow-primary/20 transition-all duration-200 hover:scale-[1.02] active:scale-95 cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed disabled:hover:scale-100 flex items-center gap-2" wire:loading.attr="disabled" wire:target="updateProfile">
                <svg wire:loading wire:target="updateProfile" class="animate-spin size-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                <span wire:loading.remove wire:target="updateProfile">Save Changes</span>
                <span wire:loading wire:target="updateProfile">Saving...</span>
            </button>
        </div>
    </div>

    <!-- Change Password -->
    <div class="bg-white rounded-2xl border border-border p-6">
        <h3 class="font-bold text-foreground mb-4">Change Password</h3>
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-foreground mb-1.5">Current Password</label>
                <input wire:model="current_password" type="password" class="w-full h-11 px-4 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all duration-200">
                @error('current_password') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1.5">New Password</label>
                    <input wire:model="password" type="password" class="w-full h-11 px-4 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all duration-200">
                    @error('password') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1.5">Confirm New Password</label>
                    <input wire:model="password_confirmation" type="password" class="w-full h-11 px-4 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all duration-200">
                </div>
            </div>
            <button wire:click="updatePassword" class="h-10 px-5 bg-foreground hover:bg-gray-800 text-white rounded-xl text-sm font-semibold transition-all duration-200 hover:scale-[1.02] active:scale-95 cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed disabled:hover:scale-100 flex items-center gap-2" wire:loading.attr="disabled" wire:target="updatePassword">
                <svg wire:loading wire:target="updatePassword" class="animate-spin size-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                <span wire:loading.remove wire:target="updatePassword">Update Password</span>
                <span wire:loading wire:target="updatePassword">Updating...</span>
            </button>
        </div>
    </div>
</div>
