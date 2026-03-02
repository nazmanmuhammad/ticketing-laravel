@section('title', 'Create Ticket')
@section('page-title', 'Create Ticket')
@section('page-description', 'Submit a new support request')

<div class="max-w-3xl">
    <form wire:submit="save" class="space-y-6">
        <!-- Title -->
        <div>
            <label class="block text-sm font-medium text-foreground mb-1.5">Title <span class="text-error">*</span></label>
            <input wire:model="title" type="text" placeholder="Brief description of the issue"
                   class="w-full h-11 px-4 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all duration-200">
            @error('title') <p class="text-xs text-error mt-1" x-data x-transition.duration.200ms>{{ $message }}</p> @enderror
        </div>

        <!-- Description -->
        <div>
            <label class="block text-sm font-medium text-foreground mb-1.5">Description <span class="text-error">*</span></label>
            <textarea wire:model="description" rows="5" placeholder="Provide full details about the issue..."
                      class="w-full px-4 py-3 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all duration-200 resize-y"></textarea>
            @error('description') <p class="text-xs text-error mt-1" x-data x-transition.duration.200ms>{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Category -->
            <div>
                <label class="block text-sm font-medium text-foreground mb-1.5">Category <span class="text-error">*</span></label>
                <select wire:model.live="category_id" class="w-full h-11 pl-4 pr-10 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary outline-none transition-all duration-200">
                    <option value="">Select category</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
                @error('category_id') <p class="text-xs text-error mt-1" x-data x-transition.duration.200ms>{{ $message }}</p> @enderror
            </div>

            <!-- Sub Category -->
            <div>
                <label class="block text-sm font-medium text-foreground mb-1.5">Sub-Category</label>
                <select wire:model="sub_category_id" class="w-full h-11 pl-4 pr-10 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary outline-none transition-all duration-200" {{ $subCategories->isEmpty() ? 'disabled' : '' }}>
                    <option value="">Select sub-category</option>
                    @foreach($subCategories as $sub)
                    <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Priority -->
        <div>
            <label class="block text-sm font-medium text-foreground mb-1.5">Priority <span class="text-error">*</span></label>
            <div class="flex flex-wrap gap-2">
                @foreach(['low' => 'Low', 'medium' => 'Medium', 'high' => 'High', 'critical' => 'Critical'] as $val => $label)
                @php
                    $colors = ['low' => 'border-green-300 bg-green-50 text-green-700', 'medium' => 'border-yellow-300 bg-yellow-50 text-yellow-700', 'high' => 'border-orange-300 bg-orange-50 text-orange-700', 'critical' => 'border-red-300 bg-red-50 text-red-700'];
                    $active = $priority === $val;
                @endphp
                <button type="button" wire:click="$set('priority', '{{ $val }}')"
                        class="px-4 py-2 rounded-xl border text-sm font-semibold transition-all duration-200 cursor-pointer {{ $active ? $colors[$val] . ' ring-2 ring-offset-1' : 'border-border bg-white text-secondary hover:border-primary' }}">
                    {{ $label }}
                </button>
                @endforeach
            </div>
        </div>

        <!-- Assignment -->
        <div>
            <label class="block text-sm font-medium text-foreground mb-1.5">Assign To</label>
            <select wire:model.live="assign_type" class="w-full h-11 pl-4 pr-10 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary outline-none transition-all duration-200">
                <option value="">Unassigned</option>
                <option value="member">Member (PIC)</option>
                <option value="team">Team</option>
            </select>
        </div>

        @if($assign_type === 'member')
        <div>
            <label class="block text-sm font-medium text-foreground mb-1.5">Select Member / PIC <span class="text-error">*</span></label>
            <select wire:model="assigned_to" class="w-full h-11 pl-4 pr-10 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary outline-none transition-all duration-200">
                <option value="">Select user</option>
                @foreach($users as $u)
                <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                @endforeach
            </select>
            @error('assigned_to') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
        </div>
        @endif

        @if($assign_type === 'team')
        <div>
            <label class="block text-sm font-medium text-foreground mb-1.5">Select Team <span class="text-error">*</span></label>
            <select wire:model="assigned_team_id" class="w-full h-11 pl-4 pr-10 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary outline-none transition-all duration-200">
                <option value="">Select team</option>
                @foreach($teams as $t)
                <option value="{{ $t->id }}">{{ $t->name }}</option>
                @endforeach
            </select>
            @error('assigned_team_id') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
        </div>
        @endif

        <!-- Attachments -->
        <div>
            <label class="block text-sm font-medium text-foreground mb-1.5">Attachments</label>
            <div class="border-2 border-dashed border-border rounded-xl p-6 text-center hover:border-primary transition-colors duration-200 cursor-pointer relative">
                <input wire:model="attachments" type="file" multiple class="absolute inset-0 opacity-0 cursor-pointer">
                <i data-lucide="upload-cloud" class="size-8 text-secondary mx-auto mb-2"></i>
                <p class="text-sm text-secondary">Click or drag files here (max 10MB each)</p>
                <div wire:loading wire:target="attachments" class="mt-2 text-xs text-primary font-medium">Uploading...</div>
            </div>
            @if(!empty($attachments))
            <div class="mt-3 space-y-2">
                @foreach($attachments as $i => $file)
                <div class="flex items-center justify-between p-2 rounded-lg bg-muted text-sm" x-data x-transition.duration.200ms>
                    <span class="truncate">{{ $file->getClientOriginalName() }}</span>
                    <button type="button" wire:click="removeAttachment({{ $i }})" class="text-error hover:text-red-700 transition-colors cursor-pointer"><i data-lucide="x" class="size-4"></i></button>
                </div>
                @endforeach
            </div>
            @endif
            @error('attachments.*') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-3 pt-4 border-t border-border">
            <a href="{{ route('tickets.index') }}" class="h-11 px-6 rounded-xl border border-border text-secondary font-semibold text-sm hover:bg-muted transition-all duration-200 flex items-center cursor-pointer">Cancel</a>
            <button type="submit" class="h-11 px-6 bg-primary hover:bg-primary-hover text-white rounded-xl font-semibold text-sm shadow-md shadow-primary/20 transition-all duration-200 hover:scale-[1.02] active:scale-95 flex items-center gap-2 cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed disabled:hover:scale-100" wire:loading.attr="disabled" wire:target="save">
                <svg wire:loading wire:target="save" class="animate-spin size-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                <span wire:loading.remove wire:target="save">Create Ticket</span>
                <span wire:loading wire:target="save">Creating...</span>
            </button>
        </div>
    </form>
</div>
