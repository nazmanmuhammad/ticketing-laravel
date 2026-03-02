@section('title', $isEdit ? 'Edit Role' : 'Create Role')
@section('page-title', $isEdit ? 'Edit Role' : 'Create Role')
@section('page-description', $isEdit ? 'Modify role permissions' : 'Create a new role')

<div class="max-w-3xl">
    <form wire:submit="save" class="space-y-6">
        <div class="bg-white rounded-2xl border border-border p-6">
            <label class="block text-sm font-medium text-foreground mb-1.5">Role Name <span class="text-error">*</span></label>
            <input wire:model="name" type="text" placeholder="e.g. Support Lead"
                   class="w-full h-11 px-4 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all duration-200"
                   {{ $isEdit && $role->name === 'Super Admin' ? 'disabled' : '' }}>
            @error('name') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="bg-white rounded-2xl border border-border p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-foreground">Permissions</h3>
                <div class="flex gap-2">
                    <button type="button" wire:click="selectAll" class="text-xs font-semibold text-primary hover:underline cursor-pointer">Select All</button>
                    <span class="text-secondary">|</span>
                    <button type="button" wire:click="deselectAll" class="text-xs font-semibold text-secondary hover:text-foreground hover:underline cursor-pointer">Deselect All</button>
                </div>
            </div>

            <div class="space-y-5">
                @foreach($permissions as $module => $perms)
                <div>
                    <h4 class="text-xs font-semibold text-secondary uppercase tracking-wider mb-2">{{ str_replace('_', ' ', $module) }}</h4>
                    <div class="flex flex-wrap gap-2">
                        @foreach($perms as $perm)
                        @php $isSelected = in_array((string) $perm->id, $selectedPermissions); @endphp
                        <button type="button" wire:click="togglePermission({{ $perm->id }})"
                                class="px-3 py-1.5 rounded-lg text-xs font-semibold border transition-all duration-200 cursor-pointer {{ $isSelected ? 'bg-primary/10 border-primary/30 text-primary' : 'bg-white border-border text-secondary hover:border-primary hover:text-primary' }}">
                            {{ explode('.', $perm->name)[1] ?? $perm->name }}
                        </button>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('roles.index') }}" class="h-11 px-6 rounded-xl border border-border text-secondary font-semibold text-sm hover:bg-muted transition-all duration-200 flex items-center cursor-pointer">Cancel</a>
            <button type="submit" class="h-11 px-6 bg-primary hover:bg-primary-hover text-white rounded-xl font-semibold text-sm shadow-md shadow-primary/20 transition-all duration-200 hover:scale-[1.02] active:scale-95 cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed disabled:hover:scale-100 flex items-center gap-2" wire:loading.attr="disabled" wire:target="save">
                <svg wire:loading wire:target="save" class="animate-spin size-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                <span wire:loading.remove wire:target="save">{{ $isEdit ? 'Update Role' : 'Create Role' }}</span>
                <span wire:loading wire:target="save">{{ $isEdit ? 'Updating...' : 'Creating...' }}</span>
            </button>
        </div>
    </form>
</div>
