<?php

use App\Models\Client;
use App\Models\PhysicalMetric;

test('bmi is auto-calculated from weight and height on save', function () {
    $client = Client::factory()->create();

    $metric = PhysicalMetric::factory()->for($client)->create([
        'weight_kg' => 78.5,
        'height_cm' => 175,
    ]);

    expect((float) $metric->bmi)->toBe(round(78.5 / (1.75 ** 2), 2));
});

test('bmi is recalculated whenever weight or height change', function () {
    $client = Client::factory()->create();

    $metric = PhysicalMetric::factory()->for($client)->create([
        'weight_kg' => 78.5,
        'height_cm' => 175,
    ]);

    $metric->update(['weight_kg' => 90]);

    expect((float) $metric->fresh()->bmi)->toBe(round(90 / (1.75 ** 2), 2));
});
