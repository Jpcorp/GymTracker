<?php

use App\Models\Client;
use App\Models\Evaluation;
use App\Models\PhysicalMetric;

test('command creates the first evaluation for a client whose start_date was 21+ days ago', function () {
    $client = Client::factory()->create([
        'status' => 'active',
        'start_date' => today()->subDays(21),
    ]);

    $this->artisan('evaluations:generate')->expectsOutputToContain('Generated 1 evaluations.');

    $evaluation = Evaluation::sole();

    expect($evaluation->client_id)->toBe($client->id);
    expect($evaluation->evaluation_number)->toBe(1);
    expect($evaluation->period_start->toDateString())->toBe($client->start_date->toDateString());
    expect($evaluation->period_end->toDateString())->toBe($client->start_date->copy()->addDays(20)->toDateString());
    expect($evaluation->evaluated_at->toDateString())->toBe(today()->toDateString());
});

test('command does not create an evaluation for a client whose start_date was less than 21 days ago', function () {
    Client::factory()->create([
        'status' => 'active',
        'start_date' => today()->subDays(10),
    ]);

    $this->artisan('evaluations:generate')->expectsOutputToContain('Generated 0 evaluations.');

    expect(Evaluation::count())->toBe(0);
});

test('command does not duplicate an evaluation when run twice for the same period', function () {
    Client::factory()->create([
        'status' => 'active',
        'start_date' => today()->subDays(25),
    ]);

    $this->artisan('evaluations:generate');
    $this->artisan('evaluations:generate');

    expect(Evaluation::count())->toBe(1);
});

test('command computes evaluation_number/period_start/period_end for a second evaluation', function () {
    $client = Client::factory()->create([
        'status' => 'active',
        'start_date' => today()->subDays(60),
    ]);

    $first = Evaluation::factory()->for($client)->create([
        'evaluation_number' => 1,
        'period_start' => $client->start_date,
        'period_end' => $client->start_date->copy()->addDays(20),
        'evaluated_at' => $client->start_date->copy()->addDays(20),
    ]);

    $this->artisan('evaluations:generate');

    expect(Evaluation::count())->toBe(2);

    $second = Evaluation::where('id', '!=', $first->id)->sole();

    expect($second->evaluation_number)->toBe(2);
    expect($second->period_start->toDateString())->toBe($first->period_end->copy()->addDay()->toDateString());
    expect($second->period_end->toDateString())->toBe($second->period_start->copy()->addDays(20)->toDateString());
});

test('a metric recorded inside the new period gets its evaluation_id backfilled after the command runs', function () {
    $client = Client::factory()->create([
        'status' => 'active',
        'start_date' => today()->subDays(21),
    ]);

    $metric = PhysicalMetric::factory()->for($client)->create([
        'recorded_at' => $client->start_date->copy()->addDays(5),
        'evaluation_id' => null,
    ]);

    $this->artisan('evaluations:generate');

    $evaluation = Evaluation::sole();

    expect($metric->fresh()->evaluation_id)->toBe($evaluation->id);
});
