<?php

use App\Livewire\Routine\RoutineShow;
use App\Models\Client;
use App\Models\Exercise;
use App\Models\Routine;
use App\Models\User;
use Livewire\Livewire;

test('a trainer can log a workout entry for an exercise and it shows in the exercise history', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();
    $routine = Routine::factory()->for($client)->create();
    $exercise = Exercise::factory()->for($routine)->create(['name' => 'Deadlift']);

    Livewire::actingAs($trainer)
        ->test(RoutineShow::class, ['client' => $client, 'routine' => $routine])
        ->call('startLogging', $exercise->id)
        ->set('workout_date', '2026-07-01')
        ->set('weight_kg', '60')
        ->set('completed_sets', '3')
        ->set('completed_reps', '10')
        ->set('rpe', '7')
        ->call('logWorkout')
        ->assertHasNoErrors();

    $log = $exercise->workoutLogs()->sole();
    expect((float) $log->weight_kg)->toBe(60.0);
    expect($log->client_id)->toBe($client->id);
});

test('a second workout log on a later date shows expected weight progression ordered desc', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();
    $routine = Routine::factory()->for($client)->create();
    $exercise = Exercise::factory()->for($routine)->create();

    $component = Livewire::actingAs($trainer)
        ->test(RoutineShow::class, ['client' => $client, 'routine' => $routine]);

    $component->call('startLogging', $exercise->id)
        ->set('workout_date', '2026-07-01')
        ->set('weight_kg', '60')
        ->call('logWorkout')
        ->assertHasNoErrors();

    $component->call('startLogging', $exercise->id)
        ->set('workout_date', '2026-07-08')
        ->set('weight_kg', '65')
        ->call('logWorkout')
        ->assertHasNoErrors();

    expect($exercise->workoutLogs()->count())->toBe(2);

    $history = $exercise->workoutLogs()->orderByDesc('workout_date')->get();

    expect($history->pluck('workout_date')->map(fn ($d) => $d->format('Y-m-d'))->all())
        ->toBe(['2026-07-08', '2026-07-01']);
    expect($history->pluck('weight_kg')->map(fn ($w) => (float) $w)->all())
        ->toBe([65.0, 60.0]);

    $component->assertSeeInOrder(['2026-07-08', '2026-07-01']);
});
