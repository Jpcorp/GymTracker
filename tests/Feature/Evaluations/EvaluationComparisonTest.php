<?php

use App\Livewire\Client\ClientShow;
use App\Models\Client;
use App\Models\Evaluation;
use App\Models\PhysicalMetric;
use App\Models\User;
use Livewire\Livewire;

test('the comparison view shows the correct delta between two evaluations weight_kg', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();

    $first = Evaluation::factory()->for($client)->create([
        'evaluation_number' => 1,
        'period_start' => today()->subDays(41),
        'period_end' => today()->subDays(21),
    ]);
    $second = Evaluation::factory()->for($client)->create([
        'evaluation_number' => 2,
        'period_start' => today()->subDays(20),
        'period_end' => today(),
    ]);

    PhysicalMetric::factory()->for($client)->for($first, 'evaluation')->create([
        'recorded_at' => $first->period_start,
        'weight_kg' => 80.0,
        'height_cm' => 175,
    ]);
    PhysicalMetric::factory()->for($client)->for($second, 'evaluation')->create([
        'recorded_at' => $second->period_start,
        'weight_kg' => 78.5,
        'height_cm' => 175,
    ]);

    Livewire::actingAs($trainer)
        ->test(ClientShow::class, ['client' => $client])
        ->assertSee('-1.5');
});

test('the first evaluation with no previous one shows raw values with no delta', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();

    $evaluation = Evaluation::factory()->for($client)->create(['evaluation_number' => 1]);

    PhysicalMetric::factory()->for($client)->for($evaluation, 'evaluation')->create([
        'recorded_at' => $evaluation->period_start,
        'weight_kg' => 80.0,
        'height_cm' => 175,
    ]);

    Livewire::actingAs($trainer)
        ->test(ClientShow::class, ['client' => $client])
        ->assertSee('80')
        ->assertDontSee('Delta');
});

test('a trainer cannot see another trainer\'s client evaluations', function () {
    $trainer = User::factory()->create();
    $otherTrainer = User::factory()->create();
    $client = Client::factory()->for($otherTrainer, 'trainer')->create();
    Evaluation::factory()->for($client)->create();

    $this->actingAs($trainer)
        ->get(route('clients.show', $client))
        ->assertForbidden();
});
