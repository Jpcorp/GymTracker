<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Client>
 */
class ClientFactory extends Factory
{
    protected $model = Client::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'birth_date' => fake()->dateTimeBetween('-60 years', '-18 years'),
            'gender' => fake()->randomElement(['male', 'female', 'other']),
            'start_date' => fake()->dateTimeBetween('-2 years', 'now'),
            'goal' => fake()->sentence(),
            'profile_photo' => null,
            'status' => 'active',
            'trainer_id' => User::factory(),
        ];
    }
}
