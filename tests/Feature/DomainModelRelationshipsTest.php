<?php

use App\Models\Attendance;
use App\Models\BodyMeasurement;
use App\Models\BodyPhoto;
use App\Models\Client;
use App\Models\Evaluation;
use App\Models\Exercise;
use App\Models\MoodRecord;
use App\Models\NutritionLog;
use App\Models\PhysicalMetric;
use App\Models\Routine;
use App\Models\SatisfactionSurvey;
use App\Models\User;
use App\Models\WorkoutLog;

test('client belongs to a trainer and exposes its domain relations', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();

    expect($client->trainer)->toBeInstanceOf(User::class);
    expect($client->trainer->id)->toBe($trainer->id);
    expect($trainer->clients->first()->id)->toBe($client->id);

    Evaluation::factory()->for($client)->create();
    PhysicalMetric::factory()->for($client)->create();
    BodyMeasurement::factory()->for($client)->create();
    Routine::factory()->for($client)->create();
    Attendance::factory()->for($client)->create();
    MoodRecord::factory()->for($client)->create();
    NutritionLog::factory()->for($client)->create();
    SatisfactionSurvey::factory()->for($client)->create();
    BodyPhoto::factory()->for($client)->create();

    expect($client->evaluations)->toHaveCount(1);
    expect($client->physicalMetrics)->toHaveCount(1);
    expect($client->bodyMeasurements)->toHaveCount(1);
    expect($client->routines)->toHaveCount(1);
    expect($client->attendances)->toHaveCount(1);
    expect($client->moodRecords)->toHaveCount(1);
    expect($client->nutritionLogs)->toHaveCount(1);
    expect($client->satisfactionSurveys)->toHaveCount(1);
    expect($client->bodyPhotos)->toHaveCount(1);
});

test('evaluation belongs to a client and has many metrics, measurements and photos', function () {
    $client = Client::factory()->create();
    $evaluation = Evaluation::factory()->for($client)->create();

    PhysicalMetric::factory()->for($client)->for($evaluation)->create();
    BodyMeasurement::factory()->for($client)->for($evaluation)->create();
    BodyPhoto::factory()->for($client)->for($evaluation)->create();

    expect($evaluation->client->id)->toBe($client->id);
    expect($evaluation->physicalMetrics)->toHaveCount(1);
    expect($evaluation->bodyMeasurements)->toHaveCount(1);
    expect($evaluation->bodyPhotos)->toHaveCount(1);
});

test('physical metric belongs to a client and optionally to an evaluation', function () {
    $client = Client::factory()->create();
    $metric = PhysicalMetric::factory()->for($client)->create();

    expect($metric->client->id)->toBe($client->id);
    expect($metric->evaluation)->toBeNull();
});

test('body measurement belongs to a client and optionally to an evaluation', function () {
    $client = Client::factory()->create();
    $evaluation = Evaluation::factory()->for($client)->create();
    $measurement = BodyMeasurement::factory()->for($client)->for($evaluation)->create();

    expect($measurement->client->id)->toBe($client->id);
    expect($measurement->evaluation->id)->toBe($evaluation->id);
});

test('body photo belongs to a client and optionally to an evaluation', function () {
    $client = Client::factory()->create();
    $photo = BodyPhoto::factory()->for($client)->create();

    expect($photo->client->id)->toBe($client->id);
    expect($photo->evaluation)->toBeNull();
});

test('routine belongs to a client and has many exercises', function () {
    $client = Client::factory()->create();
    $routine = Routine::factory()->for($client)->create();
    $exercise = Exercise::factory()->for($routine)->create();

    expect($routine->client->id)->toBe($client->id);
    expect($routine->exercises->first()->id)->toBe($exercise->id);
});

test('exercise belongs to a routine and has many workout logs', function () {
    $routine = Routine::factory()->create();
    $exercise = Exercise::factory()->for($routine)->create();
    $client = Client::factory()->create();
    $log = WorkoutLog::factory()->for($client)->for($exercise)->create();

    expect($exercise->routine->id)->toBe($routine->id);
    expect($exercise->workoutLogs->first()->id)->toBe($log->id);
});

test('workout log belongs to both a client and an exercise', function () {
    $client = Client::factory()->create();
    $exercise = Exercise::factory()->create();
    $log = WorkoutLog::factory()->for($client)->for($exercise)->create();

    expect($log->client->id)->toBe($client->id);
    expect($log->exercise->id)->toBe($exercise->id);
});

test('attendance, mood record, nutrition log and satisfaction survey belong to a client', function () {
    $client = Client::factory()->create();

    expect(Attendance::factory()->for($client)->create()->client->id)->toBe($client->id);
    expect(MoodRecord::factory()->for($client)->create()->client->id)->toBe($client->id);
    expect(NutritionLog::factory()->for($client)->create()->client->id)->toBe($client->id);
    expect(SatisfactionSurvey::factory()->for($client)->create()->client->id)->toBe($client->id);
});
