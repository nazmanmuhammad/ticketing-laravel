@section('title', 'Workflow Settings')
@section('page-title', 'Settings')
@section('page-description', 'Manage approval workflows')

<div>
    @include('livewire.settings.partials.settings-nav')

    <div class="flex items-center justify-between mb-6">
        <h3 class="font-bold text-foreground">Approval Workflows</h3>
        <button wire:click="create" class="h-9 px-4 bg-primary hover:bg-primary-hover text-white rounded-xl text-sm font-semibold transition-all duration-200 active:scale-95 cursor-pointer flex items-center gap-1.5">
            <i data-lucide="plus" class="size-4"></i> Add Workflow
        </button>
    </div>

    @if($showForm)
    <div class="bg-white rounded-2xl border border-border p-5 mb-6" x-data x-transition>
        <h4 class="font-semibold text-foreground mb-3">{{ $editingId ? 'Edit' : 'Add' }} Workflow Step</h4>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 mb-3">
            <div>
                <label class="block text-xs font-medium text-secondary mb-1">Module</label>
                <select wire:model="module" class="w-full h-10 pl-3 pr-8 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary outline-none transition-all duration-200">
                    <option value="access_request">Access Request</option>
                    <option value="change_request">Change Request</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-secondary mb-1">System (optional)</label>
                <select wire:model="system_id" class="w-full h-10 pl-3 pr-8 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary outline-none transition-all duration-200">
                    <option value="">All Systems</option>
                    @foreach($systems as $sys)
                    <option value="{{ $sys->id }}">{{ $sys->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-secondary mb-1">Level</label>
                <input wire:model="level" type="number" min="1" class="w-full h-10 px-4 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary outline-none transition-all duration-200">
            </div>
            <div>
                <label class="block text-xs font-medium text-secondary mb-1">Approver</label>
                <select wire:model="approver_id" class="w-full h-10 pl-3 pr-8 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary outline-none transition-all duration-200">
                    <option value="">Select approver</option>
                    @foreach($users as $u)
                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-secondary mb-1">Or Role</label>
                <select wire:model="approver_role" class="w-full h-10 pl-3 pr-8 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary outline-none transition-all duration-200">
                    <option value="">None</option>
                    @foreach($roles as $role)
                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-secondary mb-1">SLA (hours)</label>
                <input wire:model="sla_hours" type="number" min="1" class="w-full h-10 px-4 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary outline-none transition-all duration-200">
            </div>
        </div>
        <div class="flex gap-2">
            <button wire:click="save" class="h-9 px-5 bg-primary hover:bg-primary-hover text-white rounded-xl text-sm font-semibold transition-all duration-200 active:scale-95 cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed flex items-center gap-2" wire:loading.attr="disabled" wire:target="save">
                <svg wire:loading wire:target="save" class="animate-spin size-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                <span wire:loading.remove wire:target="save">Save</span>
                <span wire:loading wire:target="save">Saving...</span>
            </button>
            <button wire:click="$set('showForm', false)" class="h-9 px-4 rounded-xl border border-border text-secondary text-sm font-semibold hover:bg-muted transition-all duration-200 cursor-pointer">Cancel</button>
        </div>
    </div>
    @endif

    <div class="bg-white rounded-2xl border border-border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="border-b border-border bg-muted/50">
                    <th class="px-4 py-3 text-left font-semibold text-secondary">Module</th>
                    <th class="px-4 py-3 text-left font-semibold text-secondary">System</th>
                    <th class="px-4 py-3 text-left font-semibold text-secondary">Level</th>
                    <th class="px-4 py-3 text-left font-semibold text-secondary">Approver</th>
                    <th class="px-4 py-3 text-left font-semibold text-secondary">SLA</th>
                    <th class="px-4 py-3 text-left font-semibold text-secondary">Actions</th>
                </tr></thead>
                <tbody>
                    @forelse($workflows as $wf)
                    <tr class="border-b border-border hover:bg-muted/30 transition-colors duration-150">
                        <td class="px-4 py-3"><span class="px-2 py-0.5 rounded-lg text-xs font-semibold bg-primary/10 text-primary">{{ str_replace('_', ' ', ucfirst($wf->module)) }}</span></td>
                        <td class="px-4 py-3 text-secondary">{{ $wf->system?->name ?? 'All' }}</td>
                        <td class="px-4 py-3 font-semibold text-foreground">{{ $wf->level }}</td>
                        <td class="px-4 py-3 text-foreground">{{ $wf->approver?->name ?? $wf->approver_role ?? '-' }}</td>
                        <td class="px-4 py-3 text-secondary">{{ $wf->sla_hours }}h</td>
                        <td class="px-4 py-3">
                            <div class="flex gap-1">
                                <button wire:click="edit({{ $wf->id }})" class="size-8 rounded-lg hover:bg-muted flex items-center justify-center transition-colors cursor-pointer"><i data-lucide="pencil" class="size-3.5 text-secondary"></i></button>
                                <button @click="$dispatch('confirm-dialog', { title: 'Delete Workflow', message: 'Delete this workflow step? This cannot be undone.', onConfirm: () => $wire.delete({{ $wf->id }}) })" class="size-8 rounded-lg hover:bg-red-50 flex items-center justify-center transition-colors cursor-pointer"><i data-lucide="trash-2" class="size-3.5 text-error"></i></button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-12 text-center"><p class="text-secondary font-medium">No workflow steps configured</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
