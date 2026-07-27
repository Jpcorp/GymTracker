<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\PhysicalMetric;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PhysicalMetric>
 */
class PhysicalMetricFactory extends Factory
{
    protected $model = PhysicalMetric::class;

    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'recorded_at' => fake()->dateTimeBetween('-6 months', 'now'),
            'weight_kg' => fake()->randomFloat(2, 50, 120),
            'height_cm' => fake()->randomFloat(2, 150, 200),
            'body_fat_percentage' => fake()->randomFloat(2, 5, 40),
            'metabolic_age' => fake()->numberBetween(18, 60),
            'basal_kcal' => fake()->numberBetween(1200, 2500),
            'visceral_fat' => fake()->numberBetween(1, 20),
            'evaluation_id' => null,
        ];
    }
}
