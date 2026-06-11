<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">{{ __('Dashboard') }}</flux:heading>
                <flux:subheading>{{ __('Welcome back!') }}</flux:subheading>
            </div>

            <flux:button :href="route('boards.index')" variant="primary" icon="layout-grid" wire:navigate>
                {{ __('View boards') }}
            </flux:button>
        </div>

        @php
            $boards = \App\Models\Board::query()
                ->accessibleBy(auth()->user())
                ->withCount('columns')
                ->latest()
                ->limit(6)
                ->get();
        @endphp

        @if ($boards->isEmpty())
            <div class="flex flex-1 flex-col items-center justify-center gap-4 rounded-xl border border-dashed border-zinc-300 p-12 dark:border-zinc-600">
                <flux:icon.layout-grid class="size-12 text-zinc-400" />
                <flux:heading size="lg">{{ __('Get started') }}</flux:heading>
                <flux:text>{{ __('Create your first kanban board to organize tasks.') }}</flux:text>
                <flux:button :href="route('boards.index')" variant="primary" icon="plus" wire:navigate>
                    {{ __('Create board') }}
                </flux:button>
            </div>
        @else
            <div>
                <flux:heading size="lg" class="mb-4">{{ __('Recent boards') }}</flux:heading>

                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($boards as $board)
                        <a
                            href="{{ route('boards.show', $board) }}"
                            wire:navigate
                            class="rounded-xl border border-zinc-200 bg-white p-5 transition hover:border-zinc-300 hover:shadow-sm dark:border-zinc-700 dark:bg-zinc-900 dark:hover:border-zinc-600"
                        >
                            <flux:heading size="lg">{{ $board->title }}</flux:heading>

                            @if ($board->description)
                                <flux:text class="mt-1 line-clamp-2">{{ $board->description }}</flux:text>
                            @endif

                            <flux:text size="sm" class="mt-2 text-zinc-500">
                                {{ trans_choice(':count column|:count columns', $board->columns_count, ['count' => $board->columns_count]) }}
                            </flux:text>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-layouts::app>
