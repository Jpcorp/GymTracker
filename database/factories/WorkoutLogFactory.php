<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Exercise;
use App\Models\WorkoutLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WorkoutLog>
 */
class WorkoutLogFactory extends Factory
{
    protected $model = WorkoutLog::class;

    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'exercise_id' => Exercise::factory(),
            'workout_date' => fake()->dateTimeBetween('-1 month', 'now'),
            'weight_kg' => fake()->randomFloat(2, 5, 150),
            'completed_sets' => fake()->numberBetween(2, 5),
            'completed_reps' => '10',
            'rpe' => fake()->numberBetween(5, 10),
            'notes' => fake()->sentence(),
        ];
    }
}
