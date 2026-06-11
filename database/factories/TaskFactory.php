<?php

namespace Database\Factories;

use App\Models\Column;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'column_id' => Column::factory(),
            'title' => fake()->sentence(2),
            'description' => fake()->optional()->sentence(),
            'order' => 0,
        ];
    }
}
