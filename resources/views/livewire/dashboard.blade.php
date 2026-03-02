@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-description', 'Overview of your helpdesk system')

<div>
    <!-- Summary Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 mb-8">
        <div class="bg-white rounded-2xl p-5 border border-border hover:shadow-md transition-all duration-200">
            <div class="flex items-center gap-3 mb-3">
                <div class="size-10 rounded-xl bg-blue-50 flex items-center justify-center"><i data-lucide="ticket" class="size-5 text-blue-600"></i></div>
            </div>
            <p class="text-2xl font-bold text-foreground">{{ number_format($ticketStats['total']) }}</p>
            <p class="text-xs text-secondary font-medium mt-1">Total Tickets</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-border hover:shadow-md transition-all duration-200">
            <div class="flex items-center gap-3 mb-3">
                <div class="size-10 rounded-xl bg-orange-50 flex items-center justify-center"><i data-lucide="circle-dot" class="size-5 text-orange-600"></i></div>
            </div>
            <p class="text-2xl font-bold text-foreground">{{ number_format($ticketStats['open']) }}</p>
            <p class="text-xs text-secondary font-medium mt-1">Open</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-border hover:shadow-md transition-all duration-200">
            <div class="flex items-center gap-3 mb-3">
                <div class="size-10 rounded-xl bg-purple-50 flex items-center justify-center"><i data-lucide="loader" class="size-5 text-purple-600"></i></div>
            </div>
            <p class="text-2xl font-bold text-foreground">{{ number_format($ticketStats['in_progress']) }}</p>
            <p class="text-xs text-secondary font-medium mt-1">In Progress</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-border hover:shadow-md transition-all duration-200">
            <div class="flex items-center gap-3 mb-3">
                <div class="size-10 rounded-xl bg-green-50 flex items-center justify-center"><i data-lucide="check-circle" class="size-5 text-green-600"></i></div>
            </div>
            <p class="text-2xl font-bold text-foreground">{{ number_format($ticketStats['resolved']) }}</p>
            <p class="text-xs text-secondary font-medium mt-1">Resolved</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-border hover:shadow-md transition-all duration-200">
            <div class="flex items-center gap-3 mb-3">
                <div class="size-10 rounded-xl bg-yellow-50 flex items-center justify-center"><i data-lucide="clock" class="size-5 text-yellow-600"></i></div>
            </div>
            <p class="text-2xl font-bold text-foreground">{{ $pendingApprovals }}</p>
            <p class="text-xs text-secondary font-medium mt-1">Pending Approvals</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Ticket Trend Chart -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-border p-6">
            <h3 class="font-semibold text-foreground mb-4">Ticket Trend (Last 30 Days)</h3>
            <canvas id="ticketTrendChart" height="100"></canvas>
        </div>
        <!-- Priority Distribution -->
        <div class="bg-white rounded-2xl border border-border p-6">
            <h3 class="font-semibold text-foreground mb-4">By Priority</h3>
            <canvas id="priorityChart" height="200"></canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- SLA Breach Alerts -->
        <div class="bg-white rounded-2xl border border-border p-6">
            <h3 class="font-semibold text-foreground mb-4 flex items-center gap-2">
                <i data-lucide="alert-triangle" class="size-5 text-error"></i> SLA Breaches
            </h3>
            @forelse($slaBreaches->take(5) as $ticket)
            <a href="{{ route('tickets.show', $ticket) }}" class="flex items-center justify-between py-3 border-b border-border last:border-0 hover:bg-muted -mx-2 px-2 rounded-lg transition-colors duration-150">
                <div>
                    <p class="text-sm font-medium text-foreground">{{ $ticket->ticket_number }}</p>
                    <p class="text-xs text-secondary truncate max-w-[200px]">{{ $ticket->title }}</p>
                </div>
                <span class="text-xs font-semibold text-red-600 bg-red-50 px-2 py-1 rounded-lg animate-pulse">Breached</span>
            </a>
            @empty
            <p class="text-sm text-secondary py-4 text-center">No SLA breaches</p>
            @endforelse
        </div>

        <!-- Recent Activities -->
        <div class="bg-white rounded-2xl border border-border p-6">
            <h3 class="font-semibold text-foreground mb-4">Recent Activities</h3>
            @forelse($recentActivities as $activity)
            <div class="flex items-start gap-3 py-3 border-b border-border last:border-0">
                <div class="size-8 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-xs shrink-0 mt-0.5">
                    {{ strtoupper(substr($activity->user->name ?? 'S', 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <p class="text-sm text-foreground"><span class="font-medium">{{ $activity->user->name ?? 'System' }}</span> {{ $activity->description }}</p>
                    <p class="text-xs text-secondary">{{ $activity->ticket->ticket_number ?? '' }} &middot; {{ $activity->created_at->diffForHumans() }}</p>
                </div>
            </div>
            @empty
            <p class="text-sm text-secondary py-4 text-center">No recent activities</p>
            @endforelse
        </div>
    </div>
</div>

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const trendData = @json($ticketTrend);
    new Chart(document.getElementById('ticketTrendChart'), {
        type: 'line',
        data: {
            labels: trendData.map(d => d.date),
            datasets: [{
                label: 'Tickets',
                data: trendData.map(d => d.count),
                borderColor: '#165DFF',
                backgroundColor: 'rgba(22, 93, 255, 0.1)',
                fill: true,
                tension: 0.4,
                pointRadius: 0,
                pointHoverRadius: 4,
            }]
        },
        options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } }, x: { display: true, ticks: { maxTicksLimit: 10 } } } }
    });

    const priorityData = @json($ticketsByPriority);
    new Chart(document.getElementById('priorityChart'), {
        type: 'doughnut',
        data: {
            labels: ['Low', 'Medium', 'High', 'Critical'],
            datasets: [{
                data: [priorityData.low, priorityData.medium, priorityData.high, priorityData.critical],
                backgroundColor: ['#30B22D', '#F59E0B', '#ED6B60', '#DC2626'],
            }]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
    });
});
</script>
@endsection
