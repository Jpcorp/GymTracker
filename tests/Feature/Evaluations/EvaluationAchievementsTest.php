<?php

use App\Livewire\Client\ClientShow;
use App\Models\Attendance;
use App\Models\Client;
use App\Models\Evaluation;
use App\Models\Exercise;
use App\Models\NutritionLog;
use App\Models\Routine;
use App\Models\User;
use App\Models\WorkoutLog;
use Livewire\Livewire;

test('a workout log improving on a prior max weight is reported as a new record', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();
    $routine = Routine::factory()->for($client)->create();
    $exercise = Exercise::factory()->for($routine)->create(['name' => 'Press de Banca']);

    $evaluation = Evaluation::factory()->for($client)->create([
        'evaluation_number' => 1,
        'period_start' => today()->subDays(20),
        'period_end' => today(),
    ]);

    WorkoutLog::factory()->for($client)->for($exercise)->create([
        'workout_date' => today()->subDays(30),
        'weight_kg' => 80,
    ]);
    WorkoutLog::factory()->for($client)->for($exercise)->create([
        'workout_date' => today()->subDays(5),
        'weight_kg' => 85,
    ]);

    $row = $client->evaluationsWithComparison()->firstWhere('evaluation.id', $evaluation->id);

    expect($row['achievements'])->toContain('Nuevo récord en Press de Banca: 85 kg (+5 kg)');
});

test('a first-time exercise log with no prior weight is not reported as a record', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();
    $routine = Routine::factory()->for($client)->create();
    $exercise = Exercise::factory()->for($routine)->create(['name' => 'Sentadilla']);

    $evaluation = Evaluation::factory()->for($client)->create([
        'evaluation_number' => 1,
        'period_start' => today()->subDays(20),
        'period_end' => today(),
    ]);

    WorkoutLog::factory()->for($client)->for($exercise)->create([
        'workout_date' => today()->subDays(5),
        'weight_kg' => 60,
    ]);

    $row = $client->evaluationsWithComparison()->firstWhere('evaluation.id', $evaluation->id);

    expect($row['achievements'])->toBeEmpty();
});

test('attendance percentage is computed correctly for the evaluation period', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();

    $evaluation = Evaluation::factory()->for($client)->create([
        'evaluation_number' => 1,
        'period_start' => today()->subDays(6),
        'period_end' => today(),
    ]);

    // 7-day period, 4 distinct attendance days => round(4/7*100) = 57%
    foreach ([0, 1, 2, 3] as $offset) {
        Attendance::factory()->for($client)->create([
            'attendance_date' => today()->subDays($offset),
        ]);
    }

    $row = $client->evaluationsWithComparison()->firstWhere('evaluation.id', $evaluation->id);

    expect($row['achievements'])->toContain('Asistencia del 57% (4 de 7 días posibles)');
});

test('nutrition compliance percentage is computed correctly for the evaluation period', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();

    $evaluation = Evaluation::factory()->for($client)->create([
        'evaluation_number' => 1,
        'period_start' => today()->subDays(6),
        'period_end' => today(),
    ]);

    // (4/5 + 5/5) / 2 = 0.9 => 90%
    NutritionLog::factory()->for($client)->create([
        'log_date' => today()->subDays(1),
        'meals_logged' => 4,
        'meals_planned' => 5,
    ]);
    NutritionLog::factory()->for($client)->create([
        'log_date' => today()->subDays(2),
        'meals_logged' => 5,
        'meals_planned' => 5,
    ]);

    $row = $client->evaluationsWithComparison()->firstWhere('evaluation.id', $evaluation->id);

    expect($row['achievements'])->toContain('Cumplimiento alimentario promedio: 90%');
});

test('an evaluation period with no logs at all yields an empty achievements list and the page still renders', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();

    Evaluation::factory()->for($client)->create([
        'evaluation_number' => 1,
        'period_start' => today()->subDays(20),
        'period_end' => today(),
    ]);

    $row = $client->evaluationsWithComparison()->first();

    expect($row['achievements'])->toBeEmpty();

    Livewire::actingAs($trainer)
        ->test(ClientShow::class, ['client' => $client])
        ->assertOk();
});
