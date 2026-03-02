@section('title', 'Roles & Permissions')
@section('page-title', 'Roles & Permissions')
@section('page-description', 'Manage user roles and permissions')

<div>
    <div class="flex flex-col sm:flex-row gap-4 justify-between items-start sm:items-center mb-6">
        <div class="relative flex-1 max-w-md">
            <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 size-4 text-secondary"></i>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search roles..."
                   class="w-full h-10 pl-10 pr-4 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all duration-200">
        </div>
        @can('roles.create')
        <a href="{{ route('roles.create') }}" class="h-10 px-5 bg-primary hover:bg-primary-hover text-white rounded-xl font-semibold text-sm shadow-md shadow-primary/20 flex items-center gap-2 transition-all duration-200 hover:scale-[1.02] active:scale-95 shrink-0">
            <i data-lucide="plus" class="size-4"></i> New Role
        </a>
        @endcan
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach($roles as $role)
        <div class="bg-white rounded-2xl border border-border p-5 hover:shadow-md hover:border-primary/30 transition-all duration-200">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <h3 class="font-bold text-foreground">{{ $role->name }}</h3>
                    <p class="text-xs text-secondary mt-0.5">{{ $role->permissions_count }} permissions · {{ $role->users_count }} users</p>
                </div>
                @if($role->name !== 'Super Admin')
                <div class="flex gap-1">
                    @can('roles.edit')
                    <a href="{{ route('roles.edit', $role) }}" class="size-8 rounded-lg hover:bg-muted flex items-center justify-center transition-colors cursor-pointer"><i data-lucide="pencil" class="size-3.5 text-secondary"></i></a>
                    @endcan
                    @can('roles.delete')
                    <button @click="$dispatch('confirm-dialog', { title: 'Delete Role', message: 'Delete role \'{{ $role->name }}\'? This action cannot be undone.', onConfirm: () => $wire.deleteRole({{ $role->id }}) })" class="size-8 rounded-lg hover:bg-red-50 flex items-center justify-center transition-colors cursor-pointer"><i data-lucide="trash-2" class="size-3.5 text-error"></i></button>
                    @endcan
                </div>
                @endif
            </div>
            <div class="flex flex-wrap gap-1">
                @foreach($role->permissions->take(6) as $perm)
                <span class="px-2 py-0.5 rounded-md bg-muted text-[10px] font-medium text-secondary">{{ $perm->name }}</span>
                @endforeach
                @if($role->permissions->count() > 6)
                <span class="px-2 py-0.5 rounded-md bg-primary/10 text-[10px] font-semibold text-primary">+{{ $role->permissions->count() - 6 }} more</span>
                @endif
                @if($role->name === 'Super Admin')
                <span class="px-2 py-0.5 rounded-md bg-green-50 text-[10px] font-semibold text-green-700">All Permissions</span>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>
