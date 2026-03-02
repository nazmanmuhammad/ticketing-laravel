@section('title', $ticket->ticket_number)
@section('page-title', $ticket->ticket_number)
@section('page-description', $ticket->title)

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
                    @can('tickets.close')
                    @if(!in_array($ticket->status, ['closed']))
                    <button @click="$dispatch('confirm-dialog', { title: 'Close Ticket', message: 'Are you sure you want to close this ticket?', confirmText: 'Close', onConfirm: () => $wire.closeTicket() })"
                            class="h-9 px-4 rounded-xl border border-error text-error text-sm font-semibold hover:bg-error-light transition-all duration-200 cursor-pointer active:scale-95 flex items-center gap-2">
                        <svg wire:loading wire:target="closeTicket" class="animate-spin size-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        <span wire:loading.remove wire:target="closeTicket">Close Ticket</span>
                        <span wire:loading wire:target="closeTicket">Closing...</span>
                    </button>
                    @endif
                    @endcan
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
                    <div class="space-y-4 mb-6 max-h-[500px] overflow-y-auto">
                        @forelse($ticket->comments as $comment)
                        <div class="flex gap-3 {{ $comment->is_internal ? 'bg-yellow-50/50 -mx-2 px-2 py-2 rounded-xl border border-yellow-100' : '' }}">
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
                                </div>
                                <div class="text-sm text-foreground/80">{!! nl2br(e($comment->body)) !!}</div>
                            </div>
                        </div>
                        @empty
                        <p class="text-sm text-secondary text-center py-6">No comments yet</p>
                        @endforelse
                    </div>

                    <!-- Add Comment Form -->
                    <div class="border-t border-border pt-4">
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
                            <textarea wire:model="commentBody" rows="3" placeholder="Write a reply..."
                                      class="w-full px-4 py-3 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all duration-200 resize-y"></textarea>
                            @error('commentBody') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                        </div>
                        <button wire:click="addComment" class="h-9 px-5 bg-primary hover:bg-primary-hover text-white rounded-xl text-sm font-semibold transition-all duration-200 hover:scale-[1.02] active:scale-95 cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed flex items-center gap-2" wire:loading.attr="disabled" wire:target="addComment">
                            <svg wire:loading wire:target="addComment" class="animate-spin size-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            <span wire:loading.remove wire:target="addComment">Post Reply</span>
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
            <div class="bg-white rounded-2xl border border-border p-5 space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-secondary uppercase mb-1.5">Status</label>
                    <select wire:model="newStatus" wire:change="updateStatus" class="w-full h-10 pl-3 pr-8 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary outline-none transition-all duration-200">
                        @foreach(['open', 'in_progress', 'pending', 'resolved', 'closed'] as $s)
                        <option value="{{ $s }}">{{ str_replace('_', ' ', ucfirst($s)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-secondary uppercase mb-1.5">Priority</label>
                    <select wire:model="newPriority" wire:change="updatePriority" class="w-full h-10 pl-3 pr-8 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary outline-none transition-all duration-200">
                        @foreach(['low', 'medium', 'high', 'critical'] as $p)
                        <option value="{{ $p }}">{{ ucfirst($p) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Assignment -->
            <div class="bg-white rounded-2xl border border-border p-5">
                <div class="flex items-center justify-between mb-3">
                    <label class="text-xs font-semibold text-secondary uppercase">Assigned To</label>
                    @can('tickets.assign')
                    <button wire:click="$set('showAssignModal', true)" class="text-xs font-semibold text-primary hover:underline cursor-pointer">Change</button>
                    @endcan
                </div>
                <p class="text-sm font-medium text-foreground">{{ $ticket->assignee?->name ?? 'Unassigned' }}</p>
                @if($ticket->team)
                <p class="text-xs text-secondary mt-1">Team: {{ $ticket->team->name }}</p>
                @endif
                @if($canWork && ($ticket->assigned_to || $ticket->assigned_team_id))
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
