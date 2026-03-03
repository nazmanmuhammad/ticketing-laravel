@section('title', $accessRequest->request_number)
@section('page-title', $accessRequest->request_number)
@section('page-description', 'Access Request Details')
@section('breadcrumbs')
<span class="text-secondary">Dashboard</span>
<span class="text-secondary">&middot;</span>
<a href="{{ route('access-requests.index') }}" class="text-secondary hover:text-primary transition-colors">Access Requests</a>
<span class="text-secondary">&middot;</span>
<span class="font-semibold text-foreground">{{ $accessRequest->request_number }}</span>
@endsection

<div x-data="{ activeTab: 'comments' }">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Request Info Card -->
            <div class="bg-white rounded-2xl border border-border p-6">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h2 class="text-lg font-bold text-foreground">{{ $accessRequest->system?->name }} — {{ ucfirst($accessRequest->access_type) }}</h2>
                        <p class="text-xs text-secondary mt-1">Created by {{ $accessRequest->requester?->name }} &middot; {{ $accessRequest->created_at->diffForHumans() }}</p>
                    </div>
                    @if($accessRequest->requester_id === auth()->id() && in_array($accessRequest->status, ['draft', 'submitted', 'pending_approval', 'info_requested']))
                    <a href="{{ route('access-requests.edit', $accessRequest) }}" wire:navigate
                       class="h-9 px-4 rounded-xl border border-border text-secondary text-sm font-semibold hover:bg-muted transition-all duration-200 cursor-pointer active:scale-95 flex items-center gap-2">
                        <i data-lucide="pencil" class="size-3.5"></i> Edit
                    </a>
                    @endif
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div><span class="text-secondary block mb-1">Access Type</span><span class="font-medium text-foreground">{{ ucfirst($accessRequest->access_type) }}{{ $accessRequest->custom_access_type ? ' - ' . $accessRequest->custom_access_type : '' }}</span></div>
                    <div><span class="text-secondary block mb-1">Period</span><span class="font-medium text-foreground">{{ $accessRequest->start_date->format('M d, Y') }} {{ $accessRequest->end_date ? '- ' . $accessRequest->end_date->format('M d, Y') : '(Permanent)' }}</span></div>
                </div>
                <div class="mt-4 pt-4 border-t border-border">
                    <span class="text-secondary text-sm block mb-1">Reason / Justification</span>
                    <div class="prose prose-sm max-w-none text-foreground/80">{!! nl2br(e($accessRequest->reason)) !!}</div>
                </div>
                @if($accessRequest->attachments->isNotEmpty())
                <div class="mt-4 pt-4 border-t border-border">
                    <p class="text-xs font-semibold text-secondary uppercase mb-2">Attachments</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($accessRequest->attachments as $att)
                        <a href="{{ asset('storage/' . $att->file_path) }}" target="_blank"
                           class="flex items-center gap-2 px-3 py-2 rounded-lg bg-muted text-sm font-medium text-foreground hover:bg-primary/10 hover:text-primary transition-colors duration-150">
                            <i data-lucide="paperclip" class="size-3.5"></i> {{ $att->file_name }}
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Approval Section -->
            @if($accessRequest->approvals->isNotEmpty())
            <div class="bg-white rounded-2xl border border-border p-6">
                <h3 class="text-sm font-bold text-foreground uppercase mb-4">Approval Workflow</h3>
                <div class="space-y-3 mb-4">
                    @foreach($accessRequest->approvals->sortBy('level') as $approval)
                    <div class="flex items-center justify-between p-3 rounded-xl border {{ $approval->status === 'approved' ? 'border-green-200 bg-green-50' : ($approval->status === 'rejected' ? 'border-red-200 bg-red-50' : ($approval->status === 'info_requested' ? 'border-yellow-200 bg-yellow-50' : 'border-border bg-muted/30')) }}">
                        <div class="flex items-center gap-3">
                            <div class="size-8 rounded-full {{ $approval->status === 'approved' ? 'bg-green-500' : ($approval->status === 'rejected' ? 'bg-red-500' : ($approval->status === 'info_requested' ? 'bg-yellow-500' : 'bg-gray-300')) }} flex items-center justify-center text-white text-xs font-bold">
                                L{{ $approval->level }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-foreground">{{ $approval->approver?->name ?? 'Unknown' }}</p>
                                <p class="text-xs text-secondary">Level {{ $approval->level }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            @php
                                $badgeColors = ['pending' => 'bg-gray-100 text-gray-600', 'approved' => 'bg-green-100 text-green-700', 'rejected' => 'bg-red-100 text-red-700', 'info_requested' => 'bg-yellow-100 text-yellow-700'];
                            @endphp
                            <span class="px-2.5 py-1 rounded-lg text-xs font-semibold {{ $badgeColors[$approval->status] ?? 'bg-gray-100 text-gray-600' }}">{{ str_replace('_', ' ', ucfirst($approval->status)) }}</span>
                            @if($approval->acted_at)
                            <p class="text-[10px] text-secondary mt-1">{{ $approval->acted_at->diffForHumans() }}</p>
                            @endif
                            @if($approval->notes)
                            <p class="text-xs text-secondary mt-1 italic max-w-[200px] truncate" title="{{ $approval->notes }}">{{ $approval->notes }}</p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>

                @if($canApprove)
                <div class="border-t border-border pt-4 space-y-3">
                    <textarea wire:model="approvalNotes" rows="2" placeholder="Approval notes (required for reject/request info)..."
                              class="w-full px-4 py-3 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all duration-200 resize-y"></textarea>
                    @error('approvalNotes') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                    <div class="flex flex-wrap gap-2">
                        <button @click="$dispatch('confirm-dialog', { title: 'Approve Request', message: 'Are you sure you want to approve this access request?', confirmText: 'Approve', onConfirm: () => $wire.approve() })" class="h-10 px-5 bg-green-600 hover:bg-green-700 text-white rounded-xl text-sm font-semibold transition-all duration-200 active:scale-95 cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed flex items-center gap-2" wire:loading.attr="disabled" wire:target="approve">
                            <svg wire:loading wire:target="approve" class="animate-spin size-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            <span wire:loading.remove wire:target="approve">Approve</span>
                            <span wire:loading wire:target="approve">Approving...</span>
                        </button>
                        <button @click="$dispatch('confirm-dialog', { title: 'Reject Request', message: 'Are you sure you want to reject this access request? Notes are required.', confirmText: 'Reject', onConfirm: () => $wire.reject() })" class="h-10 px-5 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm font-semibold transition-all duration-200 active:scale-95 cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed flex items-center gap-2" wire:loading.attr="disabled" wire:target="reject">
                            <svg wire:loading wire:target="reject" class="animate-spin size-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            <span wire:loading.remove wire:target="reject">Reject</span>
                            <span wire:loading wire:target="reject">Rejecting...</span>
                        </button>
                        <button @click="$dispatch('confirm-dialog', { title: 'Request Info', message: 'Are you sure you want to request additional information? Notes are required.', confirmText: 'Request Info', onConfirm: () => $wire.requestInfo() })" class="h-10 px-5 bg-yellow-500 hover:bg-yellow-600 text-white rounded-xl text-sm font-semibold transition-all duration-200 active:scale-95 cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed flex items-center gap-2" wire:loading.attr="disabled" wire:target="requestInfo">
                            <svg wire:loading wire:target="requestInfo" class="animate-spin size-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            <span wire:loading.remove wire:target="requestInfo">Request Info</span>
                            <span wire:loading wire:target="requestInfo">Requesting...</span>
                        </button>
                    </div>
                </div>
                @endif

                @if($needsInfoResponse && $infoRequestDetail)
                <div class="border-t border-border pt-4">
                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-4">
                        <div class="flex items-start gap-3">
                            <div class="size-8 rounded-full bg-yellow-100 flex items-center justify-center shrink-0 mt-0.5">
                                <i data-lucide="message-circle-question" class="size-4 text-yellow-600"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-yellow-800">Additional Information Requested</p>
                                <p class="text-xs text-yellow-700 mt-0.5">By <strong>{{ $infoRequestDetail->approver?->name }}</strong> &middot; {{ $infoRequestDetail->acted_at?->diffForHumans() }}</p>
                                @if($infoRequestDetail->notes)
                                <div class="mt-2 p-3 bg-white rounded-lg border border-yellow-100 text-sm text-foreground/80">
                                    {!! nl2br(e($infoRequestDetail->notes)) !!}
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <label class="block text-sm font-semibold text-foreground">Your Response</label>
                        <textarea wire:model="infoResponse" rows="3" placeholder="Provide the requested information..."
                                  class="w-full px-4 py-3 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all duration-200 resize-y"></textarea>
                        @error('infoResponse') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                        <button wire:click="respondToInfoRequest" class="h-10 px-5 bg-primary hover:bg-primary-hover text-white rounded-xl text-sm font-semibold transition-all duration-200 active:scale-95 cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed flex items-center gap-2" wire:loading.attr="disabled" wire:target="respondToInfoRequest">
                            <svg wire:loading wire:target="respondToInfoRequest" class="animate-spin size-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            <span wire:loading.remove wire:target="respondToInfoRequest">Submit Response</span>
                            <span wire:loading wire:target="respondToInfoRequest">Submitting...</span>
                        </button>
                    </div>
                </div>
                @endif
            </div>
            @endif

            <!-- Implement Action -->
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

            <!-- Tabs: Comments & Activity -->
            <div class="bg-white rounded-2xl border border-border overflow-hidden">
                <div class="flex border-b border-border">
                    <button @click="activeTab = 'comments'" :class="activeTab === 'comments' ? 'border-primary text-primary' : 'border-transparent text-secondary hover:text-foreground'"
                            class="px-5 py-3 text-sm font-semibold border-b-2 transition-all duration-200 cursor-pointer">
                        Comments ({{ $accessRequest->comments->count() }})
                    </button>
                    <button @click="activeTab = 'activity'" :class="activeTab === 'activity' ? 'border-primary text-primary' : 'border-transparent text-secondary hover:text-foreground'"
                            class="px-5 py-3 text-sm font-semibold border-b-2 transition-all duration-200 cursor-pointer">
                        Activity Log
                    </button>
                </div>

                <!-- Comments Tab -->
                <div x-show="activeTab === 'comments'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="p-6">
                    <div class="space-y-4 mb-6 max-h-[500px] overflow-y-auto" id="comments-list">
                        @forelse($accessRequest->comments as $comment)
                        <div id="comment-{{ $comment->id }}" class="group flex gap-3 {{ $comment->is_internal ? 'bg-yellow-50/50 -mx-2 px-2 py-2 rounded-xl border border-yellow-100' : '' }}">
                            <div class="size-8 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-xs shrink-0 mt-0.5">
                                {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-sm font-semibold text-foreground">{{ $comment->user->name }}</span>
                                    @if($comment->is_internal)
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-yellow-100 text-yellow-700">INTERNAL</span>
                                    @endif
                                    <span class="text-xs text-secondary">{{ $comment->created_at->diffForHumans() }}</span>
                                    <button wire:click="setReplyTo({{ $comment->id }})" class="opacity-0 group-hover:opacity-100 transition-opacity text-xs text-primary hover:underline cursor-pointer ml-auto" title="Reply">
                                        <i data-lucide="reply" class="size-3.5 inline"></i> Reply
                                    </button>
                                </div>

                                @if($comment->parent)
                                <div class="mb-2 pl-3 border-l-2 border-primary/30 rounded">
                                    <p class="text-xs text-secondary"><strong>{{ $comment->parent->user->name }}</strong></p>
                                    <p class="text-xs text-secondary/70 truncate">{{ Str::limit($comment->parent->body, 120) }}</p>
                                </div>
                                @endif

                                <div class="text-sm text-foreground/80">{!! nl2br(e($comment->body)) !!}</div>

                                @if($comment->attachments && $comment->attachments->isNotEmpty())
                                <div class="mt-2 flex flex-wrap gap-2">
                                    @foreach($comment->attachments as $att)
                                    @php
                                        $isImage = in_array(strtolower(pathinfo($att->file_name, PATHINFO_EXTENSION)), ['jpg','jpeg','png','gif','webp','svg']);
                                    @endphp
                                    @if($isImage)
                                    <a href="{{ asset('storage/' . $att->file_path) }}" target="_blank" class="block">
                                        <img src="{{ asset('storage/' . $att->file_path) }}" alt="{{ $att->file_name }}" class="max-h-32 rounded-lg border border-border hover:shadow-md transition-shadow">
                                    </a>
                                    @else
                                    <a href="{{ asset('storage/' . $att->file_path) }}" target="_blank"
                                       class="flex items-center gap-2 px-3 py-2 rounded-lg bg-muted text-xs font-medium text-foreground hover:bg-primary/10 hover:text-primary transition-colors duration-150">
                                        <i data-lucide="paperclip" class="size-3"></i> {{ $att->file_name }}
                                        @if($att->file_size)
                                        <span class="text-secondary">({{ number_format($att->file_size / 1024, 0) }}KB)</span>
                                        @endif
                                    </a>
                                    @endif
                                    @endforeach
                                </div>
                                @endif
                            </div>
                        </div>
                        @empty
                        <p class="text-sm text-secondary text-center py-6">No comments yet</p>
                        @endforelse
                    </div>

                    <!-- Add Comment Form -->
                    <div class="border-t border-border pt-4">
                        @if($replyToId)
                        <div class="flex items-center gap-2 mb-3 px-3 py-2 bg-primary/5 border border-primary/20 rounded-xl">
                            <i data-lucide="reply" class="size-3.5 text-primary shrink-0"></i>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs text-primary font-semibold">Replying to {{ $replyToUser }}</p>
                                <p class="text-xs text-secondary truncate">{{ $replyToBody }}</p>
                            </div>
                            <button wire:click="cancelReply" class="size-6 rounded-lg hover:bg-muted flex items-center justify-center transition-colors cursor-pointer shrink-0">
                                <i data-lucide="x" class="size-3.5 text-secondary"></i>
                            </button>
                        </div>
                        @endif

                        <div class="mb-3" x-data="{ showCanned: false }">
                            <div class="flex items-center gap-3 mb-2">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input wire:model="isInternal" type="checkbox" class="w-4 h-4 rounded border-border text-yellow-500 focus:ring-yellow-400">
                                    <span class="text-xs font-medium text-secondary">Internal Note</span>
                                </label>
                                <div class="relative">
                                    <button @click="showCanned = !showCanned" type="button" class="text-xs font-medium text-primary hover:underline cursor-pointer">Canned Response</button>
                                    <div x-show="showCanned" @click.away="showCanned = false"
                                         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                                         class="absolute left-0 top-full mt-1 w-64 bg-white rounded-xl shadow-lg border border-border py-1 z-20">
                                        @foreach($cannedResponses as $cr)
                                        <button wire:click="applyCannedResponse({{ $cr->id }})" @click="showCanned = false" type="button"
                                                class="w-full text-left px-3 py-2 text-sm hover:bg-muted transition-colors duration-150 cursor-pointer">
                                            {{ $cr->title }}
                                        </button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <textarea wire:model="commentBody" rows="3" placeholder="{{ $replyToId ? 'Write your reply...' : 'Write a comment...' }}"
                                      class="w-full px-4 py-3 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all duration-200 resize-y"></textarea>
                            @error('commentBody') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- File Attachments -->
                        <div class="mb-3">
                            <label class="flex items-center gap-2 cursor-pointer text-xs font-medium text-secondary hover:text-primary transition-colors">
                                <i data-lucide="paperclip" class="size-3.5"></i>
                                <span>Attach files</span>
                                <input type="file" wire:model="commentAttachments" multiple class="hidden" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip,.rar">
                            </label>
                            <div wire:loading wire:target="commentAttachments" class="mt-2 text-xs text-secondary flex items-center gap-2">
                                <svg class="animate-spin size-3" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                Uploading...
                            </div>
                            @if(!empty($commentAttachments))
                            <div class="mt-2 flex flex-wrap gap-2">
                                @foreach($commentAttachments as $index => $file)
                                <div class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg bg-muted text-xs">
                                    <i data-lucide="file" class="size-3 text-secondary"></i>
                                    <span class="text-foreground font-medium max-w-[150px] truncate">{{ $file->getClientOriginalName() }}</span>
                                    <button wire:click="removeAttachment({{ $index }})" class="size-4 rounded hover:bg-red-100 flex items-center justify-center cursor-pointer">
                                        <i data-lucide="x" class="size-3 text-red-500"></i>
                                    </button>
                                </div>
                                @endforeach
                            </div>
                            @endif
                            @error('commentAttachments.*') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                        </div>

                        <button wire:click="addComment" class="h-9 px-5 bg-primary hover:bg-primary-hover text-white rounded-xl text-sm font-semibold transition-all duration-200 hover:scale-[1.02] active:scale-95 cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed flex items-center gap-2" wire:loading.attr="disabled" wire:target="addComment">
                            <svg wire:loading wire:target="addComment" class="animate-spin size-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            <span wire:loading.remove wire:target="addComment">{{ $replyToId ? 'Post Reply' : 'Post Comment' }}</span>
                            <span wire:loading wire:target="addComment">Posting...</span>
                        </button>
                    </div>
                </div>

                <!-- Activity Tab -->
                <div x-show="activeTab === 'activity'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="p-6">
                    <div class="space-y-4 max-h-[500px] overflow-y-auto">
                        @forelse($accessRequest->activities as $activity)
                        <div class="flex gap-3">
                            <div class="flex flex-col items-center">
                                <div class="size-7 rounded-full bg-muted flex items-center justify-center shrink-0">
                                    <i data-lucide="activity" class="size-3.5 text-secondary"></i>
                                </div>
                                @if(!$loop->last)<div class="w-px flex-1 bg-border mt-1"></div>@endif
                            </div>
                            <div class="pb-4">
                                <p class="text-sm text-foreground">{{ $activity->description }}</p>
                                <p class="text-xs text-secondary">{{ $activity->user->name ?? 'System' }} &middot; {{ $activity->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        @empty
                        <p class="text-sm text-secondary text-center py-6">No activity recorded</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-4">
            <div class="bg-white rounded-2xl border border-border p-5">
                <label class="block text-xs font-semibold text-secondary uppercase mb-3">Status</label>
                @php
                    $sColors = ['draft'=>'bg-gray-100 text-gray-600','submitted'=>'bg-blue-50 text-blue-700','pending_approval'=>'bg-amber-50 text-amber-700','info_requested'=>'bg-orange-50 text-orange-700','approved'=>'bg-green-50 text-green-700','rejected'=>'bg-red-50 text-red-700','implemented'=>'bg-purple-50 text-purple-700'];
                @endphp
                <span class="px-3 py-1.5 rounded-lg text-sm font-semibold {{ $sColors[$accessRequest->status] ?? '' }}">{{ str_replace('_', ' ', ucfirst($accessRequest->status)) }}</span>
            </div>
            <div class="bg-white rounded-2xl border border-border p-5 space-y-3">
                <label class="block text-xs font-semibold text-secondary uppercase">Details</label>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between"><span class="text-secondary">Request #</span><span class="font-mono font-semibold text-primary">{{ $accessRequest->request_number }}</span></div>
                    <div class="flex justify-between"><span class="text-secondary">System</span><span class="font-medium">{{ $accessRequest->system?->name }}</span></div>
                    <div class="flex justify-between"><span class="text-secondary">Access Type</span><span class="font-medium">{{ ucfirst($accessRequest->access_type) }}</span></div>
                    @if($accessRequest->assignee)
                    <div class="flex justify-between"><span class="text-secondary">Assigned To</span><span class="font-medium">{{ $accessRequest->assignee->name }}</span></div>
                    @endif
                    @if($accessRequest->team)
                    <div class="flex justify-between"><span class="text-secondary">Team</span><span class="font-medium">{{ $accessRequest->team->name }}</span></div>
                    @endif
                    <div class="flex justify-between"><span class="text-secondary">Created</span><span class="font-medium">{{ $accessRequest->created_at->format('M d, Y H:i') }}</span></div>
                </div>
            </div>
        </div>
    </div>
</div>
