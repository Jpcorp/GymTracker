<?php

namespace Database\Factories;

use App\Models\Exercise;
use App\Models\Routine;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Exercise>
 */
class ExerciseFactory extends Factory
{
    protected $model = Exercise::class;

    public function definition(): array
    {
        return [
            'routine_id' => Routine::factory(),
            'name' => fake()->words(2, true),
            'muscle_group' => fake()->randomElement(['chest', 'back', 'legs', 'arms', 'shoulders', 'core']),
            'sets' => fake()->numberBetween(2, 5),
            'reps_range' => '8-12',
            'rest_seconds' => fake()->numberBetween(30, 120),
            'notes' => fake()->sentence(),
        ];
    }
}
