<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Routine;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Routine>
 */
class RoutineFactory extends Factory
{
    protected $model = Routine::class;

    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'weekly_frequency' => fake()->numberBetween(1, 6),
            'start_date' => fake()->dateTimeBetween('-3 months', 'now'),
            'end_date' => null,
            'is_active' => true,
        ];
    }
}
