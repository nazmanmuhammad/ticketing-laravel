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
<title>{{ $appName }} - {{ $appTitle }}</title>
@if($appFavicon)<link rel="icon" href="{{ Storage::url($appFavicon) }}" type="image/png">@endif
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
<style type="text/tailwindcss">
  :root {
    --primary: #165DFF;
    --primary-hover: #0E4BD9;
    --foreground: #080C1A;
    --secondary: #6A7686;
    --muted: #EFF2F7;
    --border: #E5E7EB;
    --success: #30B22D;
    --error: #ED6B60;
    --warning: #F59E0B;
    --info: #3B82F6;
    --font-sans: 'Inter', sans-serif;
  }
  @theme inline {
    --color-primary: var(--primary);
    --color-primary-hover: var(--primary-hover);
    --color-foreground: var(--foreground);
    --color-secondary: var(--secondary);
    --color-muted: var(--muted);
    --color-border: var(--border);
    --color-success: var(--success);
    --color-error: var(--error);
    --color-warning: var(--warning);
    --color-info: var(--info);
    --font-sans: var(--font-sans);
  }
  .gradient-text {
    background: linear-gradient(135deg, #165DFF 0%, #8B5CF6 50%, #06B6D4 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
  }
  .hero-gradient {
    background: radial-gradient(ellipse at 30% 0%, rgba(22,93,255,0.08) 0%, transparent 60%),
                radial-gradient(ellipse at 80% 100%, rgba(139,92,246,0.06) 0%, transparent 50%);
  }
  .float-animation { animation: float 6s ease-in-out infinite; }
  .float-animation-delay { animation: float 6s ease-in-out 2s infinite; }
  @keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-12px); }
  }
</style>
</head>
<body class="font-sans bg-white text-foreground antialiased" x-data="{ mobileMenu: false }">

