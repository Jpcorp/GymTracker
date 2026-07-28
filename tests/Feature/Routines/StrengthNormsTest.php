<?php

use App\Livewire\Routine\RoutineShow;
use App\Models\Client;
use App\Models\Exercise;
use App\Models\PhysicalMetric;
use App\Models\Routine;
use App\Models\User;
use Livewire\Livewire;

test('an exercise matching a known lift classifies at the expected strength level for its bodyweight ratio', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();
    PhysicalMetric::factory()->for($client)->create(['recorded_at' => now(), 'weight_kg' => 80]);
    $routine = Routine::factory()->for($client)->create();
    $exercise = Exercise::factory()->for($routine)->create(['name' => 'Sentadilla']);

    $component = Livewire::actingAs($trainer)
        ->test(RoutineShow::class, ['client' => $client, 'routine' => $routine]);

    // e1rm 120kg / bodyweight 80kg = 1.5 ratio -> squat thresholds [1.0, 1.5, 2.0) -> advanced
    expect($component->instance()->strengthLevel($exercise, 120.0))->toBe('advanced');
});

test('an exercise with no matching lift category returns null', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();
    PhysicalMetric::factory()->for($client)->create(['recorded_at' => now(), 'weight_kg' => 80]);
    $routine = Routine::factory()->for($client)->create();
    $exercise = Exercise::factory()->for($routine)->create(['name' => 'Curl de Bíceps']);

    $component = Livewire::actingAs($trainer)
        ->test(RoutineShow::class, ['client' => $client, 'routine' => $routine]);

    expect($component->instance()->strengthLevel($exercise, 40.0))->toBeNull();
});

test('a client with no physical metric on record cannot be classified', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();
    $routine = Routine::factory()->for($client)->create();
    $exercise = Exercise::factory()->for($routine)->create(['name' => 'Sentadilla']);

    $component = Livewire::actingAs($trainer)
        ->test(RoutineShow::class, ['client' => $client, 'routine' => $routine]);

    expect($component->instance()->strengthLevel($exercise, 120.0))->toBeNull();
});

test('an exercise whose most recent workout log is 5+ weeks old is flagged as stale', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();
    $routine = Routine::factory()->for($client)->create();
    $stale = Exercise::factory()->for($routine)->create();
    $fresh = Exercise::factory()->for($routine)->create();

    $stale->workoutLogs()->create([
        'client_id' => $client->id,
        'workout_date' => now()->subWeeks(5)->format('Y-m-d'),
        'weight_kg' => 100, 'completed_reps' => '5',
    ]);
    $fresh->workoutLogs()->create([
        'client_id' => $client->id,
        'workout_date' => now()->subDay()->format('Y-m-d'),
        'weight_kg' => 100, 'completed_reps' => '5',
    ]);

    $component = Livewire::actingAs($trainer)
        ->test(RoutineShow::class, ['client' => $client, 'routine' => $routine]);

    $exercises = $routine->exercises()
        ->with(['workoutLogs' => fn ($query) => $query->orderByDesc('workout_date')->orderByDesc('id')])
        ->get();

    $staleIds = $component->instance()->staleExercises($exercises);

    expect($staleIds)->toContain($stale->id);
    expect($staleIds)->not->toContain($fresh->id);
});
