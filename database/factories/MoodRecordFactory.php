<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\MoodRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MoodRecord>
 */
class MoodRecordFactory extends Factory
{
    protected $model = MoodRecord::class;

    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'week_start' => fake()->dateTimeBetween('-2 months', '-1 month'),
            'week_end' => fake()->dateTimeBetween('-1 month', 'now'),
            'mood_level' => fake()->numberBetween(1, 10),
            'energy_level' => fake()->numberBetween(1, 10),
            'motivation_level' => fake()->numberBetween(1, 10),
            'notes' => fake()->sentence(),
        ];
    }
}
