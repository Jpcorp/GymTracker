<?php

use App\Models\Client;
use App\Models\User;
use App\Notifications\ClientAbsenceAlert;
use Illuminate\Support\Facades\Notification;

test('a client whose last attendance was exactly 7 days ago triggers an alert to their trainer', function () {
    $this->travelTo('2026-07-27 12:00:00');
    Notification::fake();

    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create(['start_date' => '2025-01-01']);
    $client->attendances()->create(['attendance_date' => '2026-07-20', 'session_type' => 'personal']);

    $this->artisan('attendance:alert-absences')->assertSuccessful();

    Notification::assertSentTo($trainer, ClientAbsenceAlert::class);
});

test('a client with no attendance triggers an alert exactly 7 days after their start date', function () {
    $this->travelTo('2026-07-27 12:00:00');
    Notification::fake();

    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create(['start_date' => '2026-07-20']);

    $this->artisan('attendance:alert-absences')->assertSuccessful();

    Notification::assertSentTo($trainer, ClientAbsenceAlert::class);
});

test('a client who attended yesterday does not trigger an alert', function () {
    $this->travelTo('2026-07-27 12:00:00');
    Notification::fake();

    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create(['start_date' => '2025-01-01']);
    $client->attendances()->create(['attendance_date' => '2026-07-26', 'session_type' => 'personal']);

    $this->artisan('attendance:alert-absences')->assertSuccessful();

    Notification::assertNothingSent();
});

test('a client absent 6 or 8 days does not trigger an alert', function () {
    $this->travelTo('2026-07-27 12:00:00');
    Notification::fake();

    $trainer = User::factory()->create();

    $sixDays = Client::factory()->for($trainer, 'trainer')->create(['start_date' => '2025-01-01']);
    $sixDays->attendances()->create(['attendance_date' => '2026-07-21', 'session_type' => 'personal']);

    $eightDays = Client::factory()->for($trainer, 'trainer')->create(['start_date' => '2025-01-01']);
    $eightDays->attendances()->create(['attendance_date' => '2026-07-19', 'session_type' => 'personal']);

    $this->artisan('attendance:alert-absences')->assertSuccessful();

    Notification::assertNothingSent();
});
