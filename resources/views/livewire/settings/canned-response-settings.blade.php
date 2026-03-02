@section('title', 'Canned Responses')
@section('page-title', 'Settings')
@section('page-description', 'Manage canned responses')

<div>
    @include('livewire.settings.partials.settings-nav')

    <div class="flex items-center justify-between mb-6">
        <h3 class="font-bold text-foreground">Canned Responses</h3>
        <button wire:click="create" class="h-9 px-4 bg-primary hover:bg-primary-hover text-white rounded-xl text-sm font-semibold transition-all duration-200 active:scale-95 cursor-pointer flex items-center gap-1.5">
            <i data-lucide="plus" class="size-4"></i> Add Response
        </button>
    </div>

    @if($showForm)
    <div class="bg-white rounded-2xl border border-border p-5 mb-6" x-data x-transition>
        <h4 class="font-semibold text-foreground mb-3">{{ $editingId ? 'Edit' : 'Add' }} Canned Response</h4>
        <div class="space-y-3">
            <input wire:model="title" type="text" placeholder="Response title" class="w-full h-10 px-4 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all duration-200">
            @error('title') <p class="text-xs text-error">{{ $message }}</p> @enderror
            <textarea wire:model="body" rows="4" placeholder="Response body..."
                      class="w-full px-4 py-3 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all duration-200 resize-y"></textarea>
            @error('body') <p class="text-xs text-error">{{ $message }}</p> @enderror
            <div class="flex gap-2">
                <button wire:click="save" class="h-9 px-5 bg-primary hover:bg-primary-hover text-white rounded-xl text-sm font-semibold transition-all duration-200 active:scale-95 cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed flex items-center gap-2" wire:loading.attr="disabled" wire:target="save">
                    <svg wire:loading wire:target="save" class="animate-spin size-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    <span wire:loading.remove wire:target="save">Save</span>
                    <span wire:loading wire:target="save">Saving...</span>
                </button>
                <button wire:click="$set('showForm', false)" class="h-9 px-4 rounded-xl border border-border text-secondary text-sm font-semibold hover:bg-muted transition-all duration-200 cursor-pointer">Cancel</button>
            </div>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @forelse($responses as $resp)
        <div class="bg-white rounded-2xl border border-border p-5 hover:shadow-md transition-all duration-200">
            <div class="flex items-start justify-between mb-2">
                <h4 class="font-semibold text-foreground">{{ $resp->title }}</h4>
                <div class="flex gap-1 shrink-0">
                    <button wire:click="edit({{ $resp->id }})" class="size-7 rounded-lg hover:bg-muted flex items-center justify-center transition-colors cursor-pointer"><i data-lucide="pencil" class="size-3 text-secondary"></i></button>
                    <button @click="$dispatch('confirm-dialog', { title: 'Delete Response', message: 'Delete \'{{ $resp->title }}\'? This cannot be undone.', onConfirm: () => $wire.delete({{ $resp->id }}) })" class="size-7 rounded-lg hover:bg-red-50 flex items-center justify-center transition-colors cursor-pointer"><i data-lucide="trash-2" class="size-3 text-error"></i></button>
                </div>
            </div>
            <p class="text-sm text-secondary line-clamp-3">{{ $resp->body }}</p>
        </div>
        @empty
        <div class="col-span-2 text-center py-12">
            <p class="text-secondary font-medium">No canned responses yet</p>
        </div>
        @endforelse
    </div>
</div>
