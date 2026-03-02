@section('title', $changeRequest->request_number)
@section('page-title', $changeRequest->request_number)
@section('page-description', $changeRequest->title)

<div x-data="{ activeTab: 'details' }">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <!-- Tabs -->
            <div class="bg-white rounded-2xl border border-border overflow-hidden">
                <div class="flex border-b border-border">
                    <button @click="activeTab = 'details'" :class="activeTab === 'details' ? 'border-primary text-primary' : 'border-transparent text-secondary hover:text-foreground'" class="px-5 py-3 text-sm font-semibold border-b-2 transition-all duration-200 cursor-pointer">Details</button>
                    <button @click="activeTab = 'approvals'" :class="activeTab === 'approvals' ? 'border-primary text-primary' : 'border-transparent text-secondary hover:text-foreground'" class="px-5 py-3 text-sm font-semibold border-b-2 transition-all duration-200 cursor-pointer">Approvals</button>
                    <button @click="activeTab = 'activity'" :class="activeTab === 'activity' ? 'border-primary text-primary' : 'border-transparent text-secondary hover:text-foreground'" class="px-5 py-3 text-sm font-semibold border-b-2 transition-all duration-200 cursor-pointer">Activity</button>
                </div>

                <!-- Details Tab -->
                <div x-show="activeTab === 'details'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="p-6 space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div><span class="text-secondary block mb-1">Requester</span><span class="font-medium text-foreground">{{ $changeRequest->requester?->name }}</span></div>
                        <div><span class="text-secondary block mb-1">System</span><span class="font-medium text-foreground">{{ $changeRequest->system?->name }}</span></div>
                        <div><span class="text-secondary block mb-1">Type</span>
                            @php $tColors = ['standard'=>'bg-green-50 text-green-700','normal'=>'bg-blue-50 text-blue-700','emergency'=>'bg-red-50 text-red-700']; @endphp
                            <span class="px-2 py-1 rounded-lg text-xs font-semibold {{ $tColors[$changeRequest->change_type] ?? '' }}">{{ ucfirst($changeRequest->change_type) }}</span>
                        </div>
                        <div><span class="text-secondary block mb-1">Related Ticket</span><span class="font-medium text-foreground">{{ $changeRequest->relatedTicket?->ticket_number ?? 'None' }}</span></div>
                    </div>
                    <div class="pt-4 border-t border-border">
                        <span class="text-secondary text-sm block mb-1">Description</span>
                        <div class="text-sm text-foreground/80">{!! nl2br(e($changeRequest->description)) !!}</div>
                    </div>
                    @if($changeRequest->impact)
                    <div class="pt-4 border-t border-border">
                        <span class="text-secondary text-sm block mb-1">Impact Assessment</span>
                        <div class="text-sm text-foreground/80">{!! nl2br(e($changeRequest->impact)) !!}</div>
                    </div>
                    @endif
                    @if($changeRequest->risk)
                    <div class="pt-4 border-t border-border">
                        <span class="text-secondary text-sm block mb-1">Risk Assessment</span>
                        <div class="text-sm text-foreground/80">{!! nl2br(e($changeRequest->risk)) !!}</div>
                    </div>
                    @endif
                    @if($changeRequest->rollback_plan)
                    <div class="pt-4 border-t border-border">
                        <span class="text-secondary text-sm block mb-1">Rollback Plan</span>
                        <div class="text-sm text-foreground/80">{!! nl2br(e($changeRequest->rollback_plan)) !!}</div>
                    </div>
                    @endif
                    @if($changeRequest->attachments->isNotEmpty())
                    <div class="pt-4 border-t border-border">
                        <span class="text-xs font-semibold text-secondary uppercase mb-2 block">Attachments</span>
                        <div class="flex flex-wrap gap-2">
                            @foreach($changeRequest->attachments as $att)
                            <a href="{{ asset('storage/' . $att->file_path) }}" target="_blank" class="flex items-center gap-2 px-3 py-2 rounded-lg bg-muted text-sm font-medium hover:bg-primary/10 hover:text-primary transition-colors duration-150"><i data-lucide="paperclip" class="size-3.5"></i> {{ $att->file_name }}</a>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Approvals Tab -->
                <div x-show="activeTab === 'approvals'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="p-6">
                    <div class="space-y-4">
                        @forelse($changeRequest->approvals as $approval)
                        <div class="flex gap-3">
                            <div class="flex flex-col items-center">
                                @php $iconColor = match($approval->status) { 'approved' => 'bg-green-100 text-green-600', 'rejected' => 'bg-red-100 text-red-600', 'rescheduled' => 'bg-yellow-100 text-yellow-600', default => 'bg-muted text-secondary' }; @endphp
                                <div class="size-8 rounded-full {{ $iconColor }} flex items-center justify-center shrink-0">
                                    @if($approval->status === 'approved')<i data-lucide="check" class="size-4"></i>@elseif($approval->status === 'rejected')<i data-lucide="x" class="size-4"></i>@else<i data-lucide="clock" class="size-4"></i>@endif
                                </div>
                                @if(!$loop->last)<div class="w-px flex-1 bg-border mt-1"></div>@endif
                            </div>
                            <div class="pb-4">
                                <p class="text-sm font-medium text-foreground">Level {{ $approval->level }} — {{ $approval->approver?->name ?? 'TBD' }}</p>
                                <p class="text-xs text-secondary">{{ str_replace('_', ' ', ucfirst($approval->status)) }} {{ $approval->acted_at ? '· ' . $approval->acted_at->diffForHumans() : '' }}</p>
                                @if($approval->notes)<p class="text-sm text-foreground/80 mt-1 bg-muted rounded-lg p-2">{{ $approval->notes }}</p>@endif
                            </div>
                        </div>
                        @empty
                        <p class="text-sm text-secondary text-center py-6">No approval workflow (standard change)</p>
                        @endforelse
                    </div>

                    @if($canApprove)
                    <div class="mt-6 pt-4 border-t border-border">
                        <h4 class="font-semibold text-foreground mb-3">Your Action Required</h4>
                        <textarea wire:model="approvalNotes" rows="3" placeholder="Notes (required for rejection)..."
                                  class="w-full px-4 py-3 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all duration-200 resize-y mb-3"></textarea>
                        @error('approvalNotes') <p class="text-xs text-error mb-2">{{ $message }}</p> @enderror
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
                </div>

                <!-- Activity Tab -->
                <div x-show="activeTab === 'activity'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="p-6">
                    <div class="space-y-4 max-h-[500px] overflow-y-auto">
                        @forelse($changeRequest->activities as $activity)
                        <div class="flex gap-3">
                            <div class="flex flex-col items-center">
                                <div class="size-7 rounded-full bg-muted flex items-center justify-center shrink-0"><i data-lucide="activity" class="size-3.5 text-secondary"></i></div>
                                @if(!$loop->last)<div class="w-px flex-1 bg-border mt-1"></div>@endif
                            </div>
                            <div class="pb-4">
                                <p class="text-sm text-foreground">{{ $activity->description }}</p>
                                <p class="text-xs text-secondary">{{ $activity->user->name ?? 'System' }} · {{ $activity->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        @empty
                        <p class="text-sm text-secondary text-center py-6">No activity recorded</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Action Panels -->
            @if($changeRequest->status === 'approved')
            @can('change_requests.schedule')
            <div class="bg-white rounded-2xl border border-border p-6">
                <h3 class="font-bold text-foreground mb-3">Schedule Implementation</h3>
                <div class="flex items-end gap-3">
                    <div class="flex-1">
                        <input wire:model="scheduledAt" type="datetime-local" class="w-full h-10 px-4 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary outline-none transition-all duration-200">
                        @error('scheduledAt') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                    </div>
                    <button wire:click="schedule" class="h-10 px-5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-semibold transition-all duration-200 active:scale-95 cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed flex items-center gap-2" wire:loading.attr="disabled" wire:target="schedule">
                        <svg wire:loading wire:target="schedule" class="animate-spin size-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        <span wire:loading.remove wire:target="schedule">Schedule</span>
                        <span wire:loading wire:target="schedule">Scheduling...</span>
                    </button>
                </div>
            </div>
            @endcan
            @endif

            @if($changeRequest->status === 'scheduled')
            @can('change_requests.implement')
            <div class="bg-white rounded-2xl border border-border p-6 space-y-4">
                <h3 class="font-bold text-foreground">Implementation</h3>
                <div class="flex gap-3">
                    <button @click="$dispatch('confirm-dialog', { title: 'Mark Implemented', message: 'Confirm this change has been implemented successfully?', confirmText: 'Confirm', onConfirm: () => $wire.markImplemented() })" class="h-10 px-5 bg-green-600 hover:bg-green-700 text-white rounded-xl text-sm font-semibold transition-all duration-200 active:scale-95 cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed flex items-center gap-2" wire:loading.attr="disabled" wire:target="markImplemented">
                        <svg wire:loading wire:target="markImplemented" class="animate-spin size-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        <span wire:loading.remove wire:target="markImplemented">Mark Implemented</span>
                        <span wire:loading wire:target="markImplemented">Processing...</span>
                    </button>
                    <div x-data="{ showFail: false }">
                        <button @click="showFail = !showFail" class="h-10 px-5 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm font-semibold transition-all duration-200 active:scale-95 cursor-pointer">Mark Failed</button>
                        <div x-show="showFail" x-transition class="mt-3">
                            <textarea wire:model="failReason" rows="2" placeholder="Reason for failure..." class="w-full px-4 py-3 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary outline-none transition-all duration-200 resize-y mb-2"></textarea>
                            @error('failReason') <p class="text-xs text-error mb-2">{{ $message }}</p> @enderror
                            <button wire:click="markFailed" class="h-9 px-4 bg-red-600 text-white rounded-xl text-sm font-semibold cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed flex items-center gap-2" wire:loading.attr="disabled" wire:target="markFailed">
                                <svg wire:loading wire:target="markFailed" class="animate-spin size-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                <span wire:loading.remove wire:target="markFailed">Confirm Failed</span>
                                <span wire:loading wire:target="markFailed">Processing...</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endcan
            @endif

            @if($changeRequest->status === 'implemented')
            <div class="bg-white rounded-2xl border border-border p-6">
                <h3 class="font-bold text-foreground mb-3">Post-Implementation Review</h3>
                <textarea wire:model="postReviewNotes" rows="3" placeholder="Post review notes..." class="w-full px-4 py-3 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary outline-none transition-all duration-200 resize-y mb-3"></textarea>
                <button @click="$dispatch('confirm-dialog', { title: 'Close Change Request', message: 'Close this change request after post-implementation review?', confirmText: 'Close', onConfirm: () => $wire.close() })" class="h-10 px-5 bg-gray-700 hover:bg-gray-800 text-white rounded-xl text-sm font-semibold transition-all duration-200 active:scale-95 cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed flex items-center gap-2" wire:loading.attr="disabled" wire:target="close">
                    <svg wire:loading wire:target="close" class="animate-spin size-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    <span wire:loading.remove wire:target="close">Close Change Request</span>
                    <span wire:loading wire:target="close">Closing...</span>
                </button>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-4">
            <div class="bg-white rounded-2xl border border-border p-5">
                <label class="block text-xs font-semibold text-secondary uppercase mb-3">Status</label>
                @php $sColors = ['draft'=>'bg-gray-100 text-gray-600','submitted'=>'bg-blue-50 text-blue-700','under_review'=>'bg-yellow-50 text-yellow-700','approved'=>'bg-green-50 text-green-700','scheduled'=>'bg-indigo-50 text-indigo-700','implemented'=>'bg-purple-50 text-purple-700','closed'=>'bg-gray-100 text-gray-600','failed'=>'bg-red-50 text-red-700']; @endphp
                <span class="px-3 py-1.5 rounded-lg text-sm font-semibold {{ $sColors[$changeRequest->status] ?? '' }}">{{ str_replace('_', ' ', ucfirst($changeRequest->status)) }}</span>
            </div>
            <div class="bg-white rounded-2xl border border-border p-5 space-y-3">
                <label class="block text-xs font-semibold text-secondary uppercase">Details</label>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between"><span class="text-secondary">Request #</span><span class="font-mono font-semibold text-primary">{{ $changeRequest->request_number }}</span></div>
                    <div class="flex justify-between"><span class="text-secondary">Type</span><span class="font-medium">{{ ucfirst($changeRequest->change_type) }}</span></div>
                    @if($changeRequest->scheduled_at)
                    <div class="flex justify-between"><span class="text-secondary">Scheduled</span><span class="font-medium">{{ $changeRequest->scheduled_at->format('M d, H:i') }}</span></div>
                    @endif
                    @if($changeRequest->implemented_at)
                    <div class="flex justify-between"><span class="text-secondary">Implemented</span><span class="font-medium">{{ $changeRequest->implemented_at->format('M d, H:i') }}</span></div>
                    @endif
                    <div class="flex justify-between"><span class="text-secondary">Created</span><span class="font-medium">{{ $changeRequest->created_at->format('M d, Y') }}</span></div>
                </div>
            </div>
        </div>
    </div>
</div>
