@section('title', 'SLA Settings')
@section('page-title', 'Settings')
@section('page-description', 'Manage SLA targets')

<div>
    @include('livewire.settings.partials.settings-nav')

    <div class="flex items-center justify-between mb-6">
        <h3 class="font-bold text-foreground">SLA Settings</h3>
        <button wire:click="save" class="h-9 px-5 bg-primary hover:bg-primary-hover text-white rounded-xl text-sm font-semibold transition-all duration-200 active:scale-95 cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed flex items-center gap-2" wire:loading.attr="disabled" wire:target="save">
            <svg wire:loading wire:target="save" class="animate-spin size-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
            <span wire:loading.remove wire:target="save">Save Changes</span>
            <span wire:loading wire:target="save">Saving...</span>
        </button>
    </div>

    <div class="bg-white rounded-2xl border border-border overflow-hidden">
        <table class="w-full text-sm">
            <thead><tr class="border-b border-border bg-muted/50">
                <th class="px-4 py-3 text-left font-semibold text-secondary">Priority</th>
                <th class="px-4 py-3 text-left font-semibold text-secondary">Response Time (hours)</th>
                <th class="px-4 py-3 text-left font-semibold text-secondary">Resolution Time (hours)</th>
            </tr></thead>
            <tbody>
                @foreach($slaData as $i => $item)
                <tr class="border-b border-border">
                    <td class="px-4 py-3">
                        @php $pColors = ['low' => 'bg-green-50 text-green-700', 'medium' => 'bg-yellow-50 text-yellow-700', 'high' => 'bg-orange-50 text-orange-700', 'critical' => 'bg-red-50 text-red-700']; @endphp
                        <span class="px-3 py-1 rounded-lg text-xs font-semibold {{ $pColors[$item['priority']] ?? '' }}">{{ ucfirst($item['priority']) }}</span>
                    </td>
                    <td class="px-4 py-3">
                        <input wire:model="slaData.{{ $i }}.response_hours" type="number" min="1" class="w-24 h-9 px-3 rounded-xl border border-border bg-white text-sm text-center focus:ring-2 focus:ring-primary outline-none transition-all duration-200">
                    </td>
                    <td class="px-4 py-3">
                        <input wire:model="slaData.{{ $i }}.resolution_hours" type="number" min="1" class="w-24 h-9 px-3 rounded-xl border border-border bg-white text-sm text-center focus:ring-2 focus:ring-primary outline-none transition-all duration-200">
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4 p-4 bg-blue-50 rounded-xl border border-blue-100">
        <p class="text-sm text-blue-700"><i data-lucide="info" class="size-4 inline mr-1"></i> Response time = max time to first response. Resolution time = max time to resolve the ticket.</p>
    </div>
</div>
