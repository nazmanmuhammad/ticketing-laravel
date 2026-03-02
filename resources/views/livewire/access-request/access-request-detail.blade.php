@section('title', $accessRequest->request_number)
@section('page-title', $accessRequest->request_number)
@section('page-description', 'Access Request Details')

<div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-border p-6">
                <h3 class="font-bold text-foreground mb-4">Request Information</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div><span class="text-secondary block mb-1">Requester</span><span class="font-medium text-foreground">{{ $accessRequest->requester?->name }}</span></div>
                    <div><span class="text-secondary block mb-1">System</span><span class="font-medium text-foreground">{{ $accessRequest->system?->name }}</span></div>
                    <div><span class="text-secondary block mb-1">Access Type</span><span class="font-medium text-foreground">{{ ucfirst($accessRequest->access_type) }}{{ $accessRequest->custom_access_type ? ' - ' . $accessRequest->custom_access_type : '' }}</span></div>
                    <div><span class="text-secondary block mb-1">Period</span><span class="font-medium text-foreground">{{ $accessRequest->start_date->format('M d, Y') }} {{ $accessRequest->end_date ? '- ' . $accessRequest->end_date->format('M d, Y') : '(Permanent)' }}</span></div>
                </div>
                <div class="mt-4 pt-4 border-t border-border">
                    <span class="text-secondary text-sm block mb-1">Reason / Justification</span>
                    <div class="text-sm text-foreground/80">{!! nl2br(e($accessRequest->reason)) !!}</div>
                </div>
                @if($accessRequest->attachments->isNotEmpty())
                <div class="mt-4 pt-4 border-t border-border">
                    <span class="text-xs font-semibold text-secondary uppercase mb-2 block">Attachments</span>
                    <div class="flex flex-wrap gap-2">
                        @foreach($accessRequest->attachments as $att)
                        <a href="{{ asset('storage/' . $att->file_path) }}" target="_blank" class="flex items-center gap-2 px-3 py-2 rounded-lg bg-muted text-sm font-medium hover:bg-primary/10 hover:text-primary transition-colors duration-150">
                            <i data-lucide="paperclip" class="size-3.5"></i> {{ $att->file_name }}
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Approval Timeline -->
            <div class="bg-white rounded-2xl border border-border p-6">
                <h3 class="font-bold text-foreground mb-4">Approval Workflow</h3>
                <div class="space-y-4">
                    @foreach($accessRequest->approvals as $approval)
                    <div class="flex gap-3">
                        <div class="flex flex-col items-center">
                            @php
                                $iconColor = match($approval->status) { 'approved' => 'bg-green-100 text-green-600', 'rejected' => 'bg-red-100 text-red-600', 'info_requested' => 'bg-yellow-100 text-yellow-600', default => 'bg-muted text-secondary' };
                            @endphp
                            <div class="size-8 rounded-full {{ $iconColor }} flex items-center justify-center shrink-0">
                                @if($approval->status === 'approved')<i data-lucide="check" class="size-4"></i>
                                @elseif($approval->status === 'rejected')<i data-lucide="x" class="size-4"></i>
                                @else<i data-lucide="clock" class="size-4"></i>@endif
                            </div>
                            @if(!$loop->last)<div class="w-px flex-1 bg-border mt-1"></div>@endif
                        </div>
                        <div class="pb-4">
                            <p class="text-sm font-medium text-foreground">Level {{ $approval->level }} — {{ $approval->approver?->name ?? 'TBD' }}</p>
                            <p class="text-xs text-secondary">{{ str_replace('_', ' ', ucfirst($approval->status)) }} {{ $approval->acted_at ? '· ' . $approval->acted_at->diffForHumans() : '' }}</p>
                            @if($approval->notes)<p class="text-sm text-foreground/80 mt-1 bg-muted rounded-lg p-2">{{ $approval->notes }}</p>@endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Approval Actions -->
            @if($canApprove)
            <div class="bg-white rounded-2xl border border-border p-6">
                <h3 class="font-bold text-foreground mb-4">Your Action Required</h3>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-foreground mb-1.5">Notes</label>
                    <textarea wire:model="approvalNotes" rows="3" placeholder="Add notes (required for reject/info request)..."
                              class="w-full px-4 py-3 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all duration-200 resize-y"></textarea>
                    @error('approvalNotes') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="flex flex-wrap gap-3">
                    <button wire:click="approve" class="h-10 px-5 bg-green-600 hover:bg-green-700 text-white rounded-xl text-sm font-semibold transition-all duration-200 active:scale-95 cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed flex items-center gap-2" wire:loading.attr="disabled" wire:target="approve">
                        <svg wire:loading wire:target="approve" class="animate-spin size-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        <span wire:loading.remove wire:target="approve">Approve</span>
                        <span wire:loading wire:target="approve">Approving...</span>
                    </button>
                    <button wire:click="reject" class="h-10 px-5 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm font-semibold transition-all duration-200 active:scale-95 cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed flex items-center gap-2" wire:loading.attr="disabled" wire:target="reject">
                        <svg wire:loading wire:target="reject" class="animate-spin size-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        <span wire:loading.remove wire:target="reject">Reject</span>
                        <span wire:loading wire:target="reject">Rejecting...</span>
                    </button>
                    <button wire:click="requestInfo" class="h-10 px-5 bg-yellow-500 hover:bg-yellow-600 text-white rounded-xl text-sm font-semibold transition-all duration-200 active:scale-95 cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed flex items-center gap-2" wire:loading.attr="disabled" wire:target="requestInfo">
                        <svg wire:loading wire:target="requestInfo" class="animate-spin size-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        <span wire:loading.remove wire:target="requestInfo">Request Info</span>
                        <span wire:loading wire:target="requestInfo">Requesting...</span>
                    </button>
                </div>
            </div>
            @endif

            @if($accessRequest->status === 'approved')
            @can('access_requests.implement')
            <div class="bg-white rounded-2xl border border-border p-6">
                <button @click="$dispatch('confirm-dialog', { title: 'Mark Implemented', message: 'Mark this access request as implemented?', confirmText: 'Confirm', onConfirm: () => $wire.markImplemented() })" class="h-10 px-5 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-sm font-semibold transition-all duration-200 active:scale-95 cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed flex items-center gap-2" wire:loading.attr="disabled" wire:target="markImplemented">
                    <svg wire:loading wire:target="markImplemented" class="animate-spin size-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    <span wire:loading.remove wire:target="markImplemented">Mark as Implemented</span>
                    <span wire:loading wire:target="markImplemented">Processing...</span>
                </button>
            </div>
            @endcan
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-4">
            <div class="bg-white rounded-2xl border border-border p-5">
                <label class="block text-xs font-semibold text-secondary uppercase mb-3">Status</label>
                @php
                    $sColors = ['draft'=>'bg-gray-100 text-gray-600','submitted'=>'bg-blue-50 text-blue-700','pending_approval'=>'bg-yellow-50 text-yellow-700','approved'=>'bg-green-50 text-green-700','rejected'=>'bg-red-50 text-red-700','implemented'=>'bg-purple-50 text-purple-700'];
                @endphp
                <span class="px-3 py-1.5 rounded-lg text-sm font-semibold {{ $sColors[$accessRequest->status] ?? '' }}">{{ str_replace('_', ' ', ucfirst($accessRequest->status)) }}</span>
            </div>
            <div class="bg-white rounded-2xl border border-border p-5 space-y-3">
                <label class="block text-xs font-semibold text-secondary uppercase">Details</label>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between"><span class="text-secondary">Request #</span><span class="font-mono font-semibold text-primary">{{ $accessRequest->request_number }}</span></div>
                    <div class="flex justify-between"><span class="text-secondary">Approval Level</span><span class="font-medium">{{ $accessRequest->current_approval_level }}</span></div>
                    <div class="flex justify-between"><span class="text-secondary">Created</span><span class="font-medium">{{ $accessRequest->created_at->format('M d, Y') }}</span></div>
                </div>
            </div>
        </div>
    </div>
</div>
