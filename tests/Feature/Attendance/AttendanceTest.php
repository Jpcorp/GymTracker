<?php

use App\Livewire\Client\ClientShow;
use App\Models\Client;
use App\Models\User;
use Livewire\Livewire;

test('guest is redirected to login from the client show page', function () {
    $client = Client::factory()->create();

    $this->get(route('clients.show', $client))->assertRedirect('/login');
});

test('a trainer can log a check-in for their own client', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();

    Livewire::actingAs($trainer)
        ->test(ClientShow::class, ['client' => $client])
        ->set('attendance_date', '2026-07-26')
        ->set('check_in', '08:00')
        ->set('check_out', '09:00')
        ->set('session_type', 'personal')
        ->call('checkIn')
        ->assertHasNoErrors();

    $attendance = $client->attendances()->sole();

    expect($attendance->attendance_date->format('Y-m-d'))->toBe('2026-07-26');
    expect($attendance->session_type)->toBe('personal');
});

test('a trainer can log a second check-in the same day without error', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();

    $component = Livewire::actingAs($trainer)->test(ClientShow::class, ['client' => $client]);

    $component->set('attendance_date', '2026-07-26')->set('session_type', 'personal')->call('checkIn')->assertHasNoErrors();
    $component->set('attendance_date', '2026-07-26')->set('session_type', 'group')->call('checkIn')->assertHasNoErrors();

    expect($client->attendances()->count())->toBe(2);
});

test('a trainer gets a 403 logging attendance for another trainer\'s client', function () {
    $trainer = User::factory()->create();
    $otherTrainer = User::factory()->create();
    $client = Client::factory()->for($otherTrainer, 'trainer')->create();

    $this->actingAs($trainer)
        ->get(route('clients.show', $client))
        ->assertForbidden();
});

test('the monthly attendance percentage computes correctly for a known fixture', function () {
    $this->travelTo('2026-07-10 12:00:00');

    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();

    foreach (['2026-07-01', '2026-07-05', '2026-07-09'] as $date) {
        $client->attendances()->create(['attendance_date' => $date, 'session_type' => 'personal']);
    }

    $percentage = Livewire::actingAs($trainer)
        ->test(ClientShow::class, ['client' => $client])
        ->instance()
        ->monthlyAttendancePercentage();

    // 3 distinct days / 10 days elapsed = 30%
    expect($percentage)->toBe(30);
});

test('attendance list shows entries ordered most-recent-first', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();

    $client->attendances()->create(['attendance_date' => '2026-07-01', 'session_type' => 'personal']);
    $client->attendances()->create(['attendance_date' => '2026-07-20', 'session_type' => 'group']);
    $client->attendances()->create(['attendance_date' => '2026-07-10', 'session_type' => 'free']);

    $this->actingAs($trainer)
        ->get(route('clients.show', $client))
        ->assertSeeInOrder(['2026-07-20', '2026-07-10', '2026-07-01']);
});
