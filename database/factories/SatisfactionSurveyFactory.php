<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\SatisfactionSurvey;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SatisfactionSurvey>
 */
class SatisfactionSurveyFactory extends Factory
{
    protected $model = SatisfactionSurvey::class;

    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'survey_date' => fake()->dateTimeBetween('-1 month', 'now'),
            'overall_satisfaction' => fake()->numberBetween(1, 10),
            'trainer_satisfaction' => fake()->numberBetween(1, 10),
            'facilities_satisfaction' => fake()->numberBetween(1, 10),
            'routines_satisfaction' => fake()->numberBetween(1, 10),
            'comments' => fake()->sentence(),
        ];
    }
}
