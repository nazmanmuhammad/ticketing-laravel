<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
@php
    $appName = \App\Models\Setting::getValue('app_name', config('app.name', 'Helpdesk'));
    $appTitle = \App\Models\Setting::getValue('app_title', 'IT Helpdesk System');
    $appDesc = \App\Models\Setting::getValue('app_description', 'Streamline your workflows, ensure compliance, and accelerate results.');
    $appLogo = \App\Models\Setting::getValue('app_logo');
    $appFavicon = \App\Models\Setting::getValue('app_favicon');
@endphp
<title>Sign In - {{ $appName }}</title>
@if($appFavicon)<link rel="icon" href="{{ Storage::url($appFavicon) }}" type="image/png">@endif
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<style type="text/tailwindcss">
  :root { --primary: #165DFF; --primary-hover: #0E4BD9; --foreground: #080C1A; --secondary: #6A7686; --muted: #EFF2F7; --border: #E5E7EB; --error: #ED6B60; --font-sans: 'Inter', sans-serif; }
  @theme inline { --color-primary: var(--primary); --color-primary-hover: var(--primary-hover); --color-foreground: var(--foreground); --color-secondary: var(--secondary); --color-muted: var(--muted); --color-border: var(--border); --color-error: var(--error); --font-sans: var(--font-sans); }
</style>
</head>
<body class="font-sans bg-white min-h-screen">
<div class="flex min-h-screen" x-data="{ show: false, showPw: false, submitting: false }" x-init="setTimeout(() => show = true, 100)">

    <!-- Left Panel - Branding -->
    <div class="hidden lg:flex lg:w-[480px] xl:w-[520px] shrink-0 relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-primary via-blue-600 to-indigo-700 rounded-3xl m-4">
            <!-- Decorative dots -->
            <div class="absolute top-8 right-8 grid grid-cols-6 gap-2 opacity-30">
                @for($i = 0; $i < 24; $i++)
                <div class="size-1.5 rounded-full bg-white"></div>
                @endfor
            </div>

            <!-- Decorative circles -->
            <svg class="absolute bottom-0 right-0 w-72 h-72 opacity-20" viewBox="0 0 300 300">
                <circle cx="200" cy="200" r="120" fill="none" stroke="#8B5CF6" stroke-width="40"/>
                <circle cx="150" cy="180" r="90" fill="none" stroke="#059669" stroke-width="35" opacity="0.7"/>
                <circle cx="220" cy="140" r="60" fill="none" stroke="#DC2626" stroke-width="30" opacity="0.5"/>
            </svg>

            <!-- Content -->
            <div class="relative z-10 flex flex-col justify-between h-full p-10">
                <!-- Logo -->
                <div class="flex items-center gap-3">
                    @if($appLogo)
                    <img src="{{ Storage::url($appLogo) }}" alt="{{ $appName }}" class="h-10 object-contain brightness-0 invert">
                    @else
                    <div class="size-10 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                        <svg class="size-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                    <span class="text-white font-bold text-lg">{{ $appName }}</span>
                    @endif
                </div>

                <!-- Title & Description -->
                <div class="mb-24">
                    <h2 class="text-white text-3xl xl:text-4xl font-bold leading-tight">{{ $appName }}</h2>
                    <p class="text-blue-200 text-lg font-medium mt-1">{{ $appTitle }}</p>
                    <p class="text-blue-100/70 text-sm mt-4 leading-relaxed max-w-sm">{{ $appDesc }}</p>
                </div>

                <!-- Footer -->
                <p class="text-blue-200/60 text-xs">&copy; {{ date('Y') }} {{ $appName }}. All rights reserved.</p>
            </div>
        </div>
    </div>

    <!-- Right Panel - Login Form -->
    <div class="flex-1 flex items-center justify-center p-6 sm:p-10">
        <div x-show="show" x-transition:enter="transition ease-out duration-600" x-transition:enter-start="opacity-0 translate-y-6" x-transition:enter-end="opacity-100 translate-y-0" class="w-full max-w-md">

            <!-- Mobile Logo -->
            <div class="lg:hidden flex items-center justify-center gap-3 mb-8">
                @if($appLogo)
                <img src="{{ Storage::url($appLogo) }}" alt="{{ $appName }}" class="h-12 object-contain">
                @else
                <div class="size-12 bg-primary rounded-2xl flex items-center justify-center shadow-lg shadow-primary/20">
                    <svg class="size-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
                @endif
            </div>

            <div class="mb-8">
                <h1 class="text-3xl font-bold text-foreground">Sign In</h1>
                <p class="text-secondary text-sm mt-2">Please enter your credentials to continue</p>
            </div>

            @if ($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm" x-data="{ s: true }" x-show="s" x-transition>
                <div class="flex items-center gap-2">
                    <svg class="size-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                    <div>
                        @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5" @submit="submitting = true">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-semibold text-foreground mb-2">Email address<span class="text-error">*</span></label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                           class="w-full h-12 px-4 rounded-xl border border-border bg-white text-sm font-medium focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all duration-200"
                           placeholder="you@company.com">
                </div>
                <div>
                    <label for="password" class="block text-sm font-semibold text-foreground mb-2">Password<span class="text-error">*</span></label>
                    <div class="relative">
                        <input id="password" :type="showPw ? 'text' : 'password'" name="password" required
                               class="w-full h-12 px-4 pr-12 rounded-xl border border-border bg-white text-sm font-medium focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all duration-200"
                               placeholder="Enter your password">
                        <button type="button" @click="showPw = !showPw" class="absolute right-4 top-1/2 -translate-y-1/2 text-secondary hover:text-foreground transition-colors cursor-pointer">
                            <svg x-show="!showPw" class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg x-show="showPw" class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-border text-primary focus:ring-primary cursor-pointer">
                        <span class="text-sm text-secondary">Remember me</span>
                    </label>
                </div>

                <button type="submit" :disabled="submitting" class="w-full h-12 bg-primary hover:bg-primary-hover text-white rounded-full font-semibold shadow-lg shadow-primary/20 hover:shadow-primary/30 transition-all duration-200 hover:scale-[1.01] active:scale-[0.98] cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed disabled:hover:scale-100 flex items-center justify-center gap-2">
                    <svg x-show="submitting" class="animate-spin size-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    <span x-text="submitting ? 'Signing in...' : 'Sign in'"></span>
                </button>
            </form>

            <p class="text-center text-sm text-secondary mt-6">
                Don't have an account? <a href="{{ route('register') }}" class="text-primary font-semibold hover:underline">Register</a>
            </p>
        </div>
    </div>

</div>
</body>
</html>
