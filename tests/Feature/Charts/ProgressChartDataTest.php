<?php

use App\Livewire\Client\ClientShow;
use App\Models\Client;
use App\Models\PhysicalMetric;
use App\Models\User;
use Livewire\Livewire;

test('guest is redirected to login from the client show page', function () {
    $client = Client::factory()->create();

    $this->get(route('clients.show', $client))->assertRedirect('/login');
});

test('a trainer cannot view another trainer\'s client show page', function () {
    $trainer = User::factory()->create();
    $otherTrainer = User::factory()->create();
    $client = Client::factory()->for($otherTrainer, 'trainer')->create();

    $this->actingAs($trainer)
        ->get(route('clients.show', $client))
        ->assertForbidden();
});

test('progress chart data is ordered by date ascending with correct values per series', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();

    PhysicalMetric::factory()->for($client)->create([
        'recorded_at' => '2026-06-01',
        'weight_kg' => 80,
        'height_cm' => 175,
        'body_fat_percentage' => 20,
    ]);
    PhysicalMetric::factory()->for($client)->create([
        'recorded_at' => '2026-05-01',
        'weight_kg' => 82,
        'height_cm' => 175,
        'body_fat_percentage' => null,
    ]);
    PhysicalMetric::factory()->for($client)->create([
        'recorded_at' => '2026-07-01',
        'weight_kg' => 78,
        'height_cm' => 175,
        'body_fat_percentage' => 18,
    ]);

    $component = Livewire::actingAs($trainer)->test(ClientShow::class, ['client' => $client]);
    $data = $component->instance()->progressChartData();

    expect($data['labels'])->toBe(['2026-05-01', '2026-06-01', '2026-07-01']);
    expect($data['hasEnoughData'])->toBeTrue();

    $weightSeries = collect($data['series'])->firstWhere('name', 'Peso (kg)');
    expect($weightSeries['data'])->toBe([82.0, 80.0, 78.0]);

    $bodyFatSeries = collect($data['series'])->firstWhere('name', 'Grasa corporal (%)');
    expect($bodyFatSeries['data'])->toBe([null, 20.0, 18.0]);
});

test('a series that is null across every record is omitted from the chart payload', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();

    PhysicalMetric::factory()->for($client)->create([
        'recorded_at' => '2026-06-01',
        'weight_kg' => 80,
        'height_cm' => 175,
        'body_fat_percentage' => null,
    ]);
    PhysicalMetric::factory()->for($client)->create([
        'recorded_at' => '2026-07-01',
        'weight_kg' => 78,
        'height_cm' => 175,
        'body_fat_percentage' => null,
    ]);

    $component = Livewire::actingAs($trainer)->test(ClientShow::class, ['client' => $client]);
    $data = $component->instance()->progressChartData();

    $seriesNames = collect($data['series'])->pluck('name');
    expect($seriesNames)->not->toContain('Grasa corporal (%)');
    expect($seriesNames)->toContain('Peso (kg)');
});

test('a client with fewer than two physical metrics is flagged as not having enough chart data', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();

    PhysicalMetric::factory()->for($client)->create();

    $component = Livewire::actingAs($trainer)->test(ClientShow::class, ['client' => $client]);
    $data = $component->instance()->progressChartData();

    expect($data['hasEnoughData'])->toBeFalse();

    $component->assertSee('Aún no hay datos suficientes');
});
