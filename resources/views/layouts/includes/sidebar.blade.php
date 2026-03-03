@php
    $currentRoute = request()->route()?->getName() ?? '';
    $currentPath = request()->path();
    $appLogo = \App\Models\Setting::getValue('app_logo');
    $appName = \App\Models\Setting::getValue('app_name', 'Helpdesk');
@endphp
<aside id="sidebar"
       :class="[
           sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
           sidebarCollapsed ? 'lg:w-[72px]' : 'lg:w-[280px]'
       ]"
       class="flex flex-col w-[280px] shrink-0 h-screen fixed inset-y-0 left-0 z-50 bg-white border-r border-border transform transition-all duration-300 overflow-hidden">
    <!-- Logo -->
    <div class="flex items-center justify-between border-b border-border h-[72px] px-4 gap-3">
      <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-3 min-w-0">
        @if($appLogo)
        <img src="{{ Storage::url($appLogo) }}" alt="{{ $appName }}" class="h-10 max-w-[40px] object-contain shrink-0">
        @else
        <div class="w-10 h-10 bg-primary rounded-xl flex items-center justify-center shadow-sm shrink-0">
          <i data-lucide="headset" class="w-5 h-5 text-white"></i>
        </div>
        @endif
        <h1 x-show="!sidebarCollapsed" x-transition:enter="transition ease-out duration-200 delay-100" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="font-bold text-xl tracking-tight text-primary whitespace-nowrap">{{ $appName }}</h1>
      </a>
      <button @click="sidebarOpen = false" class="lg:hidden size-10 flex shrink-0 bg-white rounded-xl items-center justify-center ring-1 ring-border hover:ring-primary transition-all duration-200 cursor-pointer">
        <i data-lucide="x" class="size-5 text-secondary"></i>
      </button>
    </div>

    <!-- Navigation -->
    <nav class="flex flex-col p-3 pb-28 gap-5 overflow-y-auto scrollbar-hide flex-1">
      <!-- Main -->
      <div class="flex flex-col gap-1">
        <h3 x-show="!sidebarCollapsed" class="font-semibold text-[10px] uppercase tracking-widest text-secondary/70 pl-3 mb-2">Main</h3>

        <a href="{{ route('dashboard') }}" wire:navigate title="Dashboard" class="group relative flex items-center rounded-xl px-3 py-2.5 gap-3 transition-all duration-200 {{ str_starts_with($currentPath, 'dashboard') ? 'bg-primary/10 text-primary' : 'text-secondary hover:bg-muted hover:text-foreground' }}" :class="sidebarCollapsed && 'justify-center'">
          <i data-lucide="layout-dashboard" class="size-[18px] shrink-0"></i>
          <span x-show="!sidebarCollapsed" class="font-medium text-sm whitespace-nowrap">Dashboard</span>
        </a>

        @can('tickets.view')
        <a href="{{ route('tickets.index') }}" wire:navigate title="Tickets" class="group relative flex items-center rounded-xl px-3 py-2.5 gap-3 transition-all duration-200 {{ str_starts_with($currentPath, 'tickets') ? 'bg-primary/10 text-primary' : 'text-secondary hover:bg-muted hover:text-foreground' }}" :class="sidebarCollapsed && 'justify-center'">
          <i data-lucide="ticket" class="size-[18px] shrink-0"></i>
          <span x-show="!sidebarCollapsed" class="font-medium text-sm whitespace-nowrap">Tickets</span>
        </a>
        @endcan

        @can('access_requests.view')
        <a href="{{ route('access-requests.index') }}" wire:navigate title="Access Requests" class="group relative flex items-center rounded-xl px-3 py-2.5 gap-3 transition-all duration-200 {{ str_starts_with($currentPath, 'access-requests') ? 'bg-primary/10 text-primary' : 'text-secondary hover:bg-muted hover:text-foreground' }}" :class="sidebarCollapsed && 'justify-center'">
          <i data-lucide="key-round" class="size-[18px] shrink-0"></i>
          <span x-show="!sidebarCollapsed" class="font-medium text-sm whitespace-nowrap">Access Requests</span>
        </a>
        @endcan

        @can('change_requests.view')
        <a href="{{ route('change-requests.index') }}" wire:navigate title="Change Requests" class="group relative flex items-center rounded-xl px-3 py-2.5 gap-3 transition-all duration-200 {{ str_starts_with($currentPath, 'change-requests') ? 'bg-primary/10 text-primary' : 'text-secondary hover:bg-muted hover:text-foreground' }}" :class="sidebarCollapsed && 'justify-center'">
          <i data-lucide="git-pull-request" class="size-[18px] shrink-0"></i>
          <span x-show="!sidebarCollapsed" class="font-medium text-sm whitespace-nowrap">Change Requests</span>
        </a>
        @endcan
      </div>

      <!-- Tasks -->
      @canany(['access_requests.approve', 'change_requests.approve', 'tickets.assign'])
      <div class="flex flex-col gap-1">
        <h3 x-show="!sidebarCollapsed" class="font-semibold text-[10px] uppercase tracking-widest text-secondary/70 pl-3 mb-2">Tasks</h3>

        <a href="{{ route('tasks.index') }}" wire:navigate title="My Tasks" class="group relative flex items-center rounded-xl px-3 py-2.5 gap-3 transition-all duration-200 {{ str_starts_with($currentPath, 'tasks') ? 'bg-primary/10 text-primary' : 'text-secondary hover:bg-muted hover:text-foreground' }}" :class="sidebarCollapsed && 'justify-center'">
          <i data-lucide="clipboard-check" class="size-[18px] shrink-0"></i>
          <span x-show="!sidebarCollapsed" class="font-medium text-sm whitespace-nowrap">My Tasks</span>
        </a>
      </div>
      @endcanany

      <!-- Reports -->
      @can('reports.view')
      <div class="flex flex-col gap-1">
        <h3 x-show="!sidebarCollapsed" class="font-semibold text-[10px] uppercase tracking-widest text-secondary/70 pl-3 mb-2">Reports</h3>

        <a href="{{ route('reports.dashboard') }}" wire:navigate title="Reports" class="group relative flex items-center rounded-xl px-3 py-2.5 gap-3 transition-all duration-200 {{ str_starts_with($currentPath, 'reports') ? 'bg-primary/10 text-primary' : 'text-secondary hover:bg-muted hover:text-foreground' }}" :class="sidebarCollapsed && 'justify-center'">
          <i data-lucide="bar-chart-3" class="size-[18px] shrink-0"></i>
          <span x-show="!sidebarCollapsed" class="font-medium text-sm whitespace-nowrap">Reports</span>
        </a>
      </div>
      @endcan

      <!-- Administration -->
      @canany(['users.view', 'roles.view', 'settings.view'])
      <div class="flex flex-col gap-1">
        <h3 x-show="!sidebarCollapsed" class="font-semibold text-[10px] uppercase tracking-widest text-secondary/70 pl-3 mb-2">Administration</h3>

        @can('users.view')
        <a href="{{ route('users.index') }}" wire:navigate title="Users" class="group relative flex items-center rounded-xl px-3 py-2.5 gap-3 transition-all duration-200 {{ str_starts_with($currentPath, 'users') ? 'bg-primary/10 text-primary' : 'text-secondary hover:bg-muted hover:text-foreground' }}" :class="sidebarCollapsed && 'justify-center'">
          <i data-lucide="users" class="size-[18px] shrink-0"></i>
          <span x-show="!sidebarCollapsed" class="font-medium text-sm whitespace-nowrap">Users</span>
        </a>
        @endcan

        @can('roles.view')
        <a href="{{ route('roles.index') }}" wire:navigate title="Roles & Permissions" class="group relative flex items-center rounded-xl px-3 py-2.5 gap-3 transition-all duration-200 {{ str_starts_with($currentPath, 'roles') ? 'bg-primary/10 text-primary' : 'text-secondary hover:bg-muted hover:text-foreground' }}" :class="sidebarCollapsed && 'justify-center'">
          <i data-lucide="shield" class="size-[18px] shrink-0"></i>
          <span x-show="!sidebarCollapsed" class="font-medium text-sm whitespace-nowrap">Roles & Permissions</span>
        </a>
        @endcan

        @can('settings.view')
        <a href="{{ route('settings.general') }}" wire:navigate title="Settings" class="group relative flex items-center rounded-xl px-3 py-2.5 gap-3 transition-all duration-200 {{ str_starts_with($currentPath, 'settings') ? 'bg-primary/10 text-primary' : 'text-secondary hover:bg-muted hover:text-foreground' }}" :class="sidebarCollapsed && 'justify-center'">
          <i data-lucide="settings" class="size-[18px] shrink-0"></i>
          <span x-show="!sidebarCollapsed" class="font-medium text-sm whitespace-nowrap">Settings</span>
        </a>
        @endcan
      </div>
      @endcanany
    </nav>

    <!-- User Profile -->
    <div class="p-3 border-t border-border bg-white">
      @auth
      <div class="flex items-center gap-3 p-3 rounded-2xl border border-border bg-gray-50/50" :class="sidebarCollapsed && 'justify-center p-2'">
        @if(auth()->user()->profile_photo)
        <img src="{{ Storage::url(auth()->user()->profile_photo) }}" alt="{{ auth()->user()->name }}" class="size-9 rounded-full object-cover shrink-0">
        @else
        <div class="size-9 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-sm shrink-0">
          {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
        </div>
        @endif
        <div x-show="!sidebarCollapsed" class="min-w-0 flex-1">
          <p class="font-semibold text-sm text-foreground truncate">{{ auth()->user()->name }}</p>
          <p class="text-xs text-secondary truncate">{{ auth()->user()->roles->first()?->name ?? 'User' }}</p>
        </div>
        <form x-show="!sidebarCollapsed" method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="text-secondary hover:text-error transition-colors duration-200 cursor-pointer" title="Logout">
            <i data-lucide="log-out" class="size-4"></i>
          </button>
        </form>
      </div>
      @endauth
    </div>
</aside>