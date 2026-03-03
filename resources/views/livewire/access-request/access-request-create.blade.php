@section('title', $isEdit ? 'Edit Access Request' : 'New Access Request')
@section('page-title', $isEdit ? 'Edit Access Request' : 'New Access Request')
@section('page-description', $isEdit ? 'Update access request details' : 'Request access to a system')
@section('breadcrumbs')
<span class="text-secondary">Dashboard</span>
<span class="text-secondary">&middot;</span>
<a href="{{ route('access-requests.index') }}" class="text-secondary hover:text-primary transition-colors">Access Requests</a>
<span class="text-secondary">&middot;</span>
<span class="font-semibold text-foreground">{{ $isEdit ? 'Edit' : 'Create New' }}</span>
@endsection

<div class="max-w-3xl">
    <form wire:submit="save" class="space-y-6">
        <div class="bg-white rounded-2xl border border-border p-6 space-y-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1.5">Requester</label>
                    <input type="text" value="{{ auth()->user()->name }}" disabled class="w-full h-11 px-4 rounded-xl border border-border bg-muted text-sm text-secondary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1.5">System <span class="text-error">*</span></label>
                    <select wire:model="system_id" class="w-full h-11 pl-4 pr-10 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary outline-none transition-all duration-200">
                        <option value="">Select system</option>
                        @foreach($systems as $sys)
                        <option value="{{ $sys->id }}">{{ $sys->name }}</option>
                        @endforeach
                    </select>
                    @error('system_id') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1.5">Access Type <span class="text-error">*</span></label>
                    <select wire:model.live="access_type" class="w-full h-11 pl-4 pr-10 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary outline-none transition-all duration-200">
                        <option value="read">Read</option>
                        <option value="write">Write</option>
                        <option value="admin">Admin</option>
                        <option value="custom">Custom</option>
                    </select>
                </div>
                @if($access_type === 'custom')
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1.5">Custom Access Type <span class="text-error">*</span></label>
                    <input wire:model="custom_access_type" type="text" placeholder="Describe custom access"
                           class="w-full h-11 px-4 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all duration-200">
                    @error('custom_access_type') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                </div>
                @endif
            </div>

            <div>
                <label class="block text-sm font-medium text-foreground mb-1.5">Reason / Justification <span class="text-error">*</span></label>
                <textarea wire:model="reason" rows="4" placeholder="Explain why you need this access..."
                          class="w-full px-4 py-3 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all duration-200 resize-y"></textarea>
                @error('reason') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1.5">Start Date <span class="text-error">*</span></label>
                    <input wire:model="start_date" type="date" class="w-full h-11 px-4 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary outline-none transition-all duration-200">
                    @error('start_date') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1.5">End Date <span class="text-secondary text-xs">(optional)</span></label>
                    <input wire:model="end_date" type="date" class="w-full h-11 px-4 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary outline-none transition-all duration-200">
                    @error('end_date') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

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

            <div>
                <label class="block text-sm font-medium text-foreground mb-1.5">Attachments</label>
                <div class="border-2 border-dashed border-border rounded-xl p-6 text-center hover:border-primary transition-colors duration-200 cursor-pointer relative">
                    <input wire:model="attachments" type="file" multiple class="absolute inset-0 opacity-0 cursor-pointer">
                    <i data-lucide="upload-cloud" class="size-8 text-secondary mx-auto mb-2"></i>
                    <p class="text-sm text-secondary">Click or drag files here</p>
                    <div wire:loading wire:target="attachments" class="mt-2 text-xs text-primary font-medium">Uploading...</div>
                </div>
                @if(!empty($attachments))
                <div class="mt-3 space-y-2">
                    @foreach($attachments as $i => $file)
                    <div class="flex items-center justify-between p-2 rounded-lg bg-muted text-sm">
                        <span class="truncate">{{ $file->getClientOriginalName() }}</span>
                        <button type="button" wire:click="removeAttachment({{ $i }})" class="text-error hover:text-red-700 transition-colors cursor-pointer"><i data-lucide="x" class="size-4"></i></button>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        @if(!$isEdit)
        <!-- Internal Approval -->
        <div>
            <label class="block text-sm font-medium text-foreground mb-1.5">Internal Approval</label>
            <select wire:model.live="needs_approval" class="w-full h-11 pl-4 pr-10 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary outline-none transition-all duration-200">
                <option value="no">No</option>
                <option value="yes">Yes</option>
            </select>
        </div>

        @if($needs_approval === 'yes')
        <div class="bg-amber-50 rounded-2xl border border-amber-200 p-5 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-sm font-semibold text-amber-800">Approval Chain</h4>
                    <p class="text-xs text-amber-600 mt-0.5">Add one or more approvers with their approval level</p>
                </div>
                <button type="button" wire:click="addApprover" class="h-8 px-3 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-xs font-semibold transition-all duration-200 cursor-pointer flex items-center gap-1">
                    <i data-lucide="plus" class="size-3.5"></i> Add Approver
                </button>
            </div>

            @foreach($approvers as $idx => $approver)
            <div class="flex items-end gap-3 p-3 bg-white rounded-xl border border-amber-100" wire:key="approver-{{ $idx }}">
                <div class="flex-1">
                    <label class="block text-xs font-medium text-secondary mb-1">Approver <span class="text-error">*</span></label>
                    <select wire:model="approvers.{{ $idx }}.user_id" class="w-full h-10 pl-3 pr-8 rounded-lg border border-border bg-white text-sm focus:ring-2 focus:ring-amber-500 outline-none transition-all duration-200">
                        <option value="">Select approver</option>
                        @foreach($users as $u)
                        <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                        @endforeach
                    </select>
                    @error("approvers.{$idx}.user_id") <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="w-28">
                    <label class="block text-xs font-medium text-secondary mb-1">Level <span class="text-error">*</span></label>
                    <input wire:model="approvers.{{ $idx }}.level" type="number" min="1" class="w-full h-10 px-3 rounded-lg border border-border bg-white text-sm text-center focus:ring-2 focus:ring-amber-500 outline-none transition-all duration-200">
                    @error("approvers.{$idx}.level") <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                </div>
                @if(count($approvers) > 1)
                <button type="button" wire:click="removeApprover({{ $idx }})" class="h-10 w-10 flex items-center justify-center rounded-lg border border-red-200 text-error hover:bg-red-50 transition-all duration-200 cursor-pointer shrink-0">
                    <i data-lucide="trash-2" class="size-4"></i>
                </button>
                @endif
            </div>
            @endforeach

            @error('approvers') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
            <p class="text-xs text-amber-600"><strong>Note:</strong> Approvers at the same level will be processed in parallel. Different levels are sequential (level 1 first, then level 2, etc.).</p>
        </div>
        @endif

        <!-- Resolve Immediately -->
        @can('access_requests.implement')
        @if(!$isEdit)
        <div class="bg-green-50 rounded-2xl border border-green-200 p-5">
            <label class="flex items-center gap-3 cursor-pointer">
                <input wire:model.live="resolve_immediately" type="checkbox" class="w-5 h-5 rounded border-green-300 text-green-600 focus:ring-green-500">
                <div>
                    <span class="text-sm font-semibold text-green-800">Resolve & Implement Immediately</span>
                    <p class="text-xs text-green-600 mt-0.5">Check this if access was granted on the spot</p>
                </div>
            </label>
            @if($resolve_immediately)
            <div class="mt-3">
                <textarea wire:model="resolution_notes" rows="2" placeholder="Implementation notes (optional)..."
                          class="w-full px-4 py-3 rounded-xl border border-green-200 bg-white text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none transition-all duration-200 resize-y"></textarea>
            </div>
            @endif
        </div>
        @endif
        @endcan
        @endif

        <div class="flex items-center gap-3">
            <a href="{{ route('access-requests.index') }}" class="h-11 px-6 rounded-xl border border-border text-secondary font-semibold text-sm hover:bg-muted transition-all duration-200 flex items-center cursor-pointer">Cancel</a>
            <button type="submit" class="h-11 px-6 bg-primary hover:bg-primary-hover text-white rounded-xl font-semibold text-sm shadow-md shadow-primary/20 transition-all duration-200 hover:scale-[1.02] active:scale-95 flex items-center gap-2 cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed disabled:hover:scale-100" wire:loading.attr="disabled" wire:target="save">
                <svg wire:loading wire:target="save" class="animate-spin size-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                <span wire:loading.remove wire:target="save">{{ $isEdit ? 'Update Request' : 'Submit Request' }}</span>
                <span wire:loading wire:target="save">{{ $isEdit ? 'Updating...' : 'Submitting...' }}</span>
            </button>
        </div>
    </form>
</div>
