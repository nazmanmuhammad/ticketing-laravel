@section('title', 'Change Requests')
@section('page-title', 'Change Requests')
@section('page-description', 'Manage change requests')

<div>
    <div class="flex flex-col lg:flex-row gap-4 justify-between items-start lg:items-center mb-6">
        <div class="flex flex-col sm:flex-row gap-3 flex-1 w-full lg:w-auto">
            <div class="relative flex-1 max-w-md">
                <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 size-4 text-secondary"></i>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search change requests..."
                       class="w-full h-10 pl-10 pr-4 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all duration-200">
            </div>
            <select wire:model.live="statusFilter" class="h-10 pl-3 pr-8 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary outline-none transition-all duration-200">
                <option value="">All Status</option>
                @foreach(['draft','submitted','under_review','approved','scheduled','implemented','closed','failed'] as $s)
                <option value="{{ $s }}">{{ str_replace('_', ' ', ucfirst($s)) }}</option>
                @endforeach
            </select>
            <select wire:model.live="typeFilter" class="h-10 pl-3 pr-8 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary outline-none transition-all duration-200">
                <option value="">All Types</option>
                <option value="standard">Standard</option>
                <option value="normal">Normal</option>
                <option value="emergency">Emergency</option>
            </select>
        </div>
        <div class="flex gap-2 shrink-0">
            @can('change_requests.view')
            <a href="{{ route('change-requests.calendar') }}" class="h-10 px-4 rounded-xl border border-border bg-white text-sm font-semibold text-secondary hover:text-foreground hover:border-primary flex items-center gap-2 transition-all duration-200">
                <i data-lucide="calendar" class="size-4"></i> Calendar
            </a>
            @endcan
            @can('change_requests.create')
            <a href="{{ route('change-requests.create') }}" class="h-10 px-5 bg-primary hover:bg-primary-hover text-white rounded-xl font-semibold text-sm shadow-md shadow-primary/20 flex items-center gap-2 transition-all duration-200 hover:scale-[1.02] active:scale-95">
                <i data-lucide="plus" class="size-4"></i> New Request
            </a>
            @endcan
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-border bg-muted/50">
                        <th wire:click="sortBy('request_number')" class="px-4 py-3 text-left font-semibold text-secondary cursor-pointer hover:text-foreground transition-colors">Request #</th>
                        <th class="px-4 py-3 text-left font-semibold text-secondary">Title</th>
                        <th class="px-4 py-3 text-left font-semibold text-secondary">Type</th>
                        <th class="px-4 py-3 text-left font-semibold text-secondary">System</th>
                        <th wire:click="sortBy('status')" class="px-4 py-3 text-left font-semibold text-secondary cursor-pointer hover:text-foreground transition-colors">Status</th>
                        <th class="px-4 py-3 text-left font-semibold text-secondary">Scheduled</th>
                        <th wire:click="sortBy('created_at')" class="px-4 py-3 text-left font-semibold text-secondary cursor-pointer hover:text-foreground transition-colors">Created</th>
                    </tr>
                </thead>
                <tbody wire:loading.remove>
                    @forelse($requests as $req)
                    <tr class="border-b border-border hover:bg-muted/30 transition-colors duration-150 cursor-pointer" onclick="window.location='{{ route('change-requests.show', $req) }}'">
                        <td class="px-4 py-3 font-mono text-xs font-semibold text-primary">{{ $req->request_number }}</td>
                        <td class="px-4 py-3"><p class="font-medium text-foreground truncate max-w-[220px]">{{ $req->title }}</p></td>
                        <td class="px-4 py-3">
                            @php $tColors = ['standard'=>'bg-green-50 text-green-700','normal'=>'bg-blue-50 text-blue-700','emergency'=>'bg-red-50 text-red-700']; @endphp
                            <span class="px-2 py-1 rounded-lg text-xs font-semibold {{ $tColors[$req->change_type] ?? '' }}">{{ ucfirst($req->change_type) }}</span>
                        </td>
                        <td class="px-4 py-3 text-secondary">{{ $req->system?->name }}</td>
                        <td class="px-4 py-3">
                            @php $sColors = ['draft'=>'bg-gray-100 text-gray-600','submitted'=>'bg-blue-50 text-blue-700','under_review'=>'bg-yellow-50 text-yellow-700','approved'=>'bg-green-50 text-green-700','scheduled'=>'bg-indigo-50 text-indigo-700','implemented'=>'bg-purple-50 text-purple-700','closed'=>'bg-gray-100 text-gray-600','failed'=>'bg-red-50 text-red-700']; @endphp
                            <span class="px-2 py-1 rounded-lg text-xs font-semibold {{ $sColors[$req->status] ?? '' }}">{{ str_replace('_', ' ', ucfirst($req->status)) }}</span>
                        </td>
                        <td class="px-4 py-3 text-xs text-secondary">{{ $req->scheduled_at?->format('M d, H:i') ?? '-' }}</td>
                        <td class="px-4 py-3 text-xs text-secondary">{{ $req->created_at->format('M d, Y') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-4 py-12 text-center"><div class="flex flex-col items-center gap-2 opacity-60"><i data-lucide="inbox" class="size-10 text-secondary"></i><p class="text-secondary font-medium">No change requests found</p></div></td></tr>
                    @endforelse
                </tbody>
                <tbody wire:loading>
                    @for($i = 0; $i < 6; $i++)
                    <tr class="border-b border-border">
                        <td class="px-4 py-3.5"><div class="h-4 bg-muted rounded w-20 animate-pulse"></div></td>
                        <td class="px-4 py-3.5"><div class="h-4 bg-muted rounded w-40 animate-pulse"></div></td>
                        <td class="px-4 py-3.5"><div class="h-6 bg-muted rounded-lg w-16 animate-pulse"></div></td>
                        <td class="px-4 py-3.5"><div class="h-4 bg-muted rounded w-24 animate-pulse"></div></td>
                        <td class="px-4 py-3.5"><div class="h-6 bg-muted rounded-lg w-24 animate-pulse"></div></td>
                        <td class="px-4 py-3.5"><div class="h-4 bg-muted rounded w-20 animate-pulse"></div></td>
                        <td class="px-4 py-3.5"><div class="h-4 bg-muted rounded w-20 animate-pulse"></div></td>
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>
        @if($requests->hasPages())
        <div class="px-4 py-3 border-t border-border">{{ $requests->links() }}</div>
        @endif
    </div>
</div>
