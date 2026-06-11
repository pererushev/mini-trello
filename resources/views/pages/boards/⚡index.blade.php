<?php

use App\Concerns\BoardValidationRules;
use App\Models\Board;
use App\Support\BoardDefaults;
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

        foreach (BoardDefaults::COLUMN_TITLES as $index => $columnTitle) {
            $board->columns()->create([
                'title' => $columnTitle,
                'order' => $index,
            ]);
        }

        $this->reset(['title', 'description']);

        Flux::modal('create-board')->close();
        Flux::toast(variant: 'success', text: 'Доска создана.');

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
        Flux::toast(variant: 'success', text: 'Доска удалена.');

        unset($this->boards);
    }
}; ?>

<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="dashboard-hero !border-brand-700/20">
        <div class="absolute end-0 top-0 size-48 translate-x-1/3 -translate-y-1/3 rounded-full bg-brand-500/10 blur-3xl"></div>

        <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white sm:text-3xl">Мои доски</h1>
                <p class="mt-1 text-brand-100/70">Создавайте канбан-доски и управляйте задачами в одном месте</p>
            </div>

            <flux:modal.trigger name="create-board">
                <flux:button variant="primary" icon="plus" data-test="create-board-button" class="shrink-0 !bg-white !text-brand-800 hover:!bg-brand-50">
                    Новая доска
                </flux:button>
            </flux:modal.trigger>
        </div>
    </div>

    @if ($this->boards->isEmpty())
        <div class="flex flex-1 flex-col items-center justify-center gap-4 rounded-2xl border border-dashed border-brand-300/40 bg-brand-50/50 p-12 dark:border-brand-700/40 dark:bg-brand-950/20">
            <span class="flex size-16 items-center justify-center rounded-2xl bg-brand-100 text-brand-600 dark:bg-brand-900/50 dark:text-brand-400">
                <flux:icon.layout-grid class="size-8" />
            </span>
            <flux:heading size="lg">Пока нет досок</flux:heading>
            <flux:text class="max-w-sm text-center">Создайте первую доску, чтобы начать организовывать задачи.</flux:text>
            <flux:modal.trigger name="create-board">
                <flux:button variant="primary" icon="plus">Создать доску</flux:button>
            </flux:modal.trigger>
        </div>
    @else
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($this->boards as $index => $board)
                <div wire:key="board-{{ $board->id }}" class="group relative">
                    <x-board-card
                        :board="$board"
                        :accent="$index"
                        :href="route('boards.show', $board)"
                    />

                    @can('delete', $board)
                        <div class="absolute end-3 top-3 z-10 opacity-0 transition group-hover:opacity-100">
                            <flux:button
                                size="sm"
                                variant="ghost"
                                icon="trash"
                                wire:click.prevent="confirmDeleteBoard({{ $board->id }})"
                                data-test="delete-board-{{ $board->id }}"
                                class="!bg-white/90 dark:!bg-zinc-800/90"
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
                <flux:heading size="lg">Создать доску</flux:heading>
                <flux:subheading>Новая канбан-доска для организации задач</flux:subheading>
            </div>

            <flux:input wire:model="title" label="Название" placeholder="Мой проект" required />

            <flux:textarea wire:model="description" label="Описание" placeholder="Необязательное описание..." rows="3" />

            <div class="flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button variant="ghost">Отмена</flux:button>
                </flux:modal.close>

                <flux:button variant="primary" type="submit" data-test="submit-create-board">
                    Создать
                </flux:button>
            </div>
        </form>
    </flux:modal>

    <flux:modal name="delete-board" class="max-w-lg">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Удалить доску?</flux:heading>
                <flux:subheading>Доска, все колонки и задачи будут удалены безвозвратно.</flux:subheading>
            </div>

            <div class="flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button variant="ghost">Отмена</flux:button>
                </flux:modal.close>

                <flux:button variant="danger" wire:click="deleteBoard" data-test="confirm-delete-board">
                    Удалить
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
