@section('title', 'Team Settings')

<div>
    @include('livewire.settings.partials.settings-nav')

    <div class="mt-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-bold text-foreground">Teams</h2>
                <p class="text-sm text-secondary mt-1">Manage teams and their members</p>
            </div>
            <button wire:click="create" class="h-10 px-4 bg-primary hover:bg-primary-hover text-white rounded-xl font-semibold text-sm shadow-md shadow-primary/20 transition-all duration-200 hover:scale-[1.02] active:scale-95 flex items-center gap-2 cursor-pointer">
                <i data-lucide="plus" class="size-4"></i> Add Team
            </button>
        </div>

        @if($showForm)
        <div class="bg-white rounded-2xl border border-border p-6 mb-6" x-data x-transition.duration.200ms>
            <h3 class="font-bold text-foreground mb-4">{{ $editingId ? 'Edit Team' : 'New Team' }}</h3>
            <form wire:submit="save" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1.5">Team Name <span class="text-error">*</span></label>
                    <input wire:model="name" type="text" placeholder="e.g., IT Support Team"
                           class="w-full h-11 px-4 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all duration-200">
                    @error('name') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-foreground mb-1.5">Description</label>
                    <textarea wire:model="description" rows="2" placeholder="Brief description of the team..."
                              class="w-full px-4 py-3 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all duration-200 resize-y"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-foreground mb-1.5">Team Members <span class="text-error">*</span></label>
                    <div class="border border-border rounded-xl p-4 max-h-60 overflow-y-auto space-y-2">
                        @foreach($users as $user)
                        <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-muted transition-colors cursor-pointer">
                            <input type="checkbox" wire:model="selectedMembers" value="{{ $user->id }}"
                                   class="size-4 rounded border-border text-primary focus:ring-2 focus:ring-primary cursor-pointer">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-foreground">{{ $user->name }}</p>
                                <p class="text-xs text-secondary">{{ $user->email }}</p>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @error('selectedMembers') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center gap-3">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model="is_active" class="size-4 rounded border-border text-primary focus:ring-2 focus:ring-primary cursor-pointer">
                        <span class="text-sm font-medium text-foreground">Active</span>
                    </label>
                </div>

                <div class="flex items-center gap-3 pt-4 border-t border-border">
                    <button type="button" wire:click="$set('showForm', false)" class="h-10 px-4 rounded-xl border border-border text-secondary font-semibold text-sm hover:bg-muted transition-all duration-200 cursor-pointer">Cancel</button>
                    <button type="submit" class="h-10 px-4 bg-primary hover:bg-primary-hover text-white rounded-xl font-semibold text-sm shadow-md shadow-primary/20 transition-all duration-200 hover:scale-[1.02] active:scale-95 flex items-center gap-2 cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed disabled:hover:scale-100" wire:loading.attr="disabled" wire:target="save">
                        <svg wire:loading wire:target="save" class="animate-spin size-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        <span wire:loading.remove wire:target="save">{{ $editingId ? 'Update' : 'Create' }}</span>
                        <span wire:loading wire:target="save">Saving...</span>
                    </button>
                </div>
            </form>
        </div>
        @endif

        <div class="bg-white rounded-2xl border border-border overflow-hidden">
            @if($teams->isEmpty())
            <div class="text-center py-12 text-secondary">
                <i data-lucide="users" class="size-12 mx-auto mb-3 opacity-40"></i>
                <p class="font-medium">No teams yet</p>
                <p class="text-sm mt-1">Create your first team to get started</p>
            </div>
            @else
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-border bg-muted/50">
                        <th class="px-4 py-3 text-left font-semibold text-secondary">Team Name</th>
                        <th class="px-4 py-3 text-left font-semibold text-secondary">Members</th>
                        <th class="px-4 py-3 text-left font-semibold text-secondary">Status</th>
                        <th class="px-4 py-3 text-right font-semibold text-secondary">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($teams as $team)
                    <tr class="border-b border-border hover:bg-muted/30 transition-colors duration-150">
                        <td class="px-4 py-3">
                            <p class="font-medium text-foreground">{{ $team->name }}</p>
                            @if($team->description)
                            <p class="text-xs text-secondary mt-0.5">{{ $team->description }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1">
                                <i data-lucide="users" class="size-4 text-secondary"></i>
                                <span class="text-secondary">{{ $team->members->count() }} {{ Str::plural('member', $team->members->count()) }}</span>
                            </div>
                            @if($team->members->isNotEmpty())
                            <p class="text-xs text-secondary mt-1">{{ $team->members->pluck('name')->take(3)->join(', ') }}{{ $team->members->count() > 3 ? '...' : '' }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-lg text-xs font-semibold {{ $team->is_active ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ $team->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button wire:click="edit({{ $team->id }})" class="size-8 flex items-center justify-center rounded-lg text-secondary hover:bg-muted hover:text-primary transition-all duration-200 cursor-pointer" title="Edit">
                                    <i data-lucide="pencil" class="size-4"></i>
                                </button>
                                <button onclick="window.dispatchEvent(new CustomEvent('confirm-delete', { detail: { id: {{ $team->id }}, message: 'Delete team {{ addslashes($team->name) }}?' } }))" class="size-8 flex items-center justify-center rounded-lg text-secondary hover:bg-red-50 hover:text-error transition-all duration-200 cursor-pointer" title="Delete">
                                    <i data-lucide="trash-2" class="size-4"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('confirm-delete', (e) => {
    if (confirm(e.detail.message)) {
        @this.call('delete', e.detail.id);
    }
});
</script>
