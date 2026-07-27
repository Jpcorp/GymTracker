<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\NutritionLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<NutritionLog>
 */
class NutritionLogFactory extends Factory
{
    protected $model = NutritionLog::class;

    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'log_date' => fake()->dateTimeBetween('-1 month', 'now'),
            'compliance' => fake()->randomElement(['complete', 'partial', 'missed']),
            'meals_logged' => fake()->numberBetween(0, 5),
            'meals_planned' => 5,
            'notes' => fake()->sentence(),
        ];
    }
}
