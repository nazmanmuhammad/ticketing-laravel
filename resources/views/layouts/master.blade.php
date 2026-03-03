<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
@php $__appName = \App\Models\Setting::getValue('app_name', config('app.name', 'Helpdesk')); $__favicon = \App\Models\Setting::getValue('app_favicon'); @endphp
<title>@yield('title', 'Helpdesk') - {{ $__appName }}</title>
@if($__favicon)<link rel="icon" href="{{ Storage::url($__favicon) }}" type="image/png">@endif
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js" onload="window.lucideLoaded=true; if(window.initLucide) window.initLucide();"></script>
<script>
  window.initLucide = function() { if(window.lucide) lucide.createIcons(); };
  document.addEventListener('DOMContentLoaded', function() { if(window.lucideLoaded) window.initLucide(); });
  document.addEventListener('livewire:navigated', function() { if(window.lucide) lucide.createIcons(); });
</script>
<style type="text/tailwindcss">
  :root {
    --primary: #165DFF;
    --primary-hover: #0E4BD9;
    --foreground: #080C1A;
    --secondary: #6A7686;
    --muted: #EFF2F7;
    --border: #E5E7EB;
    --card-grey: #F1F3F6;
    --success: #30B22D;
    --success-light: #DCFCE7;
    --error: #ED6B60;
    --error-light: #FEE2E2;
    --warning: #F59E0B;
    --warning-light: #FEF9C3;
    --info: #3B82F6;
    --info-light: #DBEAFE;
    --font-sans: 'Inter', sans-serif;
  }
  @theme inline {
    --color-primary: var(--primary);
    --color-primary-hover: var(--primary-hover);
    --color-foreground: var(--foreground);
    --color-secondary: var(--secondary);
    --color-muted: var(--muted);
    --color-border: var(--border);
    --color-card-grey: var(--card-grey);
    --color-success: var(--success);
    --color-success-light: var(--success-light);
    --color-error: var(--error);
    --color-error-light: var(--error-light);
    --color-warning: var(--warning);
    --color-warning-light: var(--warning-light);
    --color-info: var(--info);
    --color-info-light: var(--info-light);
    --font-sans: var(--font-sans);
    --radius-card: 24px;
    --radius-button: 50px;
  }
  select {
    @apply appearance-none bg-no-repeat cursor-pointer;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%236B7280' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
    background-position: right 10px center;
    padding-right: 40px;
  }
  .scrollbar-hide::-webkit-scrollbar { display: none; }
  .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@livewireStyles
@yield('styles')
</head>
<body class="font-sans bg-white min-h-screen overflow-x-hidden text-foreground" x-data="{ sidebarOpen: false, sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true' }" @toggle-sidebar.window="sidebarCollapsed = !sidebarCollapsed; localStorage.setItem('sidebarCollapsed', sidebarCollapsed)">

<!-- Navigation Loading -->
<div x-data="{ navigating: false, progress: 0 }"
     x-init="
        document.addEventListener('livewire:navigate-start', () => { navigating = true; progress = 0; let i = setInterval(() => { progress = Math.min(progress + Math.random() * 15, 90); }, 150); $el._interval = i; });
        document.addEventListener('livewire:navigate-end', () => { progress = 100; navigating = false; clearInterval($el._interval); setTimeout(() => { progress = 0; }, 300); if(window.lucide) lucide.createIcons(); });
     ">
    <!-- Loading Bar -->
    <div x-show="progress > 0"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed top-0 left-0 right-0 z-[999] h-1">
        <div class="h-full bg-gradient-to-r from-primary to-blue-400 rounded-r-full transition-all duration-200 ease-out shadow-[0_0_15px_rgba(22,93,255,0.6)]" :style="'width: ' + progress + '%'"></div>
    </div>
    
    <!-- Loading Overlay -->
    <div x-show="navigating"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-white/60 backdrop-blur-sm z-[998] flex items-center justify-center">
        <div class="flex flex-col items-center gap-3">
            <div class="size-12 border-4 border-primary/20 border-t-primary rounded-full animate-spin"></div>
            <p class="text-sm font-medium text-secondary">Loading...</p>
        </div>
    </div>
</div>

