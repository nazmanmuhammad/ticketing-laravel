@section('title', 'Tickets')
@section('page-title', 'Tickets')
@section('page-description', 'Manage support tickets')

<div>
    <!-- Toolbar -->
    <div class="flex flex-col lg:flex-row gap-4 justify-between items-start lg:items-center mb-6">
        <div class="flex flex-col sm:flex-row gap-3 flex-1 w-full lg:w-auto">
            <div class="relative flex-1 max-w-md">
                <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 size-4 text-secondary"></i>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search tickets..."
                       class="w-full h-10 pl-10 pr-4 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all duration-200">
            </div>
            <select wire:model.live="statusFilter" class="h-10 pl-3 pr-8 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary outline-none transition-all duration-200">
                <option value="">All Status</option>
                <option value="open">Open</option>
                <option value="in_progress">In Progress</option>
                <option value="pending">Pending</option>
                <option value="resolved">Resolved</option>
                <option value="closed">Closed</option>
            </select>
            <select wire:model.live="priorityFilter" class="h-10 pl-3 pr-8 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary outline-none transition-all duration-200">
                <option value="">All Priority</option>
                <option value="low">Low</option>
                <option value="medium">Medium</option>
                <option value="high">High</option>
                <option value="critical">Critical</option>
            </select>
            @if($search || $statusFilter || $priorityFilter || $categoryFilter || $assignedFilter || $dateFrom || $dateTo)
            <button wire:click="clearFilters" class="h-10 px-4 rounded-xl border border-border bg-white text-sm text-secondary hover:text-foreground hover:border-primary transition-all duration-200 cursor-pointer flex items-center gap-1.5">
                <i data-lucide="x" class="size-3.5"></i> Clear
            </button>
            @endif
        </div>
        @can('tickets.create')
        <a href="{{ route('tickets.create') }}" class="h-10 px-5 bg-primary hover:bg-primary-hover text-white rounded-xl font-semibold text-sm shadow-md shadow-primary/20 hover:shadow-primary/40 flex items-center gap-2 transition-all duration-200 hover:scale-[1.02] active:scale-95 shrink-0">
            <i data-lucide="plus" class="size-4"></i> New Ticket
        </a>
        @endcan
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl border border-border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-border bg-muted/50">
                        <th wire:click="sortBy('ticket_number')" class="px-4 py-3 text-left font-semibold text-secondary cursor-pointer hover:text-foreground transition-colors duration-150">
                            <div class="flex items-center gap-1">Ticket # @if($sortField === 'ticket_number')<i data-lucide="{{ $sortDirection === 'asc' ? 'chevron-up' : 'chevron-down' }}" class="size-3"></i>@endif</div>
                        </th>
                        <th class="px-4 py-3 text-left font-semibold text-secondary">Title</th>
                        <th class="px-4 py-3 text-left font-semibold text-secondary">Requester</th>
                        <th wire:click="sortBy('priority')" class="px-4 py-3 text-left font-semibold text-secondary cursor-pointer hover:text-foreground transition-colors duration-150">Priority</th>
                        <th wire:click="sortBy('status')" class="px-4 py-3 text-left font-semibold text-secondary cursor-pointer hover:text-foreground transition-colors duration-150">Status</th>
                        <th class="px-4 py-3 text-left font-semibold text-secondary">Assigned To</th>
                        <th class="px-4 py-3 text-left font-semibold text-secondary">SLA</th>
                        <th wire:click="sortBy('created_at')" class="px-4 py-3 text-left font-semibold text-secondary cursor-pointer hover:text-foreground transition-colors duration-150">Created</th>
                    </tr>
                </thead>
                <tbody wire:loading.remove>
                    @forelse($tickets as $ticket)
                    <tr class="border-b border-border hover:bg-muted/30 transition-colors duration-150 cursor-pointer" onclick="window.location='{{ route('tickets.show', $ticket) }}'">
                        <td class="px-4 py-3 font-mono text-xs font-semibold text-primary">{{ $ticket->ticket_number }}</td>
                        <td class="px-4 py-3">
                            <p class="font-medium text-foreground truncate max-w-[250px]">{{ $ticket->title }}</p>
                            <p class="text-xs text-secondary">{{ $ticket->category?->name }}</p>
                        </td>
                        <td class="px-4 py-3 text-secondary">{{ $ticket->requester?->name ?? '-' }}</td>
                        <td class="px-4 py-3">
                            @php
                                $pColors = ['low' => 'bg-green-50 text-green-700', 'medium' => 'bg-yellow-50 text-yellow-700', 'high' => 'bg-orange-50 text-orange-700', 'critical' => 'bg-red-50 text-red-700 animate-pulse'];
                            @endphp
                            <span class="px-2 py-1 rounded-lg text-xs font-semibold {{ $pColors[$ticket->priority] ?? '' }}">{{ ucfirst($ticket->priority) }}</span>
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $sColors = ['open' => 'bg-blue-50 text-blue-700', 'in_progress' => 'bg-purple-50 text-purple-700', 'pending' => 'bg-yellow-50 text-yellow-700', 'resolved' => 'bg-green-50 text-green-700', 'closed' => 'bg-gray-100 text-gray-600'];
                            @endphp
                            <span class="px-2 py-1 rounded-lg text-xs font-semibold {{ $sColors[$ticket->status] ?? '' }}">{{ str_replace('_', ' ', ucfirst($ticket->status)) }}</span>
                        </td>
                        <td class="px-4 py-3 text-secondary">{{ $ticket->assignee?->name ?? 'Unassigned' }}</td>
                        <td class="px-4 py-3">
                            @if($ticket->sla_due_at)
                                @php $slaService = app(\App\Services\SlaService::class); $slaStatus = $slaService->getSlaStatus($ticket); $slaColor = $slaService->getSlaColor($slaStatus); @endphp
                                <span class="px-2 py-1 rounded-lg text-xs font-semibold {{ $slaColor }}">{{ str_replace('_', ' ', ucfirst($slaStatus)) }}</span>
                            @else
                                <span class="text-xs text-secondary">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-xs text-secondary">{{ $ticket->created_at->format('M d, Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center">
                            <div class="flex flex-col items-center gap-2 opacity-60">
                                <i data-lucide="inbox" class="size-10 text-secondary"></i>
                                <p class="text-secondary font-medium">No tickets found</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tbody wire:loading>
                    @for($i = 0; $i < 6; $i++)
                    <tr class="border-b border-border">
                        <td class="px-4 py-3.5"><div class="h-4 bg-muted rounded w-20 animate-pulse"></div></td>
                        <td class="px-4 py-3.5"><div class="space-y-1.5 animate-pulse"><div class="h-4 bg-muted rounded w-44"></div><div class="h-3 bg-muted rounded w-24"></div></div></td>
                        <td class="px-4 py-3.5"><div class="h-4 bg-muted rounded w-24 animate-pulse"></div></td>
                        <td class="px-4 py-3.5"><div class="h-6 bg-muted rounded-lg w-16 animate-pulse"></div></td>
                        <td class="px-4 py-3.5"><div class="h-6 bg-muted rounded-lg w-20 animate-pulse"></div></td>
                        <td class="px-4 py-3.5"><div class="h-4 bg-muted rounded w-24 animate-pulse"></div></td>
                        <td class="px-4 py-3.5"><div class="h-2 bg-muted rounded-full w-16 animate-pulse"></div></td>
                        <td class="px-4 py-3.5"><div class="h-4 bg-muted rounded w-20 animate-pulse"></div></td>
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>
        @if($tickets->hasPages())
        <div class="px-4 py-3 border-t border-border">
            {{ $tickets->links() }}
        </div>
        @endif
    </div>
</div>
