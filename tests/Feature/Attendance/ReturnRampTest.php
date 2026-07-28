<?php

use App\Models\Attendance;
use App\Models\Client;
use App\Models\User;

test('returnRampRecommended is true when the last two attendances are 10 days apart and the most recent was 1 day ago', function () {
    $client = Client::factory()->create();
    Attendance::factory()->for($client)->create(['attendance_date' => now()->subDays(11)]);
    Attendance::factory()->for($client)->create(['attendance_date' => now()->subDay()]);

    expect($client->returnRampRecommended())->toBeTrue();
});

test('returnRampRecommended is false when the 10-day gap happened long ago, not a fresh comeback', function () {
    $client = Client::factory()->create();
    Attendance::factory()->for($client)->create(['attendance_date' => now()->subDays(40)]);
    Attendance::factory()->for($client)->create(['attendance_date' => now()->subDays(30)]);

    expect($client->returnRampRecommended())->toBeFalse();
});

test('returnRampRecommended is false with consistent attendance under 7 days apart', function () {
    $client = Client::factory()->create();
    Attendance::factory()->for($client)->create(['attendance_date' => now()->subDays(5)]);
    Attendance::factory()->for($client)->create(['attendance_date' => now()->subDay()]);

    expect($client->returnRampRecommended())->toBeFalse();
});

test('returnRampRecommended is false with fewer than 2 attendance records', function () {
    $client = Client::factory()->create();
    Attendance::factory()->for($client)->create(['attendance_date' => now()->subDay()]);

    expect($client->returnRampRecommended())->toBeFalse();
});

test('the client-show attendance tab renders the return ramp banner when recommended', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();
    Attendance::factory()->for($client)->create(['attendance_date' => now()->subDays(11)]);
    Attendance::factory()->for($client)->create(['attendance_date' => now()->subDay()]);

    $this->actingAs($trainer)
        ->get(route('clients.show', $client))
        ->assertOk()
        ->assertSee(__('clients.return_ramp.recommended_text'));
});

test('the client-show attendance tab does not render the return ramp banner when not recommended', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();

    $this->actingAs($trainer)
        ->get(route('clients.show', $client))
        ->assertOk()
        ->assertDontSee(__('clients.return_ramp.recommended_text'));
});
