<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Attendance>
 */
class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'attendance_date' => fake()->dateTimeBetween('-1 month', 'now'),
            'check_in' => '08:00:00',
            'check_out' => '09:00:00',
            'session_type' => fake()->randomElement(['personal', 'group', 'free']),
            'duration_minutes' => 60,
        ];
    }
}
