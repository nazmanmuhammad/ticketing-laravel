@section('title', 'Ticket Report')
@section('page-title', 'Ticket Report')
@section('page-description', 'Detailed ticket analytics')

<div>
    <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center mb-6">
        <div class="flex gap-3">
            <input wire:model.live="dateFrom" type="date" class="h-10 px-4 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary outline-none transition-all duration-200">
            <span class="text-secondary self-center">to</span>
            <input wire:model.live="dateTo" type="date" class="h-10 px-4 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary outline-none transition-all duration-200">
        </div>
        <select wire:model.live="groupBy" class="h-10 pl-3 pr-8 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary outline-none transition-all duration-200">
            <option value="status">Group by Status</option>
            <option value="priority">Group by Priority</option>
            <option value="category">Group by Category</option>
            <option value="agent">Group by Agent</option>
        </select>
        <a href="{{ route('reports.dashboard') }}" class="text-sm font-semibold text-primary hover:underline">&larr; Back to Reports</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-2xl border border-border p-6">
            <h3 class="font-semibold text-foreground mb-4">Ticket Trend</h3>
            <canvas id="trendChart" height="150"></canvas>
        </div>
        <div class="bg-white rounded-2xl border border-border p-6">
            <h3 class="font-semibold text-foreground mb-4">Distribution ({{ ucfirst($groupBy) }})</h3>
            <canvas id="distributionChart" height="150"></canvas>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-border p-6">
        <h3 class="font-semibold text-foreground mb-4">Summary — {{ number_format($totalTickets) }} tickets</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-border bg-muted/50">
                        <th class="px-4 py-3 text-left font-semibold text-secondary">{{ ucfirst($groupBy) }}</th>
                        <th class="px-4 py-3 text-right font-semibold text-secondary">Count</th>
                        <th class="px-4 py-3 text-right font-semibold text-secondary">Percentage</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($groupedData as $label => $count)
                    <tr class="border-b border-border">
                        <td class="px-4 py-3 font-medium text-foreground">{{ str_replace('_', ' ', ucfirst($label)) }}</td>
                        <td class="px-4 py-3 text-right text-foreground font-semibold">{{ number_format($count) }}</td>
                        <td class="px-4 py-3 text-right text-secondary">{{ $totalTickets > 0 ? round(($count / $totalTickets) * 100, 1) : 0 }}%</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const trendData = @json($trend);
    new Chart(document.getElementById('trendChart'), {
        type: 'line', data: { labels: trendData.map(d => d.date), datasets: [{ label: 'Tickets', data: trendData.map(d => d.count), borderColor: '#165DFF', backgroundColor: 'rgba(22,93,255,0.1)', fill: true, tension: 0.4, pointRadius: 0 }] },
        options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
    });
    const distData = @json($groupedData);
    const colors = ['#165DFF','#30B22D','#F59E0B','#ED6B60','#8B5CF6','#06B6D4','#EC4899','#F97316'];
    new Chart(document.getElementById('distributionChart'), {
        type: 'bar', data: { labels: Object.keys(distData).map(k => k.replace('_',' ')), datasets: [{ data: Object.values(distData), backgroundColor: colors.slice(0, Object.keys(distData).length) }] },
        options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
    });
});
</script>
@endsection
