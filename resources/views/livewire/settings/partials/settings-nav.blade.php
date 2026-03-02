@php $currentRoute = request()->route()?->getName() ?? ''; @endphp
<div class="flex flex-wrap gap-2 mb-6">
    @foreach([
        'settings.categories' => 'Categories',
        'settings.systems' => 'Systems',
        'settings.teams' => 'Teams',
        'settings.sla' => 'SLA Settings',
        'settings.workflows' => 'Workflows',
        'settings.canned-responses' => 'Canned Responses',
    ] as $route => $label)
    <a href="{{ route($route) }}"
       class="px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200 {{ $currentRoute === $route ? 'bg-primary text-white shadow-md shadow-primary/20' : 'bg-white border border-border text-secondary hover:border-primary hover:text-primary' }}">
        {{ $label }}
    </a>
    @endforeach
</div>