<!-- Toast Notifications -->
<div x-data="toastNotification()" @toast.window="addToast($event.detail)" class="fixed top-5 right-5 z-[200] flex flex-col gap-3 pointer-events-none">
    <template x-for="toast in toasts" :key="toast.id">
        <div x-show="toast.visible"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-x-8"
             x-transition:enter-end="opacity-100 translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-x-0"
             x-transition:leave-end="opacity-0 translate-x-8"
             class="pointer-events-auto flex items-center gap-3 px-5 py-3 rounded-xl shadow-lg border min-w-[320px]"
             :class="{
                'bg-white border-green-200 text-green-800': toast.type === 'success',
                'bg-white border-red-200 text-red-800': toast.type === 'error',
                'bg-white border-blue-200 text-blue-800': toast.type === 'info',
                'bg-white border-yellow-200 text-yellow-800': toast.type === 'warning'
             }">
            <span x-text="toast.message" class="text-sm font-medium"></span>
            <button @click="removeToast(toast.id)" class="ml-auto text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="size-4"></i>
            </button>
        </div>
    </template>
</div>

<!-- Mobile Overlay -->
<div x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="sidebarOpen = false" class="fixed inset-0 bg-black/60 z-40 lg:hidden"></div>

<div class="flex h-screen max-h-screen flex-1 bg-muted overflow-hidden">
  <!-- SIDEBAR -->
  @include('layouts.includes.sidebar')

  <!-- MAIN CONTENT -->
  <main :class="sidebarCollapsed ? 'lg:ml-[72px]' : 'lg:ml-[280px]'" class="flex-1 flex flex-col min-h-screen overflow-x-hidden relative transition-all duration-300">
    <!-- Top Header Bar -->
    @include('layouts.includes.header')

    <!-- Page Content -->
    <div class="flex-1 p-5 md:p-8 overflow-y-auto">
      @if (trim($slot ?? '') !== '')
        {{ $slot }}
      @else
        @yield('content')
      @endif
    </div>
  </main>
</div>

<!-- Confirmation Dialog -->
<div x-data="confirmDialog()" @confirm-dialog.window="open($event.detail)" x-show="show" x-cloak class="fixed inset-0 z-[300] flex items-center justify-center p-4" style="display:none">
    <div x-show="show" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="cancel()" class="absolute inset-0 bg-black/50"></div>
    <div x-show="show" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="relative bg-white rounded-2xl shadow-2xl border border-border max-w-sm w-full p-6">
        <div class="flex items-center gap-3 mb-4">
            <div class="size-10 rounded-xl bg-red-50 flex items-center justify-center shrink-0">
                <i data-lucide="alert-triangle" class="size-5 text-red-500"></i>
            </div>
            <div>
                <h3 class="font-bold text-foreground" x-text="title"></h3>
                <p class="text-sm text-secondary mt-0.5" x-text="message"></p>
            </div>
        </div>
        <div class="flex items-center justify-end gap-2 mt-6">
            <button @click="cancel()" class="h-10 px-5 rounded-xl border border-border text-secondary font-semibold text-sm hover:bg-muted transition-all duration-200 cursor-pointer">Cancel</button>
            <button @click="confirm()" class="h-10 px-5 bg-red-600 hover:bg-red-700 text-white rounded-xl font-semibold text-sm transition-all duration-200 active:scale-95 cursor-pointer flex items-center gap-2">
                <span x-text="confirmText"></span>
            </button>
        </div>
    </div>
</div>

@livewireScripts
<script>
function toastNotification() {
    return {
        toasts: [],
        addToast(detail) {
            const id = Date.now();
            this.toasts.push({ id, message: detail.message, type: detail.type || 'info', visible: true });
            setTimeout(() => this.removeToast(id), 3000);
        },
        removeToast(id) {
            const t = this.toasts.find(t => t.id === id);
            if (t) t.visible = false;
            setTimeout(() => { this.toasts = this.toasts.filter(t => t.id !== id); }, 300);
        }
    };
}

function confirmDialog() {
    return {
        show: false,
        title: '',
        message: '',
        confirmText: 'Delete',
        onConfirm: null,
        open(detail) {
            this.title = detail.title || 'Confirm Delete';
            this.message = detail.message || 'Are you sure? This action cannot be undone.';
            this.confirmText = detail.confirmText || 'Delete';
            this.onConfirm = detail.onConfirm || null;
            this.show = true;
            this.$nextTick(() => { if(window.lucide) lucide.createIcons(); });
        },
        confirm() {
            if (this.onConfirm) this.onConfirm();
            this.show = false;
        },
        cancel() {
            this.show = false;
        }
    };
}

document.addEventListener('livewire:init', () => {
    Livewire.hook('morph.updated', ({ el, component }) => {
        if(window.lucide) lucide.createIcons();
    });
});
</script>
@yield('scripts')
</body>
</html>