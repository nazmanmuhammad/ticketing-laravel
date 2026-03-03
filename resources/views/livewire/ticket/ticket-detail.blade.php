@section('title', $ticket->ticket_number . ' - ' . $ticket->title)
@section('page-title', $ticket->ticket_number)
@section('page-description', $ticket->title)
@section('breadcrumbs')
<span class="text-secondary">Dashboard</span>
<span class="text-secondary">&middot;</span>
<a href="{{ route('tickets.index') }}" class="text-secondary hover:text-primary transition-colors">Tickets</a>
<span class="text-secondary">&middot;</span>
<span class="font-semibold text-foreground">{{ $ticket->ticket_number }}</span>
@endsection

<div x-data="{ activeTab: 'comments' }">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Ticket Info Card -->
            <div class="bg-white rounded-2xl border border-border p-6">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h2 class="text-lg font-bold text-foreground">{{ $ticket->title }}</h2>
                        <p class="text-xs text-secondary mt-1">Created by {{ $ticket->requester?->name }} &middot; {{ $ticket->created_at->diffForHumans() }}</p>
                    </div>
                    @if($canManage && !in_array($ticket->status, ['closed']))
                    <button @click="$dispatch('confirm-dialog', { title: 'Close Ticket', message: 'Are you sure you want to close this ticket?', confirmText: 'Close', onConfirm: () => $wire.closeTicket() })"
                            class="h-9 px-4 rounded-xl border border-error text-error text-sm font-semibold hover:bg-error-light transition-all duration-200 cursor-pointer active:scale-95 flex items-center gap-2">
                        <svg wire:loading wire:target="closeTicket" class="animate-spin size-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        <span wire:loading.remove wire:target="closeTicket">Close Ticket</span>
                        <span wire:loading wire:target="closeTicket">Closing...</span>
                    </button>
                    @endif
                </div>
                <div class="prose prose-sm max-w-none text-foreground/80">
                    {!! nl2br(e($ticket->description)) !!}
                </div>

                @if($ticket->attachments->isNotEmpty())
                <div class="mt-4 pt-4 border-t border-border">
                    <p class="text-xs font-semibold text-secondary uppercase mb-2">Attachments</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($ticket->attachments as $att)
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
            @if($ticket->approvals->isNotEmpty())
            <div class="bg-white rounded-2xl border border-border p-6">
                <h3 class="text-sm font-bold text-foreground uppercase mb-4">Approval Workflow</h3>
                <div class="space-y-3 mb-4">
                    @foreach($ticket->approvals->sortBy('level') as $approval)
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
                        <button wire:click="approveTicket" class="h-10 px-5 bg-green-600 hover:bg-green-700 text-white rounded-xl text-sm font-semibold transition-all duration-200 active:scale-95 cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed flex items-center gap-2" wire:loading.attr="disabled" wire:target="approveTicket">
                            <svg wire:loading wire:target="approveTicket" class="animate-spin size-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            <span wire:loading.remove wire:target="approveTicket">Approve</span>
                            <span wire:loading wire:target="approveTicket">Approving...</span>
                        </button>
                        <button wire:click="rejectTicket" class="h-10 px-5 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm font-semibold transition-all duration-200 active:scale-95 cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed flex items-center gap-2" wire:loading.attr="disabled" wire:target="rejectTicket">
                            <svg wire:loading wire:target="rejectTicket" class="animate-spin size-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            <span wire:loading.remove wire:target="rejectTicket">Reject</span>
                            <span wire:loading wire:target="rejectTicket">Rejecting...</span>
                        </button>
                        <button wire:click="requestTicketInfo" class="h-10 px-5 bg-yellow-500 hover:bg-yellow-600 text-white rounded-xl text-sm font-semibold transition-all duration-200 active:scale-95 cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed flex items-center gap-2" wire:loading.attr="disabled" wire:target="requestTicketInfo">
                            <svg wire:loading wire:target="requestTicketInfo" class="animate-spin size-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            <span wire:loading.remove wire:target="requestTicketInfo">Request Info</span>
                            <span wire:loading wire:target="requestTicketInfo">Requesting...</span>
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

            <!-- Tabs -->
            <div class="bg-white rounded-2xl border border-border overflow-hidden">
                <div class="flex border-b border-border">
                    <button @click="activeTab = 'comments'" :class="activeTab === 'comments' ? 'border-primary text-primary' : 'border-transparent text-secondary hover:text-foreground'"
                            class="px-5 py-3 text-sm font-semibold border-b-2 transition-all duration-200 cursor-pointer">
                        Comments ({{ $ticket->comments->count() }})
                    </button>
                    <button @click="activeTab = 'activity'" :class="activeTab === 'activity' ? 'border-primary text-primary' : 'border-transparent text-secondary hover:text-foreground'"
                            class="px-5 py-3 text-sm font-semibold border-b-2 transition-all duration-200 cursor-pointer">
                        Activity Log
                    </button>
                </div>

                <!-- Comments Tab -->
                <div x-show="activeTab === 'comments'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="p-6">
                    <div class="space-y-4 mb-6 max-h-[500px] overflow-y-auto" id="comments-list">
                        @forelse($ticket->comments as $comment)
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
                        @forelse($ticket->activities as $activity)
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
            <!-- Status & Priority -->
            <div class="bg-white rounded-2xl border border-border p-5 space-y-4" x-data="{ showEditMenu: false }">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-xs font-semibold text-secondary uppercase">Status & Priority</h3>
                    @if($canManage)
                    <div class="relative">
                        <button @click="showEditMenu = !showEditMenu" class="size-8 rounded-lg hover:bg-muted flex items-center justify-center transition-colors cursor-pointer">
                            <i data-lucide="more-vertical" class="size-4 text-secondary"></i>
                        </button>
                        <div x-show="showEditMenu" @click.away="showEditMenu = false"
                             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 top-full mt-1 w-48 bg-white rounded-xl shadow-lg border border-border py-1 z-20">
                            <div class="px-3 py-2 border-b border-border">
                                <p class="text-xs font-semibold text-secondary">Edit Status</p>
                                <select wire:model="newStatus" wire:change="updateStatus(); showEditMenu = false" class="w-full h-8 pl-2 pr-6 rounded-lg border border-border bg-white text-xs focus:ring-2 focus:ring-primary outline-none transition-all duration-200 mt-1">
                                    @foreach(['open', 'in_progress', 'pending', 'resolved', 'closed'] as $s)
                                    <option value="{{ $s }}">{{ str_replace('_', ' ', ucfirst($s)) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="px-3 py-2">
                                <p class="text-xs font-semibold text-secondary">Edit Priority</p>
                                <select wire:model="newPriority" wire:change="updatePriority(); showEditMenu = false" class="w-full h-8 pl-2 pr-6 rounded-lg border border-border bg-white text-xs focus:ring-2 focus:ring-primary outline-none transition-all duration-200 mt-1">
                                    @foreach(['low', 'medium', 'high', 'critical'] as $p)
                                    <option value="{{ $p }}">{{ ucfirst($p) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                <div>
                    <label class="block text-xs font-medium text-secondary mb-1.5">Status</label>
                    @php $statusColors = ['open'=>'bg-blue-50 text-blue-700','in_progress'=>'bg-yellow-50 text-yellow-700','pending'=>'bg-orange-50 text-orange-700','pending_approval'=>'bg-amber-50 text-amber-700','info_requested'=>'bg-yellow-50 text-yellow-700','rejected'=>'bg-red-50 text-red-700','resolved'=>'bg-green-50 text-green-700','closed'=>'bg-gray-100 text-gray-600']; @endphp
                    <span class="inline-block px-3 py-1.5 rounded-lg text-sm font-semibold {{ $statusColors[$ticket->status] ?? 'bg-muted text-secondary' }}">{{ str_replace('_', ' ', ucfirst($ticket->status)) }}</span>
                </div>
                <div>
                    <label class="block text-xs font-medium text-secondary mb-1.5">Priority</label>
                    @php $prioColors = ['low'=>'bg-green-50 text-green-700','medium'=>'bg-blue-50 text-blue-700','high'=>'bg-orange-50 text-orange-700','critical'=>'bg-red-50 text-red-700']; @endphp
                    <span class="inline-block px-3 py-1.5 rounded-lg text-sm font-semibold {{ $prioColors[$ticket->priority] ?? 'bg-muted text-secondary' }}">{{ ucfirst($ticket->priority) }}</span>
                </div>
            </div>

            <!-- Assignment -->
            <div class="bg-white rounded-2xl border border-border p-5">
                <div class="flex items-center justify-between mb-3">
                    <label class="text-xs font-semibold text-secondary uppercase">Assigned To</label>
                    @if($canManage)
                    <button wire:click="$set('showAssignModal', true)" class="text-xs font-semibold text-primary hover:underline cursor-pointer">Change</button>
                    @endif
                </div>
                <p class="text-sm font-medium text-foreground">{{ $ticket->assignee?->name ?? 'Unassigned' }}</p>
                @if($ticket->team)
                <p class="text-xs text-secondary mt-1">Team: {{ $ticket->team->name }}</p>
                @endif
                @if($canManage)
                <div class="mt-3 pt-3 border-t border-border">
                    <div class="flex items-center gap-2 text-xs">
                        <div class="size-2 rounded-full bg-green-500"></div>
                        <span class="font-medium text-green-700">You can work on this ticket</span>
                    </div>
                </div>
                @endif
            </div>

            <!-- SLA -->
            <div class="bg-white rounded-2xl border border-border p-5">
                <label class="block text-xs font-semibold text-secondary uppercase mb-2">SLA</label>
                @if($ticket->sla_due_at)
                    @php $slaService = app(\App\Services\SlaService::class); $slaStatus = $slaService->getSlaStatus($ticket); $slaColor = $slaService->getSlaColor($slaStatus); @endphp
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-1 rounded-lg text-xs font-semibold {{ $slaColor }}">{{ str_replace('_', ' ', ucfirst($slaStatus)) }}</span>
                        <span class="text-xs text-secondary">Due: {{ $ticket->sla_due_at->format('M d, H:i') }}</span>
                    </div>
                @else
                    <p class="text-sm text-secondary">No SLA set</p>
                @endif
            </div>

            <!-- Details -->
            <div class="bg-white rounded-2xl border border-border p-5 space-y-3">
                <label class="block text-xs font-semibold text-secondary uppercase">Details</label>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between"><span class="text-secondary">Category</span><span class="font-medium text-foreground">{{ $ticket->category?->name ?? '-' }}</span></div>
                    <div class="flex justify-between"><span class="text-secondary">Sub-category</span><span class="font-medium text-foreground">{{ $ticket->subCategory?->name ?? '-' }}</span></div>
                    <div class="flex justify-between"><span class="text-secondary">Created</span><span class="font-medium text-foreground">{{ $ticket->created_at->format('M d, Y H:i') }}</span></div>
                    @if($ticket->closed_at)
                    <div class="flex justify-between"><span class="text-secondary">Closed</span><span class="font-medium text-foreground">{{ $ticket->closed_at->format('M d, Y H:i') }}</span></div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Assign Modal -->
    @if($showAssignModal)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" x-data x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
        <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100">
            <div class="flex items-center justify-between p-5 border-b border-border">
                <h3 class="font-bold text-foreground">Assign Ticket</h3>
                <button wire:click="$set('showAssignModal', false)" class="size-8 rounded-lg hover:bg-muted flex items-center justify-center transition-colors cursor-pointer"><i data-lucide="x" class="size-4 text-secondary"></i></button>
            </div>
            <div class="p-5 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1.5">Agent</label>
                    <select wire:model="assignToUser" class="w-full h-10 pl-3 pr-8 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary outline-none transition-all duration-200">
                        <option value="">Select agent</option>
                        @foreach($agents as $agent)
                        <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1.5">Team</label>
                    <select wire:model="assignToTeam" class="w-full h-10 pl-3 pr-8 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary outline-none transition-all duration-200">
                        <option value="">Select team</option>
                        @foreach($teams as $team)
                        <option value="{{ $team->id }}">{{ $team->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="flex gap-3 p-5 border-t border-border">
                <button wire:click="$set('showAssignModal', false)" class="flex-1 h-10 rounded-xl border border-border text-secondary font-semibold text-sm hover:bg-muted transition-all duration-200 cursor-pointer">Cancel</button>
                <button wire:click="assignTicket" class="flex-1 h-10 rounded-xl bg-primary text-white font-semibold text-sm hover:bg-primary-hover transition-all duration-200 active:scale-95 cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed flex items-center justify-center gap-2" wire:loading.attr="disabled" wire:target="assignTicket">
                    <svg wire:loading wire:target="assignTicket" class="animate-spin size-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    <span wire:loading.remove wire:target="assignTicket">Assign</span>
                    <span wire:loading wire:target="assignTicket">Assigning...</span>
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
