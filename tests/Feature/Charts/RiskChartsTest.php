<?php

use App\Livewire\Client\ClientShow;
use App\Models\Client;
use App\Models\User;
use Livewire\Livewire;

test('ACWR computes correctly for a known attendance fixture', function () {
    $this->travelTo('2026-07-27 12:00:00');

    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();

    $currentWeekStart = now()->startOfWeek();
    $oneWeekAgoStart = $currentWeekStart->copy()->subWeek();

    // Current week and the week before it each get 4 distinct attendance days,
    // the two weeks before that stay empty: chronic = (4+4+0+0)/4 = 2, acute = 4 -> ratio 2.0.
    foreach ([$currentWeekStart, $oneWeekAgoStart] as $weekStart) {
        foreach (range(0, 3) as $offset) {
            $client->attendances()->create([
                'attendance_date' => $weekStart->copy()->addDays($offset)->format('Y-m-d'),
                'session_type' => 'personal',
            ]);
        }
    }

    $data = Livewire::actingAs($trainer)
        ->test(ClientShow::class, ['client' => $client])
        ->instance()
        ->acwrChartData();

    expect($data['hasEnoughData'])->toBeTrue();
    expect(end($data['series'][0]['data']))->toBe(2.0);
});

test('a brand-new client with no attendance history has ACWR hasEnoughData false', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();

    $data = Livewire::actingAs($trainer)
        ->test(ClientShow::class, ['client' => $client])
        ->instance()
        ->acwrChartData();

    expect($data['hasEnoughData'])->toBeFalse();
});

test('symmetry chart pulls values from the most recent body measurement', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();

    $client->bodyMeasurements()->create([
        'recorded_at' => '2026-01-01',
        'right_arm_cm' => 30,
        'left_arm_cm' => 29,
        'right_thigh_cm' => 55,
        'left_thigh_cm' => 54,
    ]);

    $client->bodyMeasurements()->create([
        'recorded_at' => '2026-06-01',
        'right_arm_cm' => 35,
        'left_arm_cm' => 34,
        'right_thigh_cm' => 60,
        'left_thigh_cm' => 58,
    ]);

    $data = Livewire::actingAs($trainer)
        ->test(ClientShow::class, ['client' => $client])
        ->instance()
        ->symmetryChartData();

    expect($data['hasEnoughData'])->toBeTrue();
    expect($data['series'][0]['data'])->toBe([35.0, 60.0]);
    expect($data['series'][1]['data'])->toBe([34.0, 58.0]);
});

test('a client with no body measurement has symmetry hasEnoughData false', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();

    $data = Livewire::actingAs($trainer)
        ->test(ClientShow::class, ['client' => $client])
        ->instance()
        ->symmetryChartData();

    expect($data['hasEnoughData'])->toBeFalse();
});
