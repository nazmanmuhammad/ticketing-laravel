@section('title', 'Pending Approvals')
@section('page-title', 'Pending Approvals')
@section('page-description', 'Access requests awaiting your approval')

<div>
    <div class="bg-white rounded-2xl border border-border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-border bg-muted/50">
                        <th class="px-4 py-3 text-left font-semibold text-secondary">Request #</th>
                        <th class="px-4 py-3 text-left font-semibold text-secondary">Requester</th>
                        <th class="px-4 py-3 text-left font-semibold text-secondary">System</th>
                        <th class="px-4 py-3 text-left font-semibold text-secondary">Access Type</th>
                        <th class="px-4 py-3 text-left font-semibold text-secondary">Submitted</th>
                        <th class="px-4 py-3 text-left font-semibold text-secondary">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                    <tr class="border-b border-border hover:bg-muted/30 transition-colors duration-150">
                        <td class="px-4 py-3 font-mono text-xs font-semibold text-primary">{{ $req->request_number }}</td>
                        <td class="px-4 py-3 text-foreground">{{ $req->requester?->name }}</td>
                        <td class="px-4 py-3 text-secondary">{{ $req->system?->name }}</td>
                        <td class="px-4 py-3"><span class="px-2 py-1 rounded-lg text-xs font-semibold bg-blue-50 text-blue-700">{{ ucfirst($req->access_type) }}</span></td>
                        <td class="px-4 py-3 text-xs text-secondary">{{ $req->created_at->diffForHumans() }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('access-requests.show', $req) }}" class="text-sm font-semibold text-primary hover:underline">Review</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-12 text-center"><div class="flex flex-col items-center gap-2 opacity-60"><i data-lucide="check-circle" class="size-10 text-green-400"></i><p class="text-secondary font-medium">No pending approvals</p></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($requests->hasPages())
        <div class="px-4 py-3 border-t border-border">{{ $requests->links() }}</div>
        @endif
    </div>
</div>
