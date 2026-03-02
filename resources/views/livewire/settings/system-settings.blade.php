@section('title', 'System Settings')
@section('page-title', 'Settings')
@section('page-description', 'Manage systems')

<div>
    @include('livewire.settings.partials.settings-nav')

    <div class="flex items-center justify-between mb-6">
        <h3 class="font-bold text-foreground">Systems</h3>
        <button wire:click="create" class="h-9 px-4 bg-primary hover:bg-primary-hover text-white rounded-xl text-sm font-semibold transition-all duration-200 active:scale-95 cursor-pointer flex items-center gap-1.5">
            <i data-lucide="plus" class="size-4"></i> Add System
        </button>
    </div>

    @if($showForm)
    <div class="bg-white rounded-2xl border border-border p-5 mb-6" x-data x-transition>
        <h4 class="font-semibold text-foreground mb-3">{{ $editingId ? 'Edit' : 'Add' }} System</h4>
        <div class="space-y-3">
            <div class="flex flex-col sm:flex-row gap-3">
                <input wire:model="name" type="text" placeholder="System name" class="flex-1 h-10 px-4 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all duration-200">
                <input wire:model="description" type="text" placeholder="Description (optional)" class="flex-1 h-10 px-4 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all duration-200">
            </div>
            <div class="flex items-center gap-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input wire:model="is_active" type="checkbox" class="w-4 h-4 rounded border-border text-primary focus:ring-primary">
                    <span class="text-sm text-foreground">Active</span>
                </label>
                <div class="flex gap-2 ml-auto">
                    <button wire:click="save" class="h-9 px-5 bg-primary hover:bg-primary-hover text-white rounded-xl text-sm font-semibold transition-all duration-200 active:scale-95 cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed flex items-center gap-2" wire:loading.attr="disabled" wire:target="save">
                        <svg wire:loading wire:target="save" class="animate-spin size-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        <span wire:loading.remove wire:target="save">Save</span>
                        <span wire:loading wire:target="save">Saving...</span>
                    </button>
                    <button wire:click="$set('showForm', false)" class="h-9 px-4 rounded-xl border border-border text-secondary text-sm font-semibold hover:bg-muted transition-all duration-200 cursor-pointer">Cancel</button>
                </div>
            </div>
        </div>
        @error('name') <p class="text-xs text-error mt-2">{{ $message }}</p> @enderror
    </div>
    @endif

    <div class="bg-white rounded-2xl border border-border overflow-hidden">
        <table class="w-full text-sm">
            <thead><tr class="border-b border-border bg-muted/50">
                <th class="px-4 py-3 text-left font-semibold text-secondary">Name</th>
                <th class="px-4 py-3 text-left font-semibold text-secondary">Description</th>
                <th class="px-4 py-3 text-left font-semibold text-secondary">Status</th>
                <th class="px-4 py-3 text-left font-semibold text-secondary">Actions</th>
            </tr></thead>
            <tbody>
                @foreach($systems as $sys)
                <tr class="border-b border-border hover:bg-muted/30 transition-colors duration-150">
                    <td class="px-4 py-3 font-medium text-foreground">{{ $sys->name }}</td>
                    <td class="px-4 py-3 text-secondary">{{ $sys->description ?? '-' }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded-lg text-xs font-semibold {{ $sys->is_active ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-600' }}">{{ $sys->is_active ? 'Active' : 'Inactive' }}</span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex gap-1">
                            <button wire:click="edit({{ $sys->id }})" class="size-8 rounded-lg hover:bg-muted flex items-center justify-center transition-colors cursor-pointer"><i data-lucide="pencil" class="size-3.5 text-secondary"></i></button>
                            <button @click="$dispatch('confirm-dialog', { title: 'Delete System', message: 'Delete \'{{ $sys->name }}\'? This cannot be undone.', onConfirm: () => $wire.delete({{ $sys->id }}) })" class="size-8 rounded-lg hover:bg-red-50 flex items-center justify-center transition-colors cursor-pointer"><i data-lucide="trash-2" class="size-3.5 text-error"></i></button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
