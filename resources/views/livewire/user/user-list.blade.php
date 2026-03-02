@section('title', 'Users')
@section('page-title', 'Users')
@section('page-description', 'Manage system users')

<div>
    <div class="flex flex-col sm:flex-row gap-4 justify-between items-start sm:items-center mb-6">
        <div class="flex flex-col sm:flex-row gap-3 flex-1 w-full sm:w-auto">
            <div class="relative flex-1 max-w-md">
                <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 size-4 text-secondary"></i>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search users..."
                       class="w-full h-10 pl-10 pr-4 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all duration-200">
            </div>
            <select wire:model.live="roleFilter" class="h-10 pl-3 pr-8 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary outline-none transition-all duration-200">
                <option value="">All Roles</option>
                @foreach($roles as $role)
                <option value="{{ $role->name }}">{{ $role->name }}</option>
                @endforeach
            </select>
        </div>
        @can('users.create')
        <a href="{{ route('users.create') }}" class="h-10 px-5 bg-primary hover:bg-primary-hover text-white rounded-xl font-semibold text-sm shadow-md shadow-primary/20 flex items-center gap-2 transition-all duration-200 hover:scale-[1.02] active:scale-95 shrink-0">
            <i data-lucide="plus" class="size-4"></i> New User
        </a>
        @endcan
    </div>

    <div wire:loading.delay class="mb-4"><div class="h-1 w-full bg-muted rounded-full overflow-hidden"><div class="h-full bg-primary rounded-full animate-pulse" style="width:60%"></div></div></div>

    <div class="bg-white rounded-2xl border border-border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-border bg-muted/50">
                        <th class="px-4 py-3 text-left font-semibold text-secondary">User</th>
                        <th class="px-4 py-3 text-left font-semibold text-secondary">Email</th>
                        <th class="px-4 py-3 text-left font-semibold text-secondary">Role</th>
                        <th class="px-4 py-3 text-left font-semibold text-secondary">Team</th>
                        <th class="px-4 py-3 text-left font-semibold text-secondary">Department</th>
                        <th class="px-4 py-3 text-left font-semibold text-secondary">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr class="border-b border-border hover:bg-muted/30 transition-colors duration-150">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="size-8 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-xs">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                                <span class="font-medium text-foreground">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-secondary">{{ $user->email }}</td>
                        <td class="px-4 py-3">
                            @foreach($user->roles as $role)
                            <span class="px-2 py-0.5 rounded-lg text-xs font-semibold bg-primary/10 text-primary">{{ $role->name }}</span>
                            @endforeach
                        </td>
                        <td class="px-4 py-3 text-secondary">
                            @if($user->teams->isNotEmpty())
                                {{ $user->teams->pluck('name')->join(', ') }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-4 py-3 text-secondary">{{ $user->department ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex gap-1">
                                @can('users.edit')
                                <a href="{{ route('users.edit', $user) }}" class="size-8 rounded-lg hover:bg-muted flex items-center justify-center transition-colors cursor-pointer"><i data-lucide="pencil" class="size-3.5 text-secondary"></i></a>
                                @endcan
                                @can('users.delete')
                                @if(!$user->hasRole('Super Admin'))
                                <button @click="$dispatch('confirm-dialog', { title: 'Delete User', message: 'Delete user \'{{ $user->name }}\'? This action cannot be undone.', onConfirm: () => $wire.deleteUser({{ $user->id }}) })" class="size-8 rounded-lg hover:bg-red-50 flex items-center justify-center transition-colors cursor-pointer"><i data-lucide="trash-2" class="size-3.5 text-error"></i></button>
                                @endif
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-12 text-center"><div class="flex flex-col items-center gap-2 opacity-60"><i data-lucide="users" class="size-10 text-secondary"></i><p class="text-secondary font-medium">No users found</p></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
        <div class="px-4 py-3 border-t border-border">{{ $users->links() }}</div>
        @endif
    </div>
</div>
