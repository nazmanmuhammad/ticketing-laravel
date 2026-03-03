@props(['cols' => 6, 'rows' => 5])

<div wire:loading.delay class="w-full">
    @for($i = 0; $i < $rows; $i++)
    <div class="animate-pulse flex items-center gap-4 px-4 py-3.5 border-b border-border last:border-0">
        @for($j = 0; $j < $cols; $j++)
        <div class="h-4 bg-muted rounded {{ $j === 0 ? 'w-20' : ($j === 1 ? 'flex-1' : 'w-' . collect([16, 20, 24, 28, 32])->random()) }}"></div>
        @endfor
    </div>
    @endfor
</div>
