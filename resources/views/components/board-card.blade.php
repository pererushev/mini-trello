@props([
    'board',
    'accent' => 0,
])

@php
    $columnsCount = $board->columns_count ?? $board->columns->count();
    $columnsLabel = match (true) {
        $columnsCount % 10 === 1 && $columnsCount % 100 !== 11 => "{$columnsCount} колонка",
        in_array($columnsCount % 10, [2, 3, 4], true) && ! in_array($columnsCount % 100, [12, 13, 14], true) => "{$columnsCount} колонки",
        default => "{$columnsCount} колонок",
    };

    $tasksCount = $board->tasks_count ?? null;
    $tasksLabel = $tasksCount !== null ? match (true) {
        $tasksCount % 10 === 1 && $tasksCount % 100 !== 11 => "{$tasksCount} задача",
        in_array($tasksCount % 10, [2, 3, 4], true) && ! in_array($tasksCount % 100, [12, 13, 14], true) => "{$tasksCount} задачи",
        default => "{$tasksCount} задач",
    } : null;
@endphp

<a
    {{ $attributes->merge([
        'class' => 'board-card board-accent-'.($accent % 5).' group block ps-6',
    ]) }}
    wire:navigate
>
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0 flex-1">
            <h3 class="truncate text-lg font-semibold text-zinc-900 group-hover:text-brand-600 dark:text-zinc-100 dark:group-hover:text-brand-400">
                {{ $board->title }}
            </h3>

            @if ($board->description)
                <p class="mt-1 line-clamp-2 text-sm text-zinc-500 dark:text-zinc-400">
                    {{ $board->description }}
                </p>
            @endif
        </div>

        <span class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-zinc-100 text-zinc-500 transition group-hover:bg-brand-100 group-hover:text-brand-600 dark:bg-zinc-800 dark:text-zinc-400 dark:group-hover:bg-brand-900/50 dark:group-hover:text-brand-400">
            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
        </span>
    </div>

    <div class="mt-4 flex items-center gap-3 text-sm text-zinc-500">
        <span class="inline-flex items-center gap-1.5">
            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="5" height="18" rx="1" />
                <rect x="10" y="3" width="5" height="12" rx="1" />
                <rect x="17" y="3" width="5" height="15" rx="1" />
            </svg>
            {{ $columnsLabel }}
        </span>

        @if ($tasksLabel)
            <span class="text-zinc-300 dark:text-zinc-600">·</span>
            <span>{{ $tasksLabel }}</span>
        @endif
    </div>
</a>
