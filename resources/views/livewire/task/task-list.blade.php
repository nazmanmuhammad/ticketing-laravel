@section('title', 'My Tasks')
@section('page-title', 'My Tasks')
@section('page-description', 'Pending approvals and assigned work')

<div>
    <!-- Tab Filters -->
    <div class="flex gap-2 mb-6 flex-wrap">
        @php
            $tabs = [
                'all' => ['label' => 'All', 'count' => $assignedTickets->count() + $pendingAccessApprovals->count() + $pendingChangeApprovals->count()],
                'tickets' => ['label' => 'Assigned Tickets', 'count' => $assignedTickets->count()],
                'access' => ['label' => 'Access Approvals', 'count' => $pendingAccessApprovals->count()],
                'change' => ['label' => 'Change Approvals', 'count' => $pendingChangeApprovals->count()],
            ];
        @endphp
        @foreach($tabs as $key => $tab)
        <button wire:click="$set('tabFilter', '{{ $key }}')"
                class="px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200 cursor-pointer flex items-center gap-2 {{ $tabFilter === $key ? 'bg-primary text-white shadow-md shadow-primary/20' : 'bg-white border border-border text-secondary hover:bg-muted' }}">
            {{ $tab['label'] }}
            @if($tab['count'] > 0)
            <span class="px-1.5 py-0.5 rounded-md text-xs {{ $tabFilter === $key ? 'bg-white/20' : 'bg-primary/10 text-primary' }}">{{ $tab['count'] }}</span>
            @endif
        </button>
        @endforeach
    </div>

    <!-- Assigned Tickets -->
    @if($tabFilter === 'all' || $tabFilter === 'tickets')
    @if($assignedTickets->isNotEmpty())
    <div class="mb-8">
        <h3 class="font-bold text-foreground mb-4 flex items-center gap-2">
            <i data-lucide="ticket" class="size-5 text-primary"></i> Assigned Tickets
            <span class="px-2 py-0.5 rounded-lg text-xs font-semibold bg-primary/10 text-primary">{{ $assignedTickets->count() }}</span>
        </h3>
        <div class="bg-white rounded-2xl border border-border overflow-hidden">
            <table class="w-full text-sm">
                <thead><tr class="border-b border-border bg-muted/50">
                    <th class="px-4 py-3 text-left font-semibold text-secondary">Ticket #</th>
                    <th class="px-4 py-3 text-left font-semibold text-secondary">Title</th>
                    <th class="px-4 py-3 text-left font-semibold text-secondary">Requester</th>
                    <th class="px-4 py-3 text-left font-semibold text-secondary">Priority</th>
                    <th class="px-4 py-3 text-left font-semibold text-secondary">Status</th>
                    <th class="px-4 py-3 text-left font-semibold text-secondary">Created</th>
                </tr></thead>
                <tbody>
                    @foreach($assignedTickets as $ticket)
                    <tr class="border-b border-border hover:bg-muted/30 transition-colors duration-150 cursor-pointer" onclick="window.location='{{ route('tickets.show', $ticket) }}'">
                        <td class="px-4 py-3 font-mono text-xs font-semibold text-primary">{{ $ticket->ticket_number }}</td>
                        <td class="px-4 py-3 text-foreground font-medium">{{ Str::limit($ticket->title, 50) }}</td>
                        <td class="px-4 py-3 text-secondary">{{ $ticket->requester?->name }}</td>
                        <td class="px-4 py-3">
                            @php $pColors = ['low'=>'bg-green-50 text-green-700','medium'=>'bg-yellow-50 text-yellow-700','high'=>'bg-orange-50 text-orange-700','critical'=>'bg-red-50 text-red-700']; @endphp
                            <span class="px-2 py-1 rounded-lg text-xs font-semibold {{ $pColors[$ticket->priority] ?? '' }}">{{ ucfirst($ticket->priority) }}</span>
                        </td>
                        <td class="px-4 py-3"><span class="px-2 py-1 rounded-lg text-xs font-semibold bg-blue-50 text-blue-700">{{ str_replace('_', ' ', ucfirst($ticket->status)) }}</span></td>
                        <td class="px-4 py-3 text-xs text-secondary">{{ $ticket->created_at->format('M d, Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @elseif($tabFilter === 'tickets')
    <div class="text-center py-12 text-secondary"><i data-lucide="check-circle" class="size-10 mx-auto mb-2 opacity-40"></i><p class="font-medium">No assigned tickets</p></div>
    @endif
    @endif

    <!-- Access Request Approvals -->
    @if($tabFilter === 'all' || $tabFilter === 'access')
    @if($pendingAccessApprovals->isNotEmpty())
    <div class="mb-8">
        <h3 class="font-bold text-foreground mb-4 flex items-center gap-2">
            <i data-lucide="key-round" class="size-5 text-primary"></i> Pending Access Approvals
            <span class="px-2 py-0.5 rounded-lg text-xs font-semibold bg-yellow-50 text-yellow-700">{{ $pendingAccessApprovals->count() }}</span>
        </h3>
        <div class="bg-white rounded-2xl border border-border overflow-hidden">
            <table class="w-full text-sm">
                <thead><tr class="border-b border-border bg-muted/50">
                    <th class="px-4 py-3 text-left font-semibold text-secondary">Request #</th>
                    <th class="px-4 py-3 text-left font-semibold text-secondary">Requester</th>
                    <th class="px-4 py-3 text-left font-semibold text-secondary">System</th>
                    <th class="px-4 py-3 text-left font-semibold text-secondary">Access Type</th>
                    <th class="px-4 py-3 text-left font-semibold text-secondary">Submitted</th>
                    <th class="px-4 py-3 text-left font-semibold text-secondary">Action</th>
                </tr></thead>
                <tbody>
                    @foreach($pendingAccessApprovals as $req)
                    <tr class="border-b border-border hover:bg-muted/30 transition-colors duration-150">
                        <td class="px-4 py-3 font-mono text-xs font-semibold text-primary">{{ $req->request_number }}</td>
                        <td class="px-4 py-3 text-foreground">{{ $req->requester?->name }}</td>
                        <td class="px-4 py-3 text-secondary">{{ $req->system?->name }}</td>
                        <td class="px-4 py-3"><span class="px-2 py-1 rounded-lg text-xs font-semibold bg-blue-50 text-blue-700">{{ ucfirst($req->access_type) }}</span></td>
                        <td class="px-4 py-3 text-xs text-secondary">{{ $req->created_at->format('M d, Y') }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('access-requests.show', $req) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-primary text-white hover:bg-primary-hover transition-colors">Review</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @elseif($tabFilter === 'access')
    <div class="text-center py-12 text-secondary"><i data-lucide="check-circle" class="size-10 mx-auto mb-2 opacity-40"></i><p class="font-medium">No pending access approvals</p></div>
    @endif
    @endif

    <!-- Change Request Approvals -->
    @if($tabFilter === 'all' || $tabFilter === 'change')
    @if($pendingChangeApprovals->isNotEmpty())
    <div class="mb-8">
        <h3 class="font-bold text-foreground mb-4 flex items-center gap-2">
            <i data-lucide="git-pull-request" class="size-5 text-primary"></i> Pending Change Approvals
            <span class="px-2 py-0.5 rounded-lg text-xs font-semibold bg-yellow-50 text-yellow-700">{{ $pendingChangeApprovals->count() }}</span>
        </h3>
        <div class="bg-white rounded-2xl border border-border overflow-hidden">
            <table class="w-full text-sm">
                <thead><tr class="border-b border-border bg-muted/50">
                    <th class="px-4 py-3 text-left font-semibold text-secondary">Request #</th>
                    <th class="px-4 py-3 text-left font-semibold text-secondary">Title</th>
                    <th class="px-4 py-3 text-left font-semibold text-secondary">Requester</th>
                    <th class="px-4 py-3 text-left font-semibold text-secondary">System</th>
                    <th class="px-4 py-3 text-left font-semibold text-secondary">Type</th>
                    <th class="px-4 py-3 text-left font-semibold text-secondary">Action</th>
                </tr></thead>
                <tbody>
                    @foreach($pendingChangeApprovals as $cr)
                    <tr class="border-b border-border hover:bg-muted/30 transition-colors duration-150">
                        <td class="px-4 py-3 font-mono text-xs font-semibold text-primary">{{ $cr->request_number }}</td>
                        <td class="px-4 py-3 text-foreground font-medium">{{ Str::limit($cr->title, 40) }}</td>
                        <td class="px-4 py-3 text-secondary">{{ $cr->requester?->name }}</td>
                        <td class="px-4 py-3 text-secondary">{{ $cr->system?->name }}</td>
                        <td class="px-4 py-3">
                            @php $tColors = ['standard'=>'bg-green-50 text-green-700','normal'=>'bg-blue-50 text-blue-700','emergency'=>'bg-red-50 text-red-700']; @endphp
                            <span class="px-2 py-1 rounded-lg text-xs font-semibold {{ $tColors[$cr->change_type] ?? '' }}">{{ ucfirst($cr->change_type) }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('change-requests.show', $cr) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-primary text-white hover:bg-primary-hover transition-colors">Review</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @elseif($tabFilter === 'change')
    <div class="text-center py-12 text-secondary"><i data-lucide="check-circle" class="size-10 mx-auto mb-2 opacity-40"></i><p class="font-medium">No pending change approvals</p></div>
    @endif
    @endif

    <!-- Empty state for all -->
    @if($tabFilter === 'all' && $assignedTickets->isEmpty() && $pendingAccessApprovals->isEmpty() && $pendingChangeApprovals->isEmpty())
    <div class="text-center py-16">
        <i data-lucide="check-circle-2" class="size-16 text-green-400 mx-auto mb-4"></i>
        <h3 class="text-lg font-bold text-foreground mb-1">All caught up!</h3>
        <p class="text-secondary">You have no pending tasks or approvals.</p>
    </div>
    @endif
</div>
