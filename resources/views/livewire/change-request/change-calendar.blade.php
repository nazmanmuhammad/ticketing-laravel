@section('title', 'Change Calendar')
@section('page-title', 'Change Calendar')
@section('page-description', 'Scheduled change requests')

<div>
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <button wire:click="previousMonth" class="size-10 rounded-xl border border-border bg-white hover:border-primary flex items-center justify-center transition-all duration-200 cursor-pointer"><i data-lucide="chevron-left" class="size-5 text-secondary"></i></button>
            <h3 class="font-bold text-lg text-foreground min-w-[160px] text-center">{{ $monthLabel }}</h3>
            <button wire:click="nextMonth" class="size-10 rounded-xl border border-border bg-white hover:border-primary flex items-center justify-center transition-all duration-200 cursor-pointer"><i data-lucide="chevron-right" class="size-5 text-secondary"></i></button>
        </div>
        <a href="{{ route('change-requests.index') }}" class="h-10 px-4 rounded-xl border border-border bg-white text-sm font-semibold text-secondary hover:text-foreground hover:border-primary flex items-center gap-2 transition-all duration-200">
            <i data-lucide="list" class="size-4"></i> List View
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-border overflow-hidden">
        <!-- Day Headers -->
        <div class="grid grid-cols-7 border-b border-border">
            @foreach(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $day)
            <div class="px-2 py-3 text-center text-xs font-semibold text-secondary uppercase">{{ $day }}</div>
            @endforeach
        </div>

        <!-- Calendar Grid -->
        <div class="grid grid-cols-7">
            @foreach($days as $day)
            <div class="min-h-[100px] border-b border-r border-border p-1.5 {{ !$day['isCurrentMonth'] ? 'bg-muted/30' : '' }} {{ $day['isToday'] ? 'bg-primary/5' : '' }}">
                <span class="text-xs font-medium {{ $day['isToday'] ? 'bg-primary text-white size-6 rounded-full flex items-center justify-center' : ($day['isCurrentMonth'] ? 'text-foreground' : 'text-secondary/40') }}">
                    {{ $day['date']->day }}
                </span>
                <div class="mt-1 space-y-1">
                    @foreach($day['changes']->take(3) as $cr)
                    <a href="{{ route('change-requests.show', $cr) }}"
                       class="block px-1.5 py-1 rounded text-[10px] font-semibold truncate transition-colors duration-150 cursor-pointer
                              {{ $cr->change_type === 'emergency' ? 'bg-red-100 text-red-700 hover:bg-red-200' : ($cr->change_type === 'standard' ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-blue-100 text-blue-700 hover:bg-blue-200') }}">
                        {{ $cr->scheduled_at->format('H:i') }} {{ Str::limit($cr->title, 15) }}
                    </a>
                    @endforeach
                    @if($day['changes']->count() > 3)
                    <span class="text-[10px] text-secondary font-medium pl-1">+{{ $day['changes']->count() - 3 }} more</span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
