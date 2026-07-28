<?php

use App\Livewire\Routine\RoutineForm;
use App\Livewire\Routine\RoutineList;
use App\Livewire\Routine\RoutineShow;
use App\Models\Client;
use App\Models\Routine;
use App\Models\User;
use Livewire\Livewire;

test('guest is redirected to login from the routines pages', function () {
    $client = Client::factory()->create();
    $routine = Routine::factory()->for($client)->create();

    $this->get(route('clients.routines.index', $client))->assertRedirect('/login');
    $this->get(route('clients.routines.create', $client))->assertRedirect('/login');
    $this->get(route('clients.routines.show', [$client, $routine]))->assertRedirect('/login');
});

test('a trainer can create a routine with exercises for their own client', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();

    Livewire::actingAs($trainer)
        ->test(RoutineForm::class, ['client' => $client])
        ->set('name', 'Strength Phase 1')
        ->set('weekly_frequency', '4')
        ->set('start_date', '2026-07-01')
        ->call('save')
        ->assertHasNoErrors();

    $routine = Routine::sole();
    expect($routine->name)->toBe('Strength Phase 1');
    expect($routine->client_id)->toBe($client->id);

    Livewire::actingAs($trainer)
        ->test(RoutineShow::class, ['client' => $client, 'routine' => $routine])
        ->set('exercise_name', 'Bench Press')
        ->set('exercise_muscle_group', 'chest')
        ->set('exercise_sets', '3')
        ->set('exercise_reps_range', '8-12')
        ->set('exercise_rest_seconds', '90')
        ->call('saveExercise')
        ->assertHasNoErrors();

    $exercise = $routine->exercises()->sole();
    expect($exercise->name)->toBe('Bench Press');
});

test('a trainer gets a 403 on another trainer\'s client routine pages', function () {
    $trainer = User::factory()->create();
    $otherTrainer = User::factory()->create();
    $client = Client::factory()->for($otherTrainer, 'trainer')->create();
    $routine = Routine::factory()->for($client)->create();

    $this->actingAs($trainer)->get(route('clients.routines.index', $client))->assertForbidden();
    $this->actingAs($trainer)->get(route('clients.routines.create', $client))->assertForbidden();
    $this->actingAs($trainer)->get(route('clients.routines.show', [$client, $routine]))->assertForbidden();
});

test('a trainer can create a routine with a specific phase', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();

    Livewire::actingAs($trainer)
        ->test(RoutineForm::class, ['client' => $client])
        ->set('name', 'Strength Block')
        ->set('weekly_frequency', '4')
        ->set('start_date', '2026-07-01')
        ->set('phase', 'intensification')
        ->call('save')
        ->assertHasNoErrors();

    expect(Routine::sole()->phase)->toBe('intensification');
});

test('a trainer can edit an existing routine\'s phase', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();
    $routine = Routine::factory()->for($client)->create(['phase' => 'accumulation']);

    Livewire::actingAs($trainer)
        ->test(RoutineForm::class, ['client' => $client, 'routine' => $routine])
        ->set('phase', 'deload')
        ->call('save')
        ->assertHasNoErrors();

    expect($routine->fresh()->phase)->toBe('deload');
});

test('validation rejects an invalid phase value', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();

    Livewire::actingAs($trainer)
        ->test(RoutineForm::class, ['client' => $client])
        ->set('name', 'Bad Phase')
        ->set('weekly_frequency', '4')
        ->set('start_date', '2026-07-01')
        ->set('phase', 'bogus')
        ->call('save')
        ->assertHasErrors(['phase']);
});

test('routine list renders the phase badge for each routine', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();
    Routine::factory()->for($client)->create(['phase' => 'realization']);

    Livewire::actingAs($trainer)
        ->test(RoutineList::class, ['client' => $client])
        ->assertSee(__('routines.phases.realization'));
});

test('routine list shows exercise count and can delete a routine', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();
    $routine = Routine::factory()->for($client)->create();
    $routine->exercises()->createMany([
        ['name' => 'Squat', 'sets' => 3, 'reps_range' => '8-10'],
        ['name' => 'Lunge', 'sets' => 3, 'reps_range' => '10-12'],
    ]);

    Livewire::actingAs($trainer)
        ->test(RoutineList::class, ['client' => $client])
        ->assertViewHas('routines', fn ($routines) => $routines->first()->exercises_count === 2)
        ->call('delete', $routine->id);

    expect(Routine::find($routine->id))->toBeNull();
});
