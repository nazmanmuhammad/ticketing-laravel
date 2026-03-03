@section('title', 'Access Requests')
@section('page-title', 'Access Requests')
@section('page-description', 'Manage system access requests')

<div>
    <div class="flex flex-col lg:flex-row gap-4 justify-between items-start lg:items-center mb-6">
        <div class="flex flex-col sm:flex-row gap-3 flex-1 w-full lg:w-auto">
            <div class="relative flex-1 max-w-md">
                <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 size-4 text-secondary"></i>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search by request number..."
                       class="w-full h-10 pl-10 pr-4 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all duration-200">
            </div>
            <select wire:model.live="statusFilter" class="h-10 pl-3 pr-8 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary outline-none transition-all duration-200">
                <option value="">All Status</option>
                @foreach(['draft','submitted','pending_approval','approved','rejected','implemented'] as $s)
                <option value="{{ $s }}">{{ str_replace('_', ' ', ucfirst($s)) }}</option>
                @endforeach
            </select>
            <select wire:model.live="systemFilter" class="h-10 pl-3 pr-8 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary outline-none transition-all duration-200">
                <option value="">All Systems</option>
                @foreach($systems as $sys)
                <option value="{{ $sys->id }}">{{ $sys->name }}</option>
                @endforeach
            </select>
        </div>
        @can('access_requests.create')
        <a href="{{ route('access-requests.create') }}" class="h-10 px-5 bg-primary hover:bg-primary-hover text-white rounded-xl font-semibold text-sm shadow-md shadow-primary/20 flex items-center gap-2 transition-all duration-200 hover:scale-[1.02] active:scale-95 shrink-0">
            <i data-lucide="plus" class="size-4"></i> New Request
        </a>
        @endcan
    </div>

    <div class="bg-white rounded-2xl border border-border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-border bg-muted/50">
                        <th wire:click="sortBy('request_number')" class="px-4 py-3 text-left font-semibold text-secondary cursor-pointer hover:text-foreground transition-colors">Request #</th>
                        <th class="px-4 py-3 text-left font-semibold text-secondary">Requester</th>
                        <th class="px-4 py-3 text-left font-semibold text-secondary">System</th>
                        <th class="px-4 py-3 text-left font-semibold text-secondary">Access Type</th>
                        <th wire:click="sortBy('status')" class="px-4 py-3 text-left font-semibold text-secondary cursor-pointer hover:text-foreground transition-colors">Status</th>
                        <th wire:click="sortBy('created_at')" class="px-4 py-3 text-left font-semibold text-secondary cursor-pointer hover:text-foreground transition-colors">Created</th>
                    </tr>
                </thead>
                <tbody wire:loading.remove>
                    @forelse($requests as $req)
                    <tr class="border-b border-border hover:bg-muted/30 transition-colors duration-150 cursor-pointer" onclick="window.location='{{ route('access-requests.show', $req) }}'">
                        <td class="px-4 py-3 font-mono text-xs font-semibold text-primary">{{ $req->request_number }}</td>
                        <td class="px-4 py-3 text-foreground">{{ $req->requester?->name }}</td>
                        <td class="px-4 py-3 text-secondary">{{ $req->system?->name }}</td>
                        <td class="px-4 py-3"><span class="px-2 py-1 rounded-lg text-xs font-semibold bg-blue-50 text-blue-700">{{ ucfirst($req->access_type) }}</span></td>
                        <td class="px-4 py-3">
                            @php
                                $sColors = ['draft'=>'bg-gray-100 text-gray-600','submitted'=>'bg-blue-50 text-blue-700','pending_approval'=>'bg-yellow-50 text-yellow-700','approved'=>'bg-green-50 text-green-700','rejected'=>'bg-red-50 text-red-700','implemented'=>'bg-purple-50 text-purple-700'];
                            @endphp
                            <span class="px-2 py-1 rounded-lg text-xs font-semibold {{ $sColors[$req->status] ?? 'bg-gray-100 text-gray-600' }}">{{ str_replace('_', ' ', ucfirst($req->status)) }}</span>
                        </td>
                        <td class="px-4 py-3 text-xs text-secondary">{{ $req->created_at->format('M d, Y') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-12 text-center"><div class="flex flex-col items-center gap-2 opacity-60"><i data-lucide="inbox" class="size-10 text-secondary"></i><p class="text-secondary font-medium">No access requests found</p></div></td></tr>
                    @endforelse
                </tbody>
                <tbody wire:loading>
                    @for($i = 0; $i < 6; $i++)
                    <tr class="border-b border-border">
                        <td class="px-4 py-3.5"><div class="h-4 bg-muted rounded w-20 animate-pulse"></div></td>
                        <td class="px-4 py-3.5"><div class="h-4 bg-muted rounded w-28 animate-pulse"></div></td>
                        <td class="px-4 py-3.5"><div class="h-4 bg-muted rounded w-24 animate-pulse"></div></td>
                        <td class="px-4 py-3.5"><div class="h-6 bg-muted rounded-lg w-16 animate-pulse"></div></td>
                        <td class="px-4 py-3.5"><div class="h-6 bg-muted rounded-lg w-24 animate-pulse"></div></td>
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
