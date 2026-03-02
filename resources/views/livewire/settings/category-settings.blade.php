@section('title', 'Category Settings')
@section('page-title', 'Settings')
@section('page-description', 'Manage categories')

<div>
    @include('livewire.settings.partials.settings-nav')

    <div class="flex items-center justify-between mb-6">
        <h3 class="font-bold text-foreground">Categories</h3>
        <button wire:click="create" class="h-9 px-4 bg-primary hover:bg-primary-hover text-white rounded-xl text-sm font-semibold transition-all duration-200 active:scale-95 cursor-pointer flex items-center gap-1.5">
            <i data-lucide="plus" class="size-4"></i> Add Category
        </button>
    </div>

    @if($showForm)
    <div class="bg-white rounded-2xl border border-border p-5 mb-6" x-data x-transition>
        <h4 class="font-semibold text-foreground mb-3">{{ $editingId ? 'Edit' : 'Add' }} Category</h4>
        <div class="flex flex-col sm:flex-row gap-3">
            <input wire:model="name" type="text" placeholder="Category name" class="flex-1 h-10 px-4 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all duration-200">
            <select wire:model="parent_id" class="h-10 pl-3 pr-8 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary outline-none transition-all duration-200">
                <option value="">Parent (none = top level)</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
            <button wire:click="save" class="h-10 px-5 bg-primary hover:bg-primary-hover text-white rounded-xl text-sm font-semibold transition-all duration-200 active:scale-95 cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed flex items-center gap-2" wire:loading.attr="disabled" wire:target="save">
                <svg wire:loading wire:target="save" class="animate-spin size-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                <span wire:loading.remove wire:target="save">Save</span>
                <span wire:loading wire:target="save">Saving...</span>
            </button>
            <button wire:click="$set('showForm', false)" class="h-10 px-4 rounded-xl border border-border text-secondary text-sm font-semibold hover:bg-muted transition-all duration-200 cursor-pointer">Cancel</button>
        </div>
        @error('name') <p class="text-xs text-error mt-2">{{ $message }}</p> @enderror
    </div>
    @endif

    <div class="space-y-3">
        @foreach($categories as $cat)
        <div class="bg-white rounded-2xl border border-border p-4">
            <div class="flex items-center justify-between">
                <span class="font-semibold text-foreground">{{ $cat->name }}</span>
                <div class="flex gap-1">
                    <button wire:click="edit({{ $cat->id }})" class="size-8 rounded-lg hover:bg-muted flex items-center justify-center transition-colors cursor-pointer"><i data-lucide="pencil" class="size-3.5 text-secondary"></i></button>
                    <button @click="$dispatch('confirm-dialog', { title: 'Delete Category', message: 'Delete \'{{ $cat->name }}\' and its sub-categories? This cannot be undone.', onConfirm: () => $wire.delete({{ $cat->id }}) })" class="size-8 rounded-lg hover:bg-red-50 flex items-center justify-center transition-colors cursor-pointer"><i data-lucide="trash-2" class="size-3.5 text-error"></i></button>
                </div>
            </div>
            @if($cat->children->isNotEmpty())
            <div class="ml-6 mt-2 space-y-1.5">
                @foreach($cat->children as $child)
                <div class="flex items-center justify-between py-1.5 px-3 rounded-lg bg-muted/50">
                    <span class="text-sm text-foreground">{{ $child->name }}</span>
                    <div class="flex gap-1">
                        <button wire:click="edit({{ $child->id }})" class="size-7 rounded-md hover:bg-white flex items-center justify-center transition-colors cursor-pointer"><i data-lucide="pencil" class="size-3 text-secondary"></i></button>
                        <button @click="$dispatch('confirm-dialog', { title: 'Delete Category', message: 'Delete \'{{ $child->name }}\'? This cannot be undone.', onConfirm: () => $wire.delete({{ $child->id }}) })" class="size-7 rounded-md hover:bg-red-50 flex items-center justify-center transition-colors cursor-pointer"><i data-lucide="trash-2" class="size-3 text-error"></i></button>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
        @endforeach
    </div>
</div>
