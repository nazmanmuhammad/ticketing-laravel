@section('title', $isEdit ? 'Edit User' : 'Create User')
@section('page-title', $isEdit ? 'Edit User' : 'Create User')
@section('page-description', $isEdit ? 'Update user information' : 'Add a new user')

<div class="max-w-2xl">
    <form wire:submit="save" class="space-y-6">
        <div class="bg-white rounded-2xl border border-border p-6 space-y-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1.5">Full Name <span class="text-error">*</span></label>
                    <input wire:model="name" type="text" placeholder="John Doe" class="w-full h-11 px-4 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all duration-200">
                    @error('name') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1.5">Email <span class="text-error">*</span></label>
                    <input wire:model="email" type="email" placeholder="john@company.com" class="w-full h-11 px-4 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all duration-200">
                    @error('email') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1.5">Password {{ $isEdit ? '' : '*' }}</label>
                    <input wire:model="password" type="password" placeholder="{{ $isEdit ? 'Leave blank to keep current' : '••••••••' }}" class="w-full h-11 px-4 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all duration-200">
                    @error('password') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1.5">Confirm Password</label>
                    <input wire:model="password_confirmation" type="password" placeholder="••••••••" class="w-full h-11 px-4 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all duration-200">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-foreground mb-1.5">Role <span class="text-error">*</span></label>
                <select wire:model="role_id" class="w-full h-11 pl-4 pr-10 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary outline-none transition-all duration-200">
                    <option value="">Select role</option>
                    @foreach($roles as $role)
                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                    @endforeach
                </select>
                @error('role_id') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-foreground mb-1.5">Teams</label>
                <div class="border border-border rounded-xl p-3 max-h-40 overflow-y-auto space-y-2">
                    @if($teams->isEmpty())
                    <p class="text-sm text-secondary text-center py-2">No teams available</p>
                    @else
                    @foreach($teams as $team)
                    <label class="flex items-center gap-2 p-2 rounded-lg hover:bg-muted transition-colors cursor-pointer">
                        <input type="checkbox" wire:model="selectedTeams" value="{{ $team->id }}"
                               class="size-4 rounded border-border text-primary focus:ring-2 focus:ring-primary cursor-pointer">
                        <span class="text-sm text-foreground">{{ $team->name }}</span>
                    </label>
                    @endforeach
                    @endif
                </div>
                @error('selectedTeams') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1.5">Department</label>
                    <input wire:model="department" type="text" placeholder="e.g. IT" list="departments-list" class="w-full h-11 px-4 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all duration-200">
                    <datalist id="departments-list">
                        @foreach($departments as $dept)
                        <option value="{{ $dept->name }}">
                        @endforeach
                    </datalist>
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1.5">Phone</label>
                    <input wire:model="phone" type="text" placeholder="+62..." class="w-full h-11 px-4 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all duration-200">
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('users.index') }}" class="h-11 px-6 rounded-xl border border-border text-secondary font-semibold text-sm hover:bg-muted transition-all duration-200 flex items-center cursor-pointer">Cancel</a>
            <button type="submit" class="h-11 px-6 bg-primary hover:bg-primary-hover text-white rounded-xl font-semibold text-sm shadow-md shadow-primary/20 transition-all duration-200 hover:scale-[1.02] active:scale-95 cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed disabled:hover:scale-100 flex items-center gap-2" wire:loading.attr="disabled" wire:target="save">
                <svg wire:loading wire:target="save" class="animate-spin size-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                <span wire:loading.remove wire:target="save">{{ $isEdit ? 'Update User' : 'Create User' }}</span>
                <span wire:loading wire:target="save">{{ $isEdit ? 'Updating...' : 'Creating...' }}</span>
            </button>
        </div>
    </form>
</div>
