<?php

namespace Database\Factories;

use App\Models\BodyMeasurement;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BodyMeasurement>
 */
class BodyMeasurementFactory extends Factory
{
    protected $model = BodyMeasurement::class;

    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'recorded_at' => fake()->dateTimeBetween('-6 months', 'now'),
            'waist_cm' => fake()->randomFloat(2, 60, 120),
            'hips_cm' => fake()->randomFloat(2, 60, 130),
            'chest_cm' => fake()->randomFloat(2, 70, 130),
            'right_arm_cm' => fake()->randomFloat(2, 20, 45),
            'left_arm_cm' => fake()->randomFloat(2, 20, 45),
            'right_thigh_cm' => fake()->randomFloat(2, 40, 70),
            'left_thigh_cm' => fake()->randomFloat(2, 40, 70),
            'evaluation_id' => null,
        ];
    }
}
