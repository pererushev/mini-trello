<?php

namespace Database\Seeders;

use App\Models\Board;
use App\Models\Task;
use App\Models\User;
use App\Support\BoardDefaults;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    /**
     * Заполнить базу демонстрационными данными на русском языке.
     */
    public function run(): void
    {
        $admin = User::factory()->create([
            'name' => 'Алексей Смирнов',
            'email' => 'demo@mini-trello.test',
            'password' => Hash::make('password'),
        ]);

        $colleague = User::factory()->create([
            'name' => 'Мария Петрова',
            'email' => 'maria@mini-trello.test',
            'password' => Hash::make('password'),
        ]);

        $boards = [
            [
                'title' => 'Разработка Mini Trello',
                'description' => 'Спринт по запуску MVP: канбан-доски, drag-and-drop задач и совместная работа.',
                'columns' => [
                    BoardDefaults::COLUMN_TODO => [
                        ['title' => 'Настроить CI/CD', 'description' => 'Добавить GitHub Actions для тестов и линтера.'],
                        ['title' => 'Написать README', 'description' => 'Описать установку, сидирование и основные возможности.'],
                        ['title' => 'Добавить уведомления', 'description' => 'Email при назначении на задачу.'],
                    ],
                    BoardDefaults::COLUMN_IN_PROGRESS => [
                        ['title' => 'Дизайн главной страницы', 'description' => 'Уникальный лендинг с превью канбана.'],
                        ['title' => 'Русское сидирование', 'description' => 'Демо-данные для быстрого старта.'],
                    ],
                    BoardDefaults::COLUMN_DONE => [
                        ['title' => 'Модели Board, Column, Task', 'description' => 'Связи и фабрики готовы.'],
                        ['title' => 'Авторизация пользователей', 'description' => 'Fortify + Livewire Flux.'],
                        ['title' => 'Drag-and-drop задач', 'description' => 'Перетаскивание между колонками.'],
                    ],
                ],
            ],
            [
                'title' => 'Маркетинг Q2',
                'description' => 'Кампания запуска продукта: контент, соцсети и лендинг.',
                'columns' => [
                    BoardDefaults::COLUMN_TODO => [
                        ['title' => 'Сценарий демо-видео', 'description' => '2 минуты — от регистрации до первой доски.'],
                        ['title' => 'Пост в Telegram', 'description' => 'Анонс открытого бета-теста.'],
                    ],
                    BoardDefaults::COLUMN_IN_PROGRESS => [
                        ['title' => 'Лендинг для рекламы', 'description' => 'Отдельная страница с UTM-метками.'],
                        ['title' => 'Сбор отзывов', 'description' => 'Опрос первых пользователей.'],
                    ],
                    BoardDefaults::COLUMN_DONE => [
                        ['title' => 'Логотип и фирменные цвета', 'description' => 'Бирюзовый + янтарный акцент.'],
                    ],
                ],
            ],
            [
                'title' => 'Личные дела',
                'description' => 'Планы на неделю и домашние задачи.',
                'columns' => [
                    BoardDefaults::COLUMN_TODO => [
                        ['title' => 'Записаться к стоматологу', 'description' => null],
                        ['title' => 'Купить продукты', 'description' => 'Молоко, хлеб, овощи.'],
                    ],
                    BoardDefaults::COLUMN_IN_PROGRESS => [
                        ['title' => 'Разобрать гардероб', 'description' => 'Отдать вещи на благотворительность.'],
                    ],
                    BoardDefaults::COLUMN_DONE => [
                        ['title' => 'Оплатить интернет', 'description' => null],
                    ],
                ],
            ],
        ];

        foreach ($boards as $boardData) {
            $board = Board::create([
                'user_id' => $admin->id,
                'title' => $boardData['title'],
                'description' => $boardData['description'],
            ]);

            if ($boardData['title'] === 'Разработка Mini Trello') {
                $board->members()->attach($colleague->id, ['role' => 'member']);
            }

            $order = 0;
            foreach ($boardData['columns'] as $columnTitle => $tasks) {
                $column = $board->columns()->create([
                    'title' => $columnTitle,
                    'order' => $order++,
                ]);

                $taskOrder = 0;
                foreach ($tasks as $taskData) {
                    $task = $column->tasks()->create([
                        'title' => $taskData['title'],
                        'description' => $taskData['description'],
                        'order' => $taskOrder++,
                    ]);

                    if ($boardData['title'] === 'Разработка Mini Trello' && $taskData['title'] === 'Дизайн главной страницы') {
                        $task->comments()->create([
                            'user_id' => $colleague->id,
                            'body' => 'Предлагаю добавить превью канбана на лендинг — будет нагляднее!',
                        ]);

                        $task->users()->attach($colleague->id);
                    }
                }
            }
        }
    }
}
