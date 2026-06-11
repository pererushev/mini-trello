<?php

use App\Concerns\BoardValidationRules;
use App\Models\Board;
use Flux\Flux;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Boards')] class extends Component {
    use AuthorizesRequests;
    use BoardValidationRules;

    public string $title = '';

    public string $description = '';

    public ?int $deletingBoardId = null;

    #[Computed]
    public function boards()
    {
        return Board::query()
            ->accessibleBy(Auth::user())
            ->withCount('columns')
            ->latest()
            ->get();
    }

    public function createBoard(): void
    {
        $this->authorize('create', Board::class);

        $validated = $this->validate($this->boardRules());

        $board = Auth::user()->boards()->create($validated);

        foreach (['To Do', 'In Progress', 'Done'] as $index => $columnTitle) {
            $board->columns()->create([
                'title' => $columnTitle,
                'order' => $index,
            ]);
        }

        $this->reset(['title', 'description']);

        Flux::modal('create-board')->close();
        Flux::toast(variant: 'success', text: __('Board created.'));

        $this->redirect(route('boards.show', $board), navigate: true);
    }

    public function confirmDeleteBoard(int $boardId): void
    {
        $board = Board::findOrFail($boardId);

        $this->authorize('delete', $board);

        $this->deletingBoardId = $boardId;
        Flux::modal('delete-board')->show();
    }

    public function deleteBoard(): void
    {
        $board = Board::findOrFail($this->deletingBoardId);

        $this->authorize('delete', $board);

        $board->delete();

        $this->deletingBoardId = null;

        Flux::modal('delete-board')->close();
        Flux::toast(variant: 'success', text: __('Board deleted.'));

        unset($this->boards);
    }
}; ?>

<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Boards') }}</flux:heading>
            <flux:subheading>{{ __('Manage your kanban boards') }}</flux:subheading>
        </div>

        <flux:modal.trigger name="create-board">
            <flux:button variant="primary" icon="plus" data-test="create-board-button">
                {{ __('New board') }}
            </flux:button>
        </flux:modal.trigger>
    </div>

    @if ($this->boards->isEmpty())
        <div class="flex flex-1 flex-col items-center justify-center gap-4 rounded-xl border border-dashed border-zinc-300 p-12 dark:border-zinc-600">
            <flux:icon.layout-grid class="size-12 text-zinc-400" />
            <flux:heading size="lg">{{ __('No boards yet') }}</flux:heading>
            <flux:text>{{ __('Create your first board to get started.') }}</flux:text>
            <flux:modal.trigger name="create-board">
                <flux:button variant="primary" icon="plus">{{ __('Create board') }}</flux:button>
            </flux:modal.trigger>
        </div>
    @else
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($this->boards as $board)
                <div
                    wire:key="board-{{ $board->id }}"
                    class="group relative rounded-xl border border-zinc-200 bg-white p-5 transition hover:border-zinc-300 hover:shadow-sm dark:border-zinc-700 dark:bg-zinc-900 dark:hover:border-zinc-600"
                >
                    <a href="{{ route('boards.show', $board) }}" wire:navigate class="block space-y-2">
                        <flux:heading size="lg" class="group-hover:text-blue-600 dark:group-hover:text-blue-400">
                            {{ $board->title }}
                        </flux:heading>

                        @if ($board->description)
                            <flux:text class="line-clamp-2">{{ $board->description }}</flux:text>
                        @endif

                        <flux:text size="sm" class="text-zinc-500">
                            {{ trans_choice(':count column|:count columns', $board->columns_count, ['count' => $board->columns_count]) }}
                        </flux:text>
                    </a>

                    @can('delete', $board)
                        <div class="absolute end-3 top-3 opacity-0 transition group-hover:opacity-100">
                            <flux:button
                                size="sm"
                                variant="ghost"
                                icon="trash"
                                wire:click.prevent="confirmDeleteBoard({{ $board->id }})"
                                data-test="delete-board-{{ $board->id }}"
                            />
                        </div>
                    @endcan
                </div>
            @endforeach
        </div>
    @endif

    <flux:modal name="create-board" class="max-w-lg">
        <form wire:submit="createBoard" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Create board') }}</flux:heading>
                <flux:subheading>{{ __('Add a new kanban board to organize your tasks.') }}</flux:subheading>
            </div>

            <flux:input wire:model="title" :label="__('Title')" placeholder="{{ __('My project') }}" required />

            <flux:textarea wire:model="description" :label="__('Description')" placeholder="{{ __('Optional description...') }}" rows="3" />

            <div class="flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>

                <flux:button variant="primary" type="submit" data-test="submit-create-board">
                    {{ __('Create') }}
                </flux:button>
            </div>
        </form>
    </flux:modal>

    <flux:modal name="delete-board" class="max-w-lg">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Delete board?') }}</flux:heading>
                <flux:subheading>{{ __('This will permanently delete the board and all its columns and tasks.') }}</flux:subheading>
            </div>

            <div class="flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>

                <flux:button variant="danger" wire:click="deleteBoard" data-test="confirm-delete-board">
                    {{ __('Delete') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
