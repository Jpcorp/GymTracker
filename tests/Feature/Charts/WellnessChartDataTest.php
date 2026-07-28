<?php

use App\Livewire\Client\ClientShow;
use App\Models\Client;
use App\Models\MoodRecord;
use App\Models\NutritionLog;
use App\Models\User;
use Livewire\Livewire;

test('mood chart data is ordered by week_start ascending with correct values per series', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();

    MoodRecord::factory()->for($client)->create([
        'week_start' => '2026-06-01',
        'week_end' => '2026-06-07',
        'mood_level' => 8,
        'energy_level' => 7,
        'motivation_level' => 6,
    ]);
    MoodRecord::factory()->for($client)->create([
        'week_start' => '2026-05-01',
        'week_end' => '2026-05-07',
        'mood_level' => 5,
        'energy_level' => 4,
        'motivation_level' => 3,
    ]);

    $component = Livewire::actingAs($trainer)->test(ClientShow::class, ['client' => $client]);
    $data = $component->instance()->moodChartData();

    expect($data['labels'])->toBe(['2026-05-01', '2026-06-01']);
    expect($data['hasEnoughData'])->toBeTrue();

    $moodSeries = collect($data['series'])->firstWhere('name', 'Ánimo (1-10)');
    expect($moodSeries['data'])->toBe([5.0, 8.0]);

    $energySeries = collect($data['series'])->firstWhere('name', 'Energía (1-10)');
    expect($energySeries['data'])->toBe([4.0, 7.0]);
});

test('a mood series that is null across every record is omitted from the chart payload', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();

    MoodRecord::factory()->for($client)->create([
        'week_start' => '2026-06-01',
        'mood_level' => 8,
        'motivation_level' => null,
    ]);
    MoodRecord::factory()->for($client)->create([
        'week_start' => '2026-07-01',
        'mood_level' => 6,
        'motivation_level' => null,
    ]);

    $component = Livewire::actingAs($trainer)->test(ClientShow::class, ['client' => $client]);
    $data = $component->instance()->moodChartData();

    $seriesNames = collect($data['series'])->pluck('name');
    expect($seriesNames)->not->toContain('Motivación (1-10)');
    expect($seriesNames)->toContain('Ánimo (1-10)');
});

test('sleep_hours series appears in the mood chart when at least two records have it set', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();

    MoodRecord::factory()->for($client)->create([
        'week_start' => '2026-06-01',
        'mood_level' => 8,
        'sleep_hours' => 6.5,
    ]);
    MoodRecord::factory()->for($client)->create([
        'week_start' => '2026-07-01',
        'mood_level' => 6,
        'sleep_hours' => 7.5,
    ]);

    $component = Livewire::actingAs($trainer)->test(ClientShow::class, ['client' => $client]);
    $data = $component->instance()->moodChartData();

    $sleepSeries = collect($data['series'])->firstWhere('name', 'Horas de sueño');
    expect($sleepSeries)->not->toBeNull();
    expect($sleepSeries['data'])->toBe([6.5, 7.5]);
});

test('sleep_hours series is omitted from the mood chart when no record has it set', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();

    MoodRecord::factory()->for($client)->create([
        'week_start' => '2026-06-01',
        'mood_level' => 8,
        'sleep_hours' => null,
    ]);
    MoodRecord::factory()->for($client)->create([
        'week_start' => '2026-07-01',
        'mood_level' => 6,
        'sleep_hours' => null,
    ]);

    $component = Livewire::actingAs($trainer)->test(ClientShow::class, ['client' => $client]);
    $data = $component->instance()->moodChartData();

    $seriesNames = collect($data['series'])->pluck('name');
    expect($seriesNames)->not->toContain('Horas de sueño');
});

test('a client with fewer than two mood records is flagged as not having enough chart data', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();

    MoodRecord::factory()->for($client)->create();

    $component = Livewire::actingAs($trainer)->test(ClientShow::class, ['client' => $client]);
    $data = $component->instance()->moodChartData();

    expect($data['hasEnoughData'])->toBeFalse();

    $component->assertSee('Aún no hay datos suficientes');
});

test('nutrition chart data computes the expected compliance percentage', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();

    NutritionLog::factory()->for($client)->create([
        'log_date' => '2026-06-01',
        'compliance' => 'partial',
        'meals_logged' => 3,
        'meals_planned' => 5,
    ]);
    NutritionLog::factory()->for($client)->create([
        'log_date' => '2026-07-01',
        'compliance' => 'missed',
        'meals_logged' => 0,
        'meals_planned' => 0,
    ]);

    $component = Livewire::actingAs($trainer)->test(ClientShow::class, ['client' => $client]);
    $data = $component->instance()->nutritionChartData();

    expect($data['labels'])->toBe(['2026-06-01', '2026-07-01']);
    expect($data['hasEnoughData'])->toBeTrue();
    expect($data['series'][0]['data'])->toBe([60.0, 0.0]);
});

test('a client with fewer than two nutrition logs is flagged as not having enough chart data', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();

    NutritionLog::factory()->for($client)->create();

    $component = Livewire::actingAs($trainer)->test(ClientShow::class, ['client' => $client]);
    $data = $component->instance()->nutritionChartData();

    expect($data['hasEnoughData'])->toBeFalse();
});
