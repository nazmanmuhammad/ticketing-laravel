@section('title', 'Reports')
@section('page-title', 'Reports')
@section('page-description', 'Helpdesk analytics and reports')

<div>
    <!-- Period Filter -->
    <div class="flex items-center gap-3 mb-6">
        <span class="text-sm font-medium text-secondary">Period:</span>
        @foreach(['7' => '7 Days', '30' => '30 Days', '90' => '90 Days', '365' => '1 Year'] as $val => $label)
        <button wire:click="$set('period', '{{ $val }}')"
                class="px-3 py-1.5 rounded-lg text-sm font-semibold transition-all duration-200 cursor-pointer {{ $period === $val ? 'bg-primary text-white' : 'bg-white border border-border text-secondary hover:border-primary hover:text-primary' }}">
            {{ $label }}
        </button>
        @endforeach
    </div>

    <!-- Quick Links -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <a href="{{ route('reports.tickets') }}" class="bg-white rounded-2xl border border-border p-5 hover:shadow-md hover:border-primary/30 transition-all duration-200 group">
            <div class="flex items-center gap-3 mb-3">
                <div class="size-10 rounded-xl bg-blue-50 flex items-center justify-center"><i data-lucide="ticket" class="size-5 text-blue-600"></i></div>
                <h3 class="font-semibold text-foreground group-hover:text-primary transition-colors">Ticket Report</h3>
            </div>
            <p class="text-xs text-secondary">Detailed ticket analytics</p>
        </a>
        <a href="{{ route('reports.access') }}" class="bg-white rounded-2xl border border-border p-5 hover:shadow-md hover:border-primary/30 transition-all duration-200 group">
            <div class="flex items-center gap-3 mb-3">
                <div class="size-10 rounded-xl bg-green-50 flex items-center justify-center"><i data-lucide="key-round" class="size-5 text-green-600"></i></div>
                <h3 class="font-semibold text-foreground group-hover:text-primary transition-colors">Access Report</h3>
            </div>
            <p class="text-xs text-secondary">Access request analytics</p>
        </a>
        <a href="{{ route('reports.changes') }}" class="bg-white rounded-2xl border border-border p-5 hover:shadow-md hover:border-primary/30 transition-all duration-200 group">
            <div class="flex items-center gap-3 mb-3">
                <div class="size-10 rounded-xl bg-purple-50 flex items-center justify-center"><i data-lucide="git-pull-request" class="size-5 text-purple-600"></i></div>
                <h3 class="font-semibold text-foreground group-hover:text-primary transition-colors">Change Report</h3>
            </div>
            <p class="text-xs text-secondary">Change request analytics</p>
        </a>
    </div>

    <!-- Summary Cards -->
    <h3 class="font-bold text-foreground mb-4">Ticket Summary</h3>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-2xl border border-border p-5">
            <p class="text-2xl font-bold text-foreground">{{ number_format($ticketStats['total']) }}</p>
            <p class="text-xs text-secondary font-medium mt-1">Total Tickets</p>
        </div>
        <div class="bg-white rounded-2xl border border-border p-5">
            <p class="text-2xl font-bold text-green-600">{{ number_format($ticketStats['resolved']) }}</p>
            <p class="text-xs text-secondary font-medium mt-1">Resolved</p>
        </div>
        <div class="bg-white rounded-2xl border border-border p-5">
            <p class="text-2xl font-bold text-foreground">{{ number_format($ticketStats['avg_resolution'], 1) }}h</p>
            <p class="text-xs text-secondary font-medium mt-1">Avg Resolution Time</p>
        </div>
        <div class="bg-white rounded-2xl border border-border p-5">
            @php $slaRate = $ticketStats['sla_total'] > 0 ? round(($ticketStats['sla_met'] / $ticketStats['sla_total']) * 100) : 0; @endphp
            <p class="text-2xl font-bold {{ $slaRate >= 80 ? 'text-green-600' : ($slaRate >= 50 ? 'text-yellow-600' : 'text-red-600') }}">{{ $slaRate }}%</p>
            <p class="text-xs text-secondary font-medium mt-1">SLA Compliance</p>
        </div>
    </div>

    <h3 class="font-bold text-foreground mb-4">Access Request Summary</h3>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-2xl border border-border p-5">
            <p class="text-2xl font-bold text-foreground">{{ number_format($accessStats['total']) }}</p>
            <p class="text-xs text-secondary font-medium mt-1">Total Requests</p>
        </div>
        <div class="bg-white rounded-2xl border border-border p-5">
            <p class="text-2xl font-bold text-green-600">{{ number_format($accessStats['approved']) }}</p>
            <p class="text-xs text-secondary font-medium mt-1">Approved</p>
        </div>
        <div class="bg-white rounded-2xl border border-border p-5">
            <p class="text-2xl font-bold text-red-600">{{ number_format($accessStats['rejected']) }}</p>
            <p class="text-xs text-secondary font-medium mt-1">Rejected</p>
        </div>
        <div class="bg-white rounded-2xl border border-border p-5">
            <p class="text-2xl font-bold text-yellow-600">{{ number_format($accessStats['pending']) }}</p>
            <p class="text-xs text-secondary font-medium mt-1">Pending</p>
        </div>
    </div>

    <h3 class="font-bold text-foreground mb-4">Change Request Summary</h3>
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl border border-border p-5">
            <p class="text-2xl font-bold text-foreground">{{ number_format($changeStats['total']) }}</p>
            <p class="text-xs text-secondary font-medium mt-1">Total Changes</p>
        </div>
        <div class="bg-white rounded-2xl border border-border p-5">
            <p class="text-2xl font-bold text-green-600">{{ number_format($changeStats['implemented']) }}</p>
            <p class="text-xs text-secondary font-medium mt-1">Implemented</p>
        </div>
        <div class="bg-white rounded-2xl border border-border p-5">
            <p class="text-2xl font-bold text-red-600">{{ number_format($changeStats['failed']) }}</p>
            <p class="text-xs text-secondary font-medium mt-1">Failed</p>
        </div>
    </div>
</div>
