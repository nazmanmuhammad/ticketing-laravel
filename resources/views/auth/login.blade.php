<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - Helpdesk</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<style type="text/tailwindcss">
  :root { --primary: #165DFF; --primary-hover: #0E4BD9; --foreground: #080C1A; --secondary: #6A7686; --muted: #EFF2F7; --border: #E5E7EB; --error: #ED6B60; --font-sans: 'Inter', sans-serif; }
  @theme inline { --color-primary: var(--primary); --color-primary-hover: var(--primary-hover); --color-foreground: var(--foreground); --color-secondary: var(--secondary); --color-muted: var(--muted); --color-border: var(--border); --color-error: var(--error); --font-sans: var(--font-sans); }
</style>
</head>
<body class="font-sans bg-muted min-h-screen flex items-center justify-center p-4">
<div x-data="{ show: false }" x-init="setTimeout(() => show = true, 100)" class="w-full max-w-md">
    <div x-show="show" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-8 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100" class="bg-white rounded-3xl shadow-xl border border-border p-8">
        <div class="text-center mb-8">
            <div class="w-14 h-14 bg-primary rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-primary/20">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
            <h1 class="text-2xl font-bold text-foreground">Welcome Back</h1>
            <p class="text-secondary text-sm mt-1">Sign in to your helpdesk account</p>
        </div>

        @if ($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm" x-data="{ show: true }" x-show="show" x-transition>
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-5" x-data="{ submitting: false }" @submit="submitting = true">
            @csrf
            <div>
                <label for="email" class="block text-sm font-medium text-foreground mb-1.5">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                       class="w-full h-12 px-4 rounded-xl border border-border bg-white text-sm font-medium focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all duration-200"
                       placeholder="you@company.com">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-foreground mb-1.5">Password</label>
                <input id="password" type="password" name="password" required
                       class="w-full h-12 px-4 rounded-xl border border-border bg-white text-sm font-medium focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all duration-200"
                       placeholder="••••••••">
            </div>
            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded border-border text-primary focus:ring-primary">
                    <span class="text-sm text-secondary">Remember me</span>
                </label>
            </div>
            <button type="submit" :disabled="submitting" class="w-full h-12 bg-primary hover:bg-primary-hover text-white rounded-xl font-semibold shadow-lg shadow-primary/20 hover:shadow-primary/40 transition-all duration-200 hover:scale-[1.02] active:scale-95 cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed disabled:hover:scale-100 flex items-center justify-center gap-2">
                <svg x-show="submitting" class="animate-spin size-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                <span x-text="submitting ? 'Signing in...' : 'Sign In'"></span>
            </button>
        </form>
        <p class="text-center text-sm text-secondary mt-6">
            Don't have an account? <a href="{{ route('register') }}" class="text-primary font-semibold hover:underline">Register</a>
        </p>
    </div>
</div>
</body>
</html>
