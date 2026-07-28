<?php

use App\Livewire\Routine\RoutineShow;
use App\Models\Client;
use App\Models\Exercise;
use App\Models\Routine;
use App\Models\User;
use Livewire\Livewire;

test('e1RM chart computes the Epley estimate from a known weight and reps fixture', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();
    $routine = Routine::factory()->for($client)->create();
    $exercise = Exercise::factory()->for($routine)->create();

    $exercise->workoutLogs()->create([
        'client_id' => $client->id,
        'workout_date' => '2026-07-01',
        'weight_kg' => 100,
        'completed_sets' => 3,
        'completed_reps' => '5',
        'rpe' => 7,
    ]);

    $component = Livewire::actingAs($trainer)
        ->test(RoutineShow::class, ['client' => $client, 'routine' => $routine]);

    $exercise->refresh()->load('workoutLogs');
    $data = $component->instance()->e1rmChartData($exercise);

    expect($data['labels'])->toBe(['2026-07-01']);
    expect($data['series'][0]['data'][0])->toEqualWithDelta(116.67, 0.01);
});

test('e1RM chart skips logs with no weight or an unparseable/zero rep count', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();
    $routine = Routine::factory()->for($client)->create();
    $exercise = Exercise::factory()->for($routine)->create();

    $exercise->workoutLogs()->create([
        'client_id' => $client->id, 'workout_date' => '2026-07-01',
        'weight_kg' => null, 'completed_reps' => '10',
    ]);
    $exercise->workoutLogs()->create([
        'client_id' => $client->id, 'workout_date' => '2026-07-08',
        'weight_kg' => 80, 'completed_reps' => '0',
    ]);

    $component = Livewire::actingAs($trainer)
        ->test(RoutineShow::class, ['client' => $client, 'routine' => $routine]);

    $exercise->refresh()->load('workoutLogs');
    $data = $component->instance()->e1rmChartData($exercise);

    expect($data['labels'])->toBe([]);
    expect($data['hasEnoughData'])->toBeFalse();
});

test('volume by muscle group sums weight * sets * reps per group and buckets null under Sin grupo', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();
    $routine = Routine::factory()->for($client)->create();

    $bench = Exercise::factory()->for($routine)->create(['muscle_group' => 'Pecho']);
    $curl = Exercise::factory()->for($routine)->create(['muscle_group' => null]);

    // Same ISO week (both Mondays of the week starting 2026-06-08).
    $bench->workoutLogs()->create([
        'client_id' => $client->id, 'workout_date' => '2026-06-08',
        'weight_kg' => 50, 'completed_sets' => 3, 'completed_reps' => '10',
    ]);
    $curl->workoutLogs()->create([
        'client_id' => $client->id, 'workout_date' => '2026-06-10',
        'weight_kg' => 20, 'completed_sets' => 4, 'completed_reps' => '8',
    ]);

    $component = Livewire::actingAs($trainer)
        ->test(RoutineShow::class, ['client' => $client, 'routine' => $routine]);

    $data = $component->instance()->volumeByMuscleGroupChartData();

    expect($data['type'])->toBe('bar');
    expect($data['labels'])->toBe(['Sem. 2026-06-08']);

    $series = collect($data['series'])->keyBy('name');
    expect($series['Pecho']['data'][0])->toBe(1500.0); // 50 * 3 * 10
    expect($series['Sin grupo']['data'][0])->toBe(640.0); // 20 * 4 * 8
});

test('RPE chart averages logs sharing the same workout date across exercises', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();
    $routine = Routine::factory()->for($client)->create();

    $squat = Exercise::factory()->for($routine)->create();
    $press = Exercise::factory()->for($routine)->create();

    $squat->workoutLogs()->create([
        'client_id' => $client->id, 'workout_date' => '2026-07-01', 'rpe' => 6,
    ]);
    $press->workoutLogs()->create([
        'client_id' => $client->id, 'workout_date' => '2026-07-01', 'rpe' => 8,
    ]);
    $squat->workoutLogs()->create([
        'client_id' => $client->id, 'workout_date' => '2026-07-08', 'rpe' => 9,
    ]);

    $component = Livewire::actingAs($trainer)
        ->test(RoutineShow::class, ['client' => $client, 'routine' => $routine]);

    $data = $component->instance()->rpeChartData();

    expect($data['labels'])->toBe(['2026-07-01', '2026-07-08']);
    expect($data['series'][0]['data'])->toBe([7.0, 9.0]);
    expect($data['hasEnoughData'])->toBeTrue();
});

test('RPE chart has not enough data with fewer than two distinct dates', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();
    $routine = Routine::factory()->for($client)->create();
    $exercise = Exercise::factory()->for($routine)->create();

    $exercise->workoutLogs()->create([
        'client_id' => $client->id, 'workout_date' => '2026-07-01', 'rpe' => 6,
    ]);

    $component = Livewire::actingAs($trainer)
        ->test(RoutineShow::class, ['client' => $client, 'routine' => $routine]);

    $data = $component->instance()->rpeChartData();

    expect($data['hasEnoughData'])->toBeFalse();
});
