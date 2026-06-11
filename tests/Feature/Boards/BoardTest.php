<?php

use App\Models\Board;
use App\Models\Column;
use App\Models\Task;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected from boards index', function () {
    $this->get(route('boards.index'))->assertRedirect(route('login'));
});

test('authenticated users can visit boards index', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('boards.index'))
        ->assertOk();
});

test('user can create a board with default columns', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test('pages::boards.index')
        ->set('title', 'Sprint board')
        ->set('description', 'Q2 planning')
        ->call('createBoard')
        ->assertHasNoErrors()
        ->assertRedirect();

    $board = Board::query()->where('title', 'Sprint board')->first();

    expect($board)->not->toBeNull();
    expect($board->user_id)->toBe($user->id);
    expect($board->columns)->toHaveCount(3);
    expect($board->columns->pluck('title')->all())->toBe(['To Do', 'In Progress', 'Done']);
});

test('user can view their board', function () {
    $user = User::factory()->create();
    $board = Board::factory()->withDefaultColumns()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->get(route('boards.show', $board))
        ->assertOk();
});

test('user cannot view another users board', function () {
    $owner = User::factory()->create();
    $stranger = User::factory()->create();
    $board = Board::factory()->withDefaultColumns()->create(['user_id' => $owner->id]);

    $this->actingAs($stranger)
        ->get(route('boards.show', $board))
        ->assertForbidden();
});

test('user can create and update tasks on their board', function () {
    $user = User::factory()->create();
    $board = Board::factory()->withDefaultColumns()->create(['user_id' => $user->id]);
    $column = $board->columns()->first();

    $this->actingAs($user);

    Livewire::test('pages::boards.show', ['board' => $board])
        ->call('startAddingTask', $column->id)
        ->set('newTaskTitle', 'Write tests')
        ->call('saveNewTask', $column->id)
        ->assertHasNoErrors();

    $task = Task::query()->where('title', 'Write tests')->first();

    expect($task)->not->toBeNull();
    expect($task->column_id)->toBe($column->id);

    Livewire::test('pages::boards.show', ['board' => $board->fresh()])
        ->call('editTask', $task->id)
        ->set('taskTitle', 'Write feature tests')
        ->set('taskDescription', 'Cover boards flow')
        ->call('updateTask')
        ->assertHasNoErrors();

    expect($task->fresh()->title)->toBe('Write feature tests');
    expect($task->fresh()->description)->toBe('Cover boards flow');
});

test('user can move tasks between columns', function () {
    $user = User::factory()->create();
    $board = Board::factory()->withDefaultColumns()->create(['user_id' => $user->id]);

    $sourceColumn = $board->columns()->where('title', 'To Do')->first();
    $targetColumn = $board->columns()->where('title', 'In Progress')->first();

    $task = Task::factory()->create([
        'column_id' => $sourceColumn->id,
        'title' => 'Move me',
        'order' => 0,
    ]);

    $this->actingAs($user);

    Livewire::test('pages::boards.show', ['board' => $board])
        ->call('sortTask', $task->id, 0, $targetColumn->id)
        ->assertHasNoErrors();

    $task->refresh();

    expect($task->column_id)->toBe($targetColumn->id);
    expect($task->order)->toBe(0);
});

test('user can delete their board', function () {
    $user = User::factory()->create();
    $board = Board::factory()->withDefaultColumns()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    Livewire::test('pages::boards.index')
        ->call('confirmDeleteBoard', $board->id)
        ->call('deleteBoard')
        ->assertHasNoErrors();

    expect(Board::find($board->id))->toBeNull();
    expect(Column::where('board_id', $board->id)->count())->toBe(0);
});
