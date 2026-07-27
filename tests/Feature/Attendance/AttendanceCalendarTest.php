<?php

use App\Livewire\Client\ClientShow;
use App\Models\Client;
use App\Models\User;
use Livewire\Livewire;

test('a trainer sees the current month calendar with an attended day marked', function () {
    $this->travelTo('2026-07-26 12:00:00');

    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();
    $client->attendances()->create(['attendance_date' => '2026-07-09', 'session_type' => 'personal']);

    $component = Livewire::actingAs($trainer)->test(ClientShow::class, ['client' => $client]);

    expect($component->get('calendarYear'))->toBe(2026);
    expect($component->get('calendarMonth'))->toBe(7);

    $weeks = $component->instance()->calendarWeeks();
    $attendedDay = collect($weeks)->flatten(1)->first(fn ($day) => $day['date']->format('Y-m-d') === '2026-07-09');
    $otherDay = collect($weeks)->flatten(1)->first(fn ($day) => $day['date']->format('Y-m-d') === '2026-07-10');

    expect($attendedDay['attended'])->toBeTrue();
    expect($otherDay['attended'])->toBeFalse();
});

test('a trainer can navigate to the previous and next month', function () {
    $this->travelTo('2026-07-26 12:00:00');

    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();

    $component = Livewire::actingAs($trainer)->test(ClientShow::class, ['client' => $client]);

    $component->call('previousMonth');
    expect($component->get('calendarYear'))->toBe(2026);
    expect($component->get('calendarMonth'))->toBe(6);

    $component->call('nextMonth')->call('nextMonth');
    expect($component->get('calendarYear'))->toBe(2026);
    expect($component->get('calendarMonth'))->toBe(8);
});

test('a trainer gets a 403 viewing the calendar for another trainer\'s client', function () {
    $trainer = User::factory()->create();
    $otherTrainer = User::factory()->create();
    $client = Client::factory()->for($otherTrainer, 'trainer')->create();

    $this->actingAs($trainer)
        ->get(route('clients.show', $client))
        ->assertForbidden();
});
