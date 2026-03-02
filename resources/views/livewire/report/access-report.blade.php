@section('title', 'Access Request Report')
@section('page-title', 'Access Request Report')
@section('page-description', 'Access request analytics')

<div>
    <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center mb-6">
        <div class="flex gap-3">
            <input wire:model.live="dateFrom" type="date" class="h-10 px-4 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary outline-none transition-all duration-200">
            <span class="text-secondary self-center">to</span>
            <input wire:model.live="dateTo" type="date" class="h-10 px-4 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary outline-none transition-all duration-200">
        </div>
        <a href="{{ route('reports.dashboard') }}" class="text-sm font-semibold text-primary hover:underline">&larr; Back to Reports</a>
    </div>

    <div class="bg-white rounded-2xl border border-border p-5 mb-6">
        <p class="text-lg font-bold text-foreground">{{ number_format($total) }} <span class="text-sm font-medium text-secondary">total access requests</span></p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl border border-border p-6">
            <h3 class="font-semibold text-foreground mb-4">By Status</h3>
            @foreach($byStatus as $label => $count)
            <div class="flex items-center justify-between py-2 border-b border-border last:border-0">
                <span class="text-sm text-foreground">{{ str_replace('_', ' ', ucfirst($label)) }}</span>
                <span class="text-sm font-semibold text-foreground">{{ $count }}</span>
            </div>
            @endforeach
        </div>
        <div class="bg-white rounded-2xl border border-border p-6">
            <h3 class="font-semibold text-foreground mb-4">By System</h3>
            @foreach($bySystem as $label => $count)
            <div class="flex items-center justify-between py-2 border-b border-border last:border-0">
                <span class="text-sm text-foreground">{{ $label }}</span>
                <span class="text-sm font-semibold text-foreground">{{ $count }}</span>
            </div>
            @endforeach
            @if(empty($bySystem))
            <p class="text-sm text-secondary text-center py-4">No data</p>
            @endif
        </div>
        <div class="bg-white rounded-2xl border border-border p-6">
            <h3 class="font-semibold text-foreground mb-4">By Access Type</h3>
            @foreach($byType as $label => $count)
            <div class="flex items-center justify-between py-2 border-b border-border last:border-0">
                <span class="text-sm text-foreground">{{ ucfirst($label) }}</span>
                <span class="text-sm font-semibold text-foreground">{{ $count }}</span>
            </div>
            @endforeach
            @if(empty($byType))
            <p class="text-sm text-secondary text-center py-4">No data</p>
            @endif
        </div>
    </div>
</div>
