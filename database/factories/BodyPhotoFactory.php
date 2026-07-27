<?php

namespace Database\Factories;

use App\Models\BodyPhoto;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BodyPhoto>
 */
class BodyPhotoFactory extends Factory
{
    protected $model = BodyPhoto::class;

    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'photo_date' => fake()->dateTimeBetween('-1 month', 'now'),
            'view_type' => fake()->randomElement(['front', 'back', 'left_side', 'right_side']),
            'photo_path' => 'photos/'.fake()->uuid().'.jpg',
            'evaluation_id' => null,
            'notes' => fake()->sentence(),
        ];
    }
}
