<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Evaluation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Evaluation>
 */
class EvaluationFactory extends Factory
{
    protected $model = Evaluation::class;

    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'evaluation_number' => fake()->numberBetween(1, 12),
            'period_start' => fake()->dateTimeBetween('-3 months', '-1 month'),
            'period_end' => fake()->dateTimeBetween('-1 month', 'now'),
            'evaluated_at' => fake()->dateTimeBetween('-1 month', 'now'),
            'achievements_summary' => fake()->sentence(),
            'trainer_notes' => fake()->sentence(),
        ];
    }
}
