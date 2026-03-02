@php
    $currentRoute = request()->route()?->getName() ?? '';
    $currentPath = request()->path();
@endphp
<aside id="sidebar"
       :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
       class="flex flex-col w-[280px] shrink-0 h-screen fixed inset-y-0 left-0 z-50 bg-white border-r border-border transform transition-transform duration-300 overflow-hidden">
    <!-- Logo -->
    <div class="flex items-center justify-between border-b border-border h-[72px] px-6 gap-3">
      <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
        <div class="w-10 h-10 bg-primary rounded-xl flex items-center justify-center shadow-sm">
          <i data-lucide="headset" class="w-5 h-5 text-white"></i>
        </div>
        <h1 class="font-bold text-xl tracking-tight text-primary">Helpdesk</h1>
      </a>
      <button @click="sidebarOpen = false" class="lg:hidden size-10 flex shrink-0 bg-white rounded-xl items-center justify-center ring-1 ring-border hover:ring-primary transition-all duration-200 cursor-pointer">
        <i data-lucide="x" class="size-5 text-secondary"></i>
      </button>
    </div>

    <!-- Navigation -->
    <nav class="flex flex-col p-4 pb-28 gap-6 overflow-y-auto scrollbar-hide flex-1">
      <!-- Main -->
      <div class="flex flex-col gap-1">
        <h3 class="font-semibold text-[10px] uppercase tracking-widest text-secondary/70 pl-3 mb-2">Main</h3>

        <a href="{{ route('dashboard') }}" class="group flex items-center rounded-xl px-3 py-2.5 gap-3 transition-all duration-200 {{ str_starts_with($currentPath, 'dashboard') ? 'bg-primary/10 text-primary' : 'text-secondary hover:bg-muted hover:text-foreground' }}">
          <i data-lucide="layout-dashboard" class="size-[18px]"></i>
          <span class="font-medium text-sm">Dashboard</span>
        </a>

        @can('tickets.view')
        <a href="{{ route('tickets.index') }}" class="group flex items-center rounded-xl px-3 py-2.5 gap-3 transition-all duration-200 {{ str_starts_with($currentPath, 'tickets') ? 'bg-primary/10 text-primary' : 'text-secondary hover:bg-muted hover:text-foreground' }}">
          <i data-lucide="ticket" class="size-[18px]"></i>
          <span class="font-medium text-sm">Tickets</span>
        </a>
        @endcan

        @can('access_requests.view')
        <a href="{{ route('access-requests.index') }}" class="group flex items-center rounded-xl px-3 py-2.5 gap-3 transition-all duration-200 {{ str_starts_with($currentPath, 'access-requests') ? 'bg-primary/10 text-primary' : 'text-secondary hover:bg-muted hover:text-foreground' }}">
          <i data-lucide="key-round" class="size-[18px]"></i>
          <span class="font-medium text-sm">Access Requests</span>
        </a>
        @endcan

        @can('change_requests.view')
        <a href="{{ route('change-requests.index') }}" class="group flex items-center rounded-xl px-3 py-2.5 gap-3 transition-all duration-200 {{ str_starts_with($currentPath, 'change-requests') ? 'bg-primary/10 text-primary' : 'text-secondary hover:bg-muted hover:text-foreground' }}">
          <i data-lucide="git-pull-request" class="size-[18px]"></i>
          <span class="font-medium text-sm">Change Requests</span>
        </a>
        @endcan
      </div>

      <!-- Tasks -->
      @canany(['access_requests.approve', 'change_requests.approve', 'tickets.assign'])
      <div class="flex flex-col gap-1">
        <h3 class="font-semibold text-[10px] uppercase tracking-widest text-secondary/70 pl-3 mb-2">Tasks</h3>

        <a href="{{ route('tasks.index') }}" class="group flex items-center rounded-xl px-3 py-2.5 gap-3 transition-all duration-200 {{ str_starts_with($currentPath, 'tasks') ? 'bg-primary/10 text-primary' : 'text-secondary hover:bg-muted hover:text-foreground' }}">
          <i data-lucide="clipboard-check" class="size-[18px]"></i>
          <span class="font-medium text-sm">My Tasks</span>
        </a>
      </div>
      @endcanany

      <!-- Reports -->
      @can('reports.view')
      <div class="flex flex-col gap-1">
        <h3 class="font-semibold text-[10px] uppercase tracking-widest text-secondary/70 pl-3 mb-2">Reports</h3>

        <a href="{{ route('reports.dashboard') }}" class="group flex items-center rounded-xl px-3 py-2.5 gap-3 transition-all duration-200 {{ str_starts_with($currentPath, 'reports') ? 'bg-primary/10 text-primary' : 'text-secondary hover:bg-muted hover:text-foreground' }}">
          <i data-lucide="bar-chart-3" class="size-[18px]"></i>
          <span class="font-medium text-sm">Reports</span>
        </a>
      </div>
      @endcan

      <!-- Administration -->
      @canany(['users.view', 'roles.view', 'settings.view'])
      <div class="flex flex-col gap-1">
        <h3 class="font-semibold text-[10px] uppercase tracking-widest text-secondary/70 pl-3 mb-2">Administration</h3>

        @can('users.view')
        <a href="{{ route('users.index') }}" class="group flex items-center rounded-xl px-3 py-2.5 gap-3 transition-all duration-200 {{ str_starts_with($currentPath, 'users') ? 'bg-primary/10 text-primary' : 'text-secondary hover:bg-muted hover:text-foreground' }}">
          <i data-lucide="users" class="size-[18px]"></i>
          <span class="font-medium text-sm">Users</span>
        </a>
        @endcan

        @can('roles.view')
        <a href="{{ route('roles.index') }}" class="group flex items-center rounded-xl px-3 py-2.5 gap-3 transition-all duration-200 {{ str_starts_with($currentPath, 'roles') ? 'bg-primary/10 text-primary' : 'text-secondary hover:bg-muted hover:text-foreground' }}">
          <i data-lucide="shield" class="size-[18px]"></i>
          <span class="font-medium text-sm">Roles & Permissions</span>
        </a>
        @endcan

        @can('settings.view')
        <a href="{{ route('settings.categories') }}" class="group flex items-center rounded-xl px-3 py-2.5 gap-3 transition-all duration-200 {{ str_starts_with($currentPath, 'settings') ? 'bg-primary/10 text-primary' : 'text-secondary hover:bg-muted hover:text-foreground' }}">
          <i data-lucide="settings" class="size-[18px]"></i>
          <span class="font-medium text-sm">Settings</span>
        </a>
        @endcan
      </div>
      @endcanany
    </nav>

    <!-- User Profile -->
    <div class="p-4 border-t border-border bg-white">
      @auth
      <div class="flex items-center gap-3 p-3 rounded-2xl border border-border bg-gray-50/50">
        <div class="size-9 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-sm">
          {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
        </div>
        <div class="min-w-0 flex-1">
          <p class="font-semibold text-sm text-foreground truncate">{{ auth()->user()->name }}</p>
          <p class="text-xs text-secondary truncate">{{ auth()->user()->roles->first()?->name ?? 'User' }}</p>
        </div>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="text-secondary hover:text-error transition-colors duration-200 cursor-pointer" title="Logout">
            <i data-lucide="log-out" class="size-4"></i>
          </button>
        </form>
      </div>
      @endauth
    </div>
</aside>