<!-- Navbar -->
<nav class="fixed top-0 left-0 right-0 z-50 bg-white/80 backdrop-blur-xl border-b border-border/50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-[72px]">
            <!-- Logo -->
            <a href="{{ route('landing') }}" class="flex items-center gap-3">
                @if($appLogo)
                <img src="{{ Storage::url($appLogo) }}" alt="{{ $appName }}" class="h-9 object-contain">
                @else
                <div class="size-10 bg-primary rounded-xl flex items-center justify-center shadow-sm">
                    <i data-lucide="headset" class="size-5 text-white"></i>
                </div>
                <span class="font-bold text-xl text-foreground">{{ $appName }}</span>
                @endif
            </a>

            <!-- Desktop Nav -->
            <div class="hidden md:flex items-center gap-8">
                <a href="#features" class="text-sm font-medium text-secondary hover:text-foreground transition-colors">Features</a>
                <a href="#how-it-works" class="text-sm font-medium text-secondary hover:text-foreground transition-colors">How It Works</a>
                <a href="#modules" class="text-sm font-medium text-secondary hover:text-foreground transition-colors">Modules</a>
            </div>

            <!-- Auth Buttons -->
            <div class="hidden md:flex items-center gap-3">
                <a href="{{ route('login') }}" class="h-10 px-5 inline-flex items-center justify-center rounded-full text-sm font-semibold text-secondary hover:text-foreground border border-border hover:border-primary/30 transition-all duration-200">Sign In</a>
                <a href="{{ route('register') }}" class="h-10 px-5 inline-flex items-center justify-center rounded-full text-sm font-semibold text-white bg-primary hover:bg-primary-hover shadow-lg shadow-primary/20 hover:shadow-primary/30 transition-all duration-200 hover:scale-[1.02] active:scale-95">Get Started</a>
            </div>

            <!-- Mobile Menu Toggle -->
            <button @click="mobileMenu = !mobileMenu" class="md:hidden size-10 flex items-center justify-center rounded-xl ring-1 ring-border cursor-pointer">
                <i x-show="!mobileMenu" data-lucide="menu" class="size-5"></i>
                <i x-show="mobileMenu" data-lucide="x" class="size-5"></i>
            </button>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div x-show="mobileMenu" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" class="md:hidden bg-white border-t border-border px-4 pb-5 pt-3 space-y-3">
        <a href="#features" @click="mobileMenu = false" class="block py-2 text-sm font-medium text-secondary hover:text-foreground">Features</a>
        <a href="#how-it-works" @click="mobileMenu = false" class="block py-2 text-sm font-medium text-secondary hover:text-foreground">How It Works</a>
        <a href="#modules" @click="mobileMenu = false" class="block py-2 text-sm font-medium text-secondary hover:text-foreground">Modules</a>
        <hr class="border-border">
        <div class="flex gap-3 pt-1">
            <a href="{{ route('login') }}" class="flex-1 h-10 inline-flex items-center justify-center rounded-full text-sm font-semibold border border-border text-secondary">Sign In</a>
            <a href="{{ route('register') }}" class="flex-1 h-10 inline-flex items-center justify-center rounded-full text-sm font-semibold text-white bg-primary shadow-lg shadow-primary/20">Get Started</a>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section class="hero-gradient pt-32 pb-20 lg:pt-40 lg:pb-28 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
            <!-- Left Content -->
            <div class="text-center lg:text-left" x-data="{ show: false }" x-init="setTimeout(() => show = true, 100)">
                <div x-show="show" x-transition:enter="transition ease-out duration-700" x-transition:enter-start="opacity-0 translate-y-6" x-transition:enter-end="opacity-100 translate-y-0">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-primary/5 border border-primary/10 mb-6">
                        <div class="size-2 rounded-full bg-success animate-pulse"></div>
                        <span class="text-xs font-semibold text-primary">{{ $appTitle }}</span>
                    </div>

                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold leading-[1.1] tracking-tight text-foreground mb-6">
                        Simplify Your
                        <span class="gradient-text">IT Operations</span>
                        with Ease
                    </h1>

                    <p class="text-lg text-secondary leading-relaxed max-w-xl mx-auto lg:mx-0 mb-8">
                        {{ $appDesc }}
                    </p>

                    <div class="flex flex-col sm:flex-row gap-3 justify-center lg:justify-start">
                        <a href="{{ route('register') }}" class="h-12 px-8 inline-flex items-center justify-center rounded-full text-sm font-bold text-white bg-primary hover:bg-primary-hover shadow-xl shadow-primary/25 hover:shadow-primary/40 transition-all duration-300 hover:scale-[1.03] active:scale-95 gap-2">
                            Get Started Free
                            <i data-lucide="arrow-right" class="size-4"></i>
                        </a>
                        <a href="#features" class="h-12 px-8 inline-flex items-center justify-center rounded-full text-sm font-bold text-foreground bg-white border border-border hover:border-primary/30 hover:bg-muted transition-all duration-200 gap-2">
                            <i data-lucide="play-circle" class="size-4 text-primary"></i>
                            Learn More
                        </a>
                    </div>

                    <!-- Stats -->
                    <div class="flex items-center gap-8 mt-10 justify-center lg:justify-start">
                        <div>
                            <p class="text-2xl font-extrabold text-foreground">99.9%</p>
                            <p class="text-xs text-secondary font-medium">Uptime SLA</p>
                        </div>
                        <div class="w-px h-10 bg-border"></div>
                        <div>
                            <p class="text-2xl font-extrabold text-foreground">50%</p>
                            <p class="text-xs text-secondary font-medium">Faster Resolution</p>
                        </div>
                        <div class="w-px h-10 bg-border"></div>
                        <div>
                            <p class="text-2xl font-extrabold text-foreground">24/7</p>
                            <p class="text-xs text-secondary font-medium">Monitoring</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right - Dashboard Preview -->
            <div class="hidden lg:block relative" x-data="{ show: false }" x-init="setTimeout(() => show = true, 400)">
                <div x-show="show" x-transition:enter="transition ease-out duration-700" x-transition:enter-start="opacity-0 translate-x-12" x-transition:enter-end="opacity-100 translate-x-0">
                    <!-- Main Card -->
                    <div class="bg-white rounded-3xl shadow-2xl shadow-black/8 border border-border/60 p-6 float-animation">
                        <!-- Mini Dashboard -->
                        <div class="grid grid-cols-3 gap-3 mb-4">
                            <div class="bg-blue-50 rounded-2xl p-4">
                                <div class="size-8 bg-primary/10 rounded-lg flex items-center justify-center mb-2"><i data-lucide="ticket" class="size-4 text-primary"></i></div>
                                <p class="text-lg font-bold text-foreground">142</p>
                                <p class="text-xs text-secondary">Open Tickets</p>
                            </div>
                            <div class="bg-green-50 rounded-2xl p-4">
                                <div class="size-8 bg-success/10 rounded-lg flex items-center justify-center mb-2"><i data-lucide="check-circle" class="size-4 text-success"></i></div>
                                <p class="text-lg font-bold text-foreground">89%</p>
                                <p class="text-xs text-secondary">SLA Met</p>
                            </div>
                            <div class="bg-purple-50 rounded-2xl p-4">
                                <div class="size-8 bg-purple-500/10 rounded-lg flex items-center justify-center mb-2"><i data-lucide="users" class="size-4 text-purple-600"></i></div>
                                <p class="text-lg font-bold text-foreground">38</p>
                                <p class="text-xs text-secondary">Active Agents</p>
                            </div>
                        </div>
                        <!-- Chart Placeholder -->
                        <div class="bg-muted/50 rounded-2xl p-4">
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-sm font-semibold text-foreground">Ticket Trend</p>
                                <span class="text-xs text-secondary">Last 7 days</span>
                            </div>
                            <svg viewBox="0 0 400 80" class="w-full h-16">
                                <defs>
                                    <linearGradient id="chartGrad" x1="0%" y1="0%" x2="0%" y2="100%">
                                        <stop offset="0%" style="stop-color:#165DFF;stop-opacity:0.2" />
                                        <stop offset="100%" style="stop-color:#165DFF;stop-opacity:0" />
                                    </linearGradient>
                                </defs>
                                <path d="M0,60 Q50,50 100,40 T200,25 T300,35 T400,15 V80 H0Z" fill="url(#chartGrad)"/>
                                <path d="M0,60 Q50,50 100,40 T200,25 T300,35 T400,15" fill="none" stroke="#165DFF" stroke-width="2.5" stroke-linecap="round"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Floating Notification Card -->
                    <div class="absolute -left-8 bottom-12 bg-white rounded-2xl shadow-xl border border-border/60 p-4 w-56 float-animation-delay">
                        <div class="flex items-center gap-3">
                            <div class="size-9 bg-success/10 rounded-full flex items-center justify-center shrink-0">
                                <i data-lucide="check" class="size-4 text-success"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-foreground truncate">Ticket Resolved</p>
                                <p class="text-[10px] text-secondary">TK-1024 closed by agent</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section id="features" class="py-20 lg:py-28 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-primary/5 border border-primary/10 mb-4">
                <span class="text-xs font-semibold text-primary">Why Choose Us</span>
            </div>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-foreground mb-4">Everything You Need to <span class="gradient-text">Deliver Excellence</span></h2>
            <p class="text-secondary text-lg max-w-2xl mx-auto">A comprehensive platform designed for IT teams who demand speed, visibility, and compliance.</p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @php
            $features = [
                ['icon' => 'zap', 'color' => 'blue', 'title' => 'Fast Ticket Resolution', 'desc' => 'Smart routing and SLA tracking ensures tickets are resolved faster with automated escalation rules.'],
                ['icon' => 'shield-check', 'color' => 'green', 'title' => 'Approval Workflows', 'desc' => 'Multi-level sequential approval workflows for access requests, change requests, and more.'],
                ['icon' => 'bar-chart-3', 'color' => 'purple', 'title' => 'Real-time Analytics', 'desc' => 'Interactive dashboards and reports give you complete visibility into your IT operations.'],
                ['icon' => 'key-round', 'color' => 'orange', 'title' => 'Access Management', 'desc' => 'Streamlined access request process with audit trails and compliance tracking.'],
                ['icon' => 'git-pull-request', 'color' => 'cyan', 'title' => 'Change Management', 'desc' => 'Structured change request process with risk assessment, scheduling, and implementation tracking.'],
                ['icon' => 'bell-ring', 'color' => 'rose', 'title' => 'Smart Notifications', 'desc' => 'Stay informed with real-time email notifications and in-app alerts for every important event.'],
            ];
            $colorMap = [
                'blue' => ['bg' => 'bg-blue-50', 'icon' => 'text-blue-600', 'ring' => 'group-hover:ring-blue-200'],
                'green' => ['bg' => 'bg-green-50', 'icon' => 'text-green-600', 'ring' => 'group-hover:ring-green-200'],
                'purple' => ['bg' => 'bg-purple-50', 'icon' => 'text-purple-600', 'ring' => 'group-hover:ring-purple-200'],
                'orange' => ['bg' => 'bg-orange-50', 'icon' => 'text-orange-600', 'ring' => 'group-hover:ring-orange-200'],
                'cyan' => ['bg' => 'bg-cyan-50', 'icon' => 'text-cyan-600', 'ring' => 'group-hover:ring-cyan-200'],
                'rose' => ['bg' => 'bg-rose-50', 'icon' => 'text-rose-600', 'ring' => 'group-hover:ring-rose-200'],
            ];
            @endphp
            @foreach($features as $f)
            @php $c = $colorMap[$f['color']]; @endphp
            <div class="group bg-white rounded-2xl border border-border p-6 hover:shadow-xl hover:shadow-black/5 hover:-translate-y-1 transition-all duration-300">
                <div class="size-12 {{ $c['bg'] }} rounded-2xl flex items-center justify-center mb-4 ring-1 ring-transparent {{ $c['ring'] }} transition-all duration-300">
                    <i data-lucide="{{ $f['icon'] }}" class="size-6 {{ $c['icon'] }}"></i>
                </div>
                <h3 class="font-bold text-foreground mb-2">{{ $f['title'] }}</h3>
                <p class="text-sm text-secondary leading-relaxed">{{ $f['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- How It Works -->
<section id="how-it-works" class="py-20 lg:py-28 bg-muted/50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-primary/5 border border-primary/10 mb-4">
                <span class="text-xs font-semibold text-primary">Simple Process</span>
            </div>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-foreground mb-4">How It <span class="gradient-text">Works</span></h2>
            <p class="text-secondary text-lg max-w-2xl mx-auto">Get up and running in minutes with our intuitive workflow.</p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-8">
            @php
            $steps = [
                ['num' => '01', 'icon' => 'file-plus', 'title' => 'Submit Request', 'desc' => 'Users submit tickets, access, or change requests via the self-service portal.'],
                ['num' => '02', 'icon' => 'route', 'title' => 'Auto Routing', 'desc' => 'Requests are automatically routed to the right team based on category and priority.'],
                ['num' => '03', 'icon' => 'clipboard-check', 'title' => 'Review & Approve', 'desc' => 'Approvers are notified and can review, approve, or reject through a simple interface.'],
                ['num' => '04', 'icon' => 'check-circle-2', 'title' => 'Resolve & Close', 'desc' => 'Issues are resolved, tracked, and closed with full audit trail and SLA metrics.'],
            ];
            @endphp
            @foreach($steps as $i => $s)
            <div class="relative text-center">
                <div class="inline-flex items-center justify-center size-16 rounded-2xl bg-white shadow-lg shadow-black/5 border border-border mb-5 mx-auto relative">
                    <i data-lucide="{{ $s['icon'] }}" class="size-7 text-primary"></i>
                    <span class="absolute -top-2 -right-2 size-6 rounded-full bg-primary text-white text-[10px] font-bold flex items-center justify-center">{{ $s['num'] }}</span>
                </div>
                <h3 class="font-bold text-foreground mb-2">{{ $s['title'] }}</h3>
                <p class="text-sm text-secondary leading-relaxed">{{ $s['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Modules Section -->
<section id="modules" class="py-20 lg:py-28 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-primary/5 border border-primary/10 mb-4">
                <span class="text-xs font-semibold text-primary">Modules</span>
            </div>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-foreground mb-4">Powerful <span class="gradient-text">Built-in Modules</span></h2>
            <p class="text-secondary text-lg max-w-2xl mx-auto">Everything integrated in one platform - no third-party tools needed.</p>
        </div>

        <div class="grid lg:grid-cols-3 gap-6">
            <!-- Ticket Management -->
            <div class="bg-gradient-to-br from-blue-50 to-white rounded-3xl border border-blue-100 p-8 group hover:shadow-xl hover:shadow-blue-500/5 transition-all duration-300">
                <div class="size-14 bg-white rounded-2xl shadow-sm flex items-center justify-center mb-5 border border-blue-100">
                    <i data-lucide="ticket" class="size-7 text-primary"></i>
                </div>
                <h3 class="text-xl font-bold text-foreground mb-3">Ticket Management</h3>
                <p class="text-sm text-secondary leading-relaxed mb-5">Complete incident and service request management with SLA tracking, priority escalation, and team assignment.</p>
                <ul class="space-y-2">
                    @foreach(['SLA monitoring & alerts', 'Auto-assignment to teams', 'Priority-based escalation', 'Canned responses'] as $item)
                    <li class="flex items-center gap-2 text-sm text-secondary">
                        <i data-lucide="check" class="size-4 text-success shrink-0"></i>
                        {{ $item }}
                    </li>
                    @endforeach
                </ul>
            </div>

            <!-- Access Management -->
            <div class="bg-gradient-to-br from-green-50 to-white rounded-3xl border border-green-100 p-8 group hover:shadow-xl hover:shadow-green-500/5 transition-all duration-300">
                <div class="size-14 bg-white rounded-2xl shadow-sm flex items-center justify-center mb-5 border border-green-100">
                    <i data-lucide="key-round" class="size-7 text-green-600"></i>
                </div>
                <h3 class="text-xl font-bold text-foreground mb-3">Access Management</h3>
                <p class="text-sm text-secondary leading-relaxed mb-5">Structured access provisioning with multi-level approvals and complete audit trails for compliance.</p>
                <ul class="space-y-2">
                    @foreach(['Multi-level approval workflow', 'System-based access control', 'Audit trail & compliance', 'Bulk provisioning'] as $item)
                    <li class="flex items-center gap-2 text-sm text-secondary">
                        <i data-lucide="check" class="size-4 text-success shrink-0"></i>
                        {{ $item }}
                    </li>
                    @endforeach
                </ul>
            </div>

            <!-- Change Management -->
            <div class="bg-gradient-to-br from-purple-50 to-white rounded-3xl border border-purple-100 p-8 group hover:shadow-xl hover:shadow-purple-500/5 transition-all duration-300">
                <div class="size-14 bg-white rounded-2xl shadow-sm flex items-center justify-center mb-5 border border-purple-100">
                    <i data-lucide="git-pull-request" class="size-7 text-purple-600"></i>
                </div>
                <h3 class="text-xl font-bold text-foreground mb-3">Change Management</h3>
                <p class="text-sm text-secondary leading-relaxed mb-5">Structured change control process with risk assessment, scheduling, and post-implementation review.</p>
                <ul class="space-y-2">
                    @foreach(['Risk assessment & categorization', 'Change calendar scheduling', 'Implementation tracking', 'Rollback planning'] as $item)
                    <li class="flex items-center gap-2 text-sm text-secondary">
                        <i data-lucide="check" class="size-4 text-success shrink-0"></i>
                        {{ $item }}
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 lg:py-28 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="relative bg-gradient-to-br from-primary via-blue-600 to-indigo-700 rounded-3xl p-10 md:p-16 text-center overflow-hidden">
            <!-- Decorative -->
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>
            <div class="absolute top-8 left-8 grid grid-cols-4 gap-2 opacity-20">
                @for($i = 0; $i < 16; $i++)
                <div class="size-1.5 rounded-full bg-white"></div>
                @endfor
            </div>

            <div class="relative z-10">
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white mb-4 leading-tight">Ready to Transform Your<br>IT Operations?</h2>
                <p class="text-blue-100 text-lg max-w-2xl mx-auto mb-8">Join hundreds of IT teams already using {{ $appName }} to deliver better service, faster.</p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="{{ route('register') }}" class="h-12 px-8 inline-flex items-center justify-center rounded-full text-sm font-bold text-primary bg-white hover:bg-gray-50 shadow-xl shadow-black/10 transition-all duration-200 hover:scale-[1.03] active:scale-95 gap-2">
                        Start Free Now
                        <i data-lucide="arrow-right" class="size-4"></i>
                    </a>
                    <a href="{{ route('login') }}" class="h-12 px-8 inline-flex items-center justify-center rounded-full text-sm font-bold text-white border-2 border-white/30 hover:bg-white/10 transition-all duration-200 gap-2">
                        Sign In
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="bg-foreground text-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-4 gap-8 mb-10">
            <!-- Brand -->
            <div class="md:col-span-2">
                <div class="flex items-center gap-3 mb-4">
                    @if($appLogo)
                    <img src="{{ Storage::url($appLogo) }}" alt="{{ $appName }}" class="h-8 object-contain brightness-0 invert">
                    @else
                    <div class="size-9 bg-primary rounded-xl flex items-center justify-center">
                        <i data-lucide="headset" class="size-5 text-white"></i>
                    </div>
                    <span class="font-bold text-lg">{{ $appName }}</span>
                    @endif
                </div>
                <p class="text-gray-400 text-sm leading-relaxed max-w-sm">{{ $appDesc }}</p>
            </div>

            <!-- Quick Links -->
            <div>
                <h4 class="font-bold text-sm mb-4">Quick Links</h4>
                <ul class="space-y-2.5">
                    <li><a href="#features" class="text-sm text-gray-400 hover:text-white transition-colors">Features</a></li>
                    <li><a href="#how-it-works" class="text-sm text-gray-400 hover:text-white transition-colors">How It Works</a></li>
                    <li><a href="#modules" class="text-sm text-gray-400 hover:text-white transition-colors">Modules</a></li>
                </ul>
            </div>

            <!-- Access -->
            <div>
                <h4 class="font-bold text-sm mb-4">Get Started</h4>
                <ul class="space-y-2.5">
                    <li><a href="{{ route('login') }}" class="text-sm text-gray-400 hover:text-white transition-colors">Sign In</a></li>
                    <li><a href="{{ route('register') }}" class="text-sm text-gray-400 hover:text-white transition-colors">Create Account</a></li>
                </ul>
            </div>
        </div>

        <div class="border-t border-gray-700 pt-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-sm text-gray-500">&copy; {{ date('Y') }} {{ $appName }}. All rights reserved.</p>
            <div class="flex items-center gap-4">
                <span class="text-sm text-gray-500">Built with</span>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-red-500/10 text-red-400 border border-red-500/20">Laravel</span>
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-purple-500/10 text-purple-400 border border-purple-500/20">Livewire</span>
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-cyan-500/10 text-cyan-400 border border-cyan-500/20">Tailwind</span>
                </div>
            </div>
        </div>
    </div>
</footer>

<script>
    document.addEventListener('DOMContentLoaded', () => { if(window.lucide) lucide.createIcons(); });
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(a => {
        a.addEventListener('click', e => {
            e.preventDefault();
            const el = document.querySelector(a.getAttribute('href'));
            if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });
</script>
</body>
</html>
