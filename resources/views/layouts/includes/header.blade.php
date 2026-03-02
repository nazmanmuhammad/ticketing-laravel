<div class="sticky top-0 z-30 flex items-center justify-between w-full h-[72px] shrink-0 border-b border-border bg-white/80 backdrop-blur-md px-5 md:px-8">
    <div class="flex items-center gap-4">
        <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden size-10 flex items-center justify-center rounded-xl ring-1 ring-border hover:ring-primary transition-all duration-200 cursor-pointer">
            <i data-lucide="menu" class="size-5 text-foreground"></i>
        </button>
        <div>
            <h2 class="font-bold text-xl text-foreground">@yield('page-title', 'Dashboard')</h2>
            <p class="hidden sm:block text-xs text-secondary">@yield('page-description', '')</p>
        </div>
    </div>

    <div class="flex items-center gap-2">
        @auth
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" class="flex items-center gap-2 px-3 py-2 rounded-xl hover:bg-muted transition-all duration-200 cursor-pointer">
                <div class="size-8 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-xs">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <span class="hidden md:block text-sm font-medium text-foreground">{{ auth()->user()->name }}</span>
                <i data-lucide="chevron-down" class="size-4 text-secondary"></i>
            </button>
            <div x-show="open" @click.away="open = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                 class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-border py-2 z-50">
                <a href="{{ route('profile') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-secondary hover:text-foreground hover:bg-muted transition-colors duration-150">
                    <i data-lucide="user" class="size-4"></i>
                    Profile
                </a>
                <hr class="my-1 border-border">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-error hover:bg-error-light transition-colors duration-150 cursor-pointer">
                        <i data-lucide="log-out" class="size-4"></i>
                        Logout
                    </button>
                </form>
            </div>
        </div>
        @endauth
    </div>
</div>