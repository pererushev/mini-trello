<?php

use App\Concerns\BoardValidationRules;
use App\Models\Board;
use App\Models\Column;
use App\Models\Task;
use Flux\Flux;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Board')] class extends Component {
    use AuthorizesRequests;
    use BoardValidationRules;

    #[Locked]
    public Board $board;

    public ?int $addingTaskToColumn = null;

    public string $newTaskTitle = '';

    public ?int $editingTaskId = null;

    public string $taskTitle = '';

    public string $taskDescription = '';

    public function mount(Board $board): void
    {
        $this->authorize('view', $board);

        $this->board = $board;
        $this->loadBoard();
    }

    #[Computed]
    public function boardTitle(): string
    {
        return $this->board->title;
    }

    #[Computed]
    public function columns()
    {
        return $this->board->columns()
            ->with(['tasks' => fn ($query) => $query->orderBy('order')])
            ->orderBy('order')
            ->get();
    }

    public function startAddingTask(int $columnId): void
    {
        $this->authorize('update', $this->board);

        $this->addingTaskToColumn = $columnId;
        $this->newTaskTitle = '';
    }

    public function cancelAddingTask(): void
    {
        $this->addingTaskToColumn = null;
        $this->newTaskTitle = '';
    }

    public function saveNewTask(int $columnId): void
    {
        $this->authorize('update', $this->board);

        $this->validate([
            'newTaskTitle' => ['required', 'string', 'max:255'],
        ]);

        $column = $this->board->columns()->findOrFail($columnId);

        $column->tasks()->create([
            'title' => $this->newTaskTitle,
            'order' => ($column->tasks()->max('order') ?? -1) + 1,
        ]);

        $this->cancelAddingTask();
        $this->loadBoard();

        Flux::toast(variant: 'success', text: 'Задача создана.');
    }

    public function editTask(int $taskId): void
    {
        $this->authorize('view', $this->board);

        $task = $this->findBoardTask($taskId);

        $this->editingTaskId = $task->id;
        $this->taskTitle = $task->title;
        $this->taskDescription = $task->description ?? '';

        Flux::modal('edit-task')->show();
    }

    public function updateTask(): void
    {
        $this->authorize('update', $this->board);

        $this->validate($this->taskRules());

        $task = $this->findBoardTask($this->editingTaskId);

        $task->update([
            'title' => $this->taskTitle,
            'description' => $this->taskDescription ?: null,
        ]);

        $this->closeTaskModal();
        $this->loadBoard();

        Flux::toast(variant: 'success', text: 'Задача обновлена.');
    }

    public function deleteTask(): void
    {
        $this->authorize('update', $this->board);

        $task = $this->findBoardTask($this->editingTaskId);
        $columnId = $task->column_id;

        $task->delete();
        $this->reorderColumn($columnId);

        $this->closeTaskModal();
        $this->loadBoard();

        Flux::toast(variant: 'success', text: 'Задача удалена.');
    }

    public function sortTask(int $taskId, int $position, ?int $columnId = null): void
    {
        $this->authorize('update', $this->board);

        $task = $this->findBoardTask($taskId);

        $sourceColumnId = $task->column_id;
        $targetColumnId = $columnId ?? $sourceColumnId;

        $this->board->columns()->findOrFail($targetColumnId);

        DB::transaction(function () use ($task, $position, $sourceColumnId, $targetColumnId): void {
            if ($sourceColumnId !== $targetColumnId) {
                $task->update(['column_id' => $targetColumnId]);
                $this->reorderColumn($sourceColumnId);
            }

            $taskIds = Task::query()
                ->where('column_id', $targetColumnId)
                ->where('id', '!=', $task->id)
                ->orderBy('order')
                ->pluck('id')
                ->all();

            array_splice($taskIds, $position, 0, [$task->id]);

            foreach ($taskIds as $index => $id) {
                Task::where('id', $id)->update(['order' => $index]);
            }
        });

        $this->loadBoard();
    }

    protected function findBoardTask(int $taskId): Task
    {
        return Task::query()
            ->whereHas('column', fn ($query) => $query->where('board_id', $this->board->id))
            ->findOrFail($taskId);
    }

    protected function reorderColumn(int $columnId): void
    {
        Task::query()
            ->where('column_id', $columnId)
            ->orderBy('order')
            ->get()
            ->each(fn (Task $task, int $index) => $task->update(['order' => $index]));
    }

    protected function loadBoard(): void
    {
        $this->board->refresh()->load([
            'columns.tasks' => fn ($query) => $query->orderBy('order'),
        ]);

        unset($this->columns, $this->boardTitle);
    }

    protected function closeTaskModal(): void
    {
        $this->editingTaskId = null;
        $this->reset(['taskTitle', 'taskDescription']);
        Flux::modal('edit-task')->close();
    }
}; ?>

<div class="flex h-full w-full flex-1 flex-col gap-4">
    <div class="flex items-center gap-4">
        <flux:button :href="route('boards.index')" icon="arrow-left" variant="ghost" wire:navigate />

        <div class="min-w-0 flex-1">
            <flux:heading size="xl" class="truncate">{{ $this->boardTitle }}</flux:heading>

            @if ($board->description)
                <flux:text class="truncate">{{ $board->description }}</flux:text>
            @endif
        </div>
    </div>

    <div class="flex flex-1 gap-4 overflow-x-auto pb-4">
        @foreach ($this->columns as $column)
            <div
                wire:key="column-{{ $column->id }}"
                class="flex w-72 shrink-0 flex-col rounded-xl border border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900"
            >
                <div class="border-b border-zinc-200 px-4 py-3 dark:border-zinc-700">
                    <flux:heading size="sm">{{ $column->title }}</flux:heading>
                    @php
                        $tasksInColumn = $column->tasks->count();
                        $tasksLabel = match (true) {
                            $tasksInColumn % 10 === 1 && $tasksInColumn % 100 !== 11 => "{$tasksInColumn} задача",
                            in_array($tasksInColumn % 10, [2, 3, 4], true) && ! in_array($tasksInColumn % 100, [12, 13, 14], true) => "{$tasksInColumn} задачи",
                            default => "{$tasksInColumn} задач",
                        };
                    @endphp
                    <flux:text size="sm" class="text-zinc-500">
                        {{ $tasksLabel }}
                    </flux:text>
                </div>

                <ul
                    wire:sort="sortTask"
                    wire:sort:group="tasks"
                    wire:sort:group-id="{{ $column->id }}"
                    class="flex min-h-24 flex-1 flex-col gap-2 p-3"
                >
                    @foreach ($column->tasks as $task)
                        <li
                            wire:sort:item="{{ $task->id }}"
                            wire:key="task-{{ $task->id }}"
                            wire:click="editTask({{ $task->id }})"
                            class="cursor-grab rounded-lg border border-zinc-200 bg-white p-3 shadow-sm transition hover:border-zinc-300 active:cursor-grabbing dark:border-zinc-600 dark:bg-zinc-800 dark:hover:border-zinc-500"
                        >
                            <flux:text class="font-medium">{{ $task->title }}</flux:text>

                            @if ($task->description)
                                <flux:text size="sm" class="mt-1 line-clamp-2 text-zinc-500">
                                    {{ $task->description }}
                                </flux:text>
                            @endif
                        </li>
                    @endforeach
                </ul>

                <div class="border-t border-zinc-200 p-3 dark:border-zinc-700">
                    @if ($addingTaskToColumn === $column->id)
                        <form wire:submit="saveNewTask({{ $column->id }})" class="space-y-2">
                            <flux:input
                                wire:model="newTaskTitle"
                                placeholder="Название задачи..."
                                data-test="new-task-input-{{ $column->id }}"
                            />

                            <div class="flex gap-2">
                                <flux:button size="sm" variant="primary" type="submit" data-test="save-task-{{ $column->id }}">
                                    Добавить
                                </flux:button>

                                <flux:button size="sm" variant="ghost" type="button" wire:click="cancelAddingTask">
                                    Отмена
                                </flux:button>
                            </div>
                        </form>
                    @else
                        @can('update', $board)
                            <flux:button
                                size="sm"
                                variant="ghost"
                                icon="plus"
                                class="w-full"
                                wire:click="startAddingTask({{ $column->id }})"
                                data-test="add-task-{{ $column->id }}"
                            >
                                Добавить задачу
                            </flux:button>
                        @endcan
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <flux:modal name="edit-task" class="max-w-lg">
        <form wire:submit="updateTask" class="space-y-6">
            <div>
                <flux:heading size="lg">Редактирование задачи</flux:heading>
            </div>

            <flux:input wire:model="taskTitle" label="Название" required />

            <flux:textarea wire:model="taskDescription" label="Описание" rows="4" />

            <div class="flex justify-between gap-2">
                @can('update', $board)
                    <flux:button variant="danger" type="button" wire:click="deleteTask" data-test="delete-task">
                        Удалить
                    </flux:button>
                @endcan

                <div class="ms-auto flex gap-2">
                    <flux:modal.close>
                        <flux:button variant="ghost" type="button">Отмена</flux:button>
                    </flux:modal.close>

                    @can('update', $board)
                        <flux:button variant="primary" type="submit" data-test="save-task">
                            Сохранить
                        </flux:button>
                    @endcan
                </div>
            </div>
        </form>
    </flux:modal>
</div>
