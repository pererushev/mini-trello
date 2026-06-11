<x-layouts::app :title="__('Dashboard')">
    @php
        $user = auth()->user();
        $boards = \App\Models\Board::query()
            ->accessibleBy($user)
            ->withCount('columns')
            ->latest()
            ->limit(6)
            ->get();

        $boardsCount = \App\Models\Board::query()->accessibleBy($user)->count();
        $tasksCount = \App\Models\Task::query()
            ->whereHas('column.board', fn ($q) => $q->accessibleBy($user))
            ->count();

        $hour = (int) now()->format('H');
        $greeting = match (true) {
            $hour < 6 => 'Доброй ночи',
            $hour < 12 => 'Доброе утро',
            $hour < 18 => 'Добрый день',
            default => 'Добрый вечер',
        };
    @endphp

    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <div class="dashboard-hero">
            <div class="absolute end-0 top-0 size-64 translate-x-1/4 -translate-y-1/4 rounded-full bg-brand-500/10 blur-3xl"></div>
            <div class="absolute bottom-0 start-0 size-48 -translate-x-1/4 translate-y-1/4 rounded-full bg-amber-glow/10 blur-3xl"></div>

            <div class="relative flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm font-medium text-brand-300">{{ $greeting }}, {{ explode(' ', $user->name)[0] }}!</p>
                    <h1 class="mt-1 text-2xl font-bold text-white sm:text-3xl">Ваш рабочий стол</h1>
                    <p class="mt-2 max-w-md text-brand-100/70">Управляйте досками, отслеживайте прогресс и держите задачи в фокусе.</p>
                </div>

                <flux:button :href="route('boards.index')" variant="primary" icon="plus" wire:navigate class="shrink-0 !bg-white !text-brand-800 hover:!bg-brand-50">
                    Новая доска
                </flux:button>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-3">
            <div class="stat-card">
                <div class="flex items-center gap-3">
                    <span class="flex size-10 items-center justify-center rounded-xl bg-brand-100 text-brand-700 dark:bg-brand-900/50 dark:text-brand-300">
                        <flux:icon.layout-grid class="size-5" />
                    </span>
                    <div>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $boardsCount }}</p>
                        <p class="text-sm text-zinc-500">
                            @php
                                $boardsLabel = match (true) {
                                    $boardsCount % 10 === 1 && $boardsCount % 100 !== 11 => 'доска',
                                    in_array($boardsCount % 10, [2, 3, 4], true) && ! in_array($boardsCount % 100, [12, 13, 14], true) => 'доски',
                                    default => 'досок',
                                };
                            @endphp
                            {{ $boardsLabel }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="flex items-center gap-3">
                    <span class="flex size-10 items-center justify-center rounded-xl bg-violet-100 text-violet-700 dark:bg-violet-900/50 dark:text-violet-300">
                        <flux:icon.queue-list class="size-5" />
                    </span>
                    <div>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $tasksCount }}</p>
                        <p class="text-sm text-zinc-500">
                            @php
                                $tasksLabel = match (true) {
                                    $tasksCount % 10 === 1 && $tasksCount % 100 !== 11 => 'задача',
                                    in_array($tasksCount % 10, [2, 3, 4], true) && ! in_array($tasksCount % 100, [12, 13, 14], true) => 'задачи',
                                    default => 'задач',
                                };
                            @endphp
                            {{ $tasksLabel }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="flex items-center gap-3">
                    <span class="flex size-10 items-center justify-center rounded-xl bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-300">
                        <flux:icon.clock class="size-5" />
                    </span>
                    <div>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $boards->count() }}</p>
                        <p class="text-sm text-zinc-500">Недавних досок</p>
                    </div>
                </div>
            </div>
        </div>

        @if ($boards->isEmpty())
            <div class="flex flex-1 flex-col items-center justify-center gap-4 rounded-2xl border border-dashed border-brand-300/40 bg-brand-50/50 p-12 dark:border-brand-700/40 dark:bg-brand-950/20">
                <span class="flex size-16 items-center justify-center rounded-2xl bg-brand-100 text-brand-600 dark:bg-brand-900/50 dark:text-brand-400">
                    <flux:icon.layout-grid class="size-8" />
                </span>
                <flux:heading size="lg">Создайте первую доску</flux:heading>
                <flux:text class="max-w-sm text-center">Организуйте задачи в канбан-формате — перетаскивайте карточки между колонками.</flux:text>
                <flux:button :href="route('boards.index')" variant="primary" icon="plus" wire:navigate>
                    Создать доску
                </flux:button>
            </div>
        @else
            <div>
                <div class="mb-4 flex items-center justify-between">
                    <flux:heading size="lg">Недавние доски</flux:heading>
                    <a href="{{ route('boards.index') }}" wire:navigate class="text-sm font-medium text-brand-600 hover:text-brand-500 dark:text-brand-400">
                        Все доски →
                    </a>
                </div>

                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($boards as $index => $board)
                        <x-board-card
                            :board="$board"
                            :accent="$index"
                            :href="route('boards.show', $board)"
                            class="group"
                        />
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-layouts::app>
