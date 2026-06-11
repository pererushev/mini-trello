<?php

namespace Database\Factories;

use App\Models\Board;
use App\Models\Task;
use App\Models\User;
use App\Support\BoardDefaults;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Board>
 */
class BoardFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(3),
            'description' => fake()->optional()->paragraph(),
        ];
    }

    public function withDefaultColumns(): static
    {
        return $this->afterCreating(function (Board $board): void {
            foreach (BoardDefaults::COLUMN_TITLES as $index => $title) {
                $column = $board->columns()->create([
                    'title' => $title,
                    'order' => $index,
                ]);

                Task::factory()
                    ->count(2)
                    ->sequence(
                        ['order' => 0],
                        ['order' => 1],
                    )
                    ->create(['column_id' => $column->id]);
            }
        });
    }
}
