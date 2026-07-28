<?php

use App\Livewire\Client\ClientForm;
use App\Livewire\Client\ClientShow;
use App\Models\Client;
use App\Models\PhysicalMetric;
use App\Models\User;
use Livewire\Livewire;

test('a trainer can set a SMART goal when creating a client', function () {
    $trainer = User::factory()->create();

    Livewire::actingAs($trainer)
        ->test(ClientForm::class)
        ->set('name', 'Jane Doe')
        ->set('email', 'jane@example.com')
        ->set('birth_date', '1990-01-01')
        ->set('gender', 'female')
        ->set('start_date', '2026-01-01')
        ->set('goal', 'Lose weight')
        ->set('goal_metric', 'weight_kg')
        ->set('goal_target_value', '80')
        ->set('goal_target_date', '2026-12-31')
        ->call('save')
        ->assertHasNoErrors();

    $client = Client::sole();

    expect($client->goal_metric)->toBe('weight_kg');
    expect((float) $client->goal_target_value)->toBe(80.0);
    expect($client->goal_target_date->format('Y-m-d'))->toBe('2026-12-31');
});

test('a trainer can set a SMART goal when editing a client', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();

    Livewire::actingAs($trainer)
        ->test(ClientForm::class, ['client' => $client])
        ->set('goal_metric', 'body_fat_percentage')
        ->set('goal_target_value', '15')
        ->set('goal_target_date', '2026-10-01')
        ->call('save')
        ->assertHasNoErrors();

    $client->refresh();

    expect($client->goal_metric)->toBe('body_fat_percentage');
    expect((float) $client->goal_target_value)->toBe(15.0);
});

test('goalProgress computes the correct percent for a known baseline/current/target', function () {
    $client = Client::factory()->create([
        'goal_metric' => 'weight_kg',
        'goal_target_value' => 80,
        'goal_target_date' => now()->addDays(12)->format('Y-m-d'),
    ]);

    PhysicalMetric::factory()->for($client)->create(['recorded_at' => now()->subDays(20), 'weight_kg' => 90]);
    PhysicalMetric::factory()->for($client)->create(['recorded_at' => now(), 'weight_kg' => 85]);

    $progress = $client->goalProgress();

    expect($progress)->not->toBeNull();
    expect($progress['baseline'])->toBe(90.0);
    expect($progress['current'])->toBe(85.0);
    expect($progress['target'])->toBe(80.0);
    expect($progress['percent'])->toBe(50);
});

test('goalProgress returns null when no goal metric/target is set', function () {
    $client = Client::factory()->create(['goal_metric' => null, 'goal_target_value' => null]);

    PhysicalMetric::factory()->for($client)->create(['weight_kg' => 90]);

    expect($client->goalProgress())->toBeNull();
});

test('goalProgress returns null when the client has no PhysicalMetric data for that metric', function () {
    $client = Client::factory()->create([
        'goal_metric' => 'weight_kg',
        'goal_target_value' => 80,
    ]);

    expect($client->goalProgress())->toBeNull();
});

test('the client-show header renders the goal progress without error', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create([
        'goal_metric' => 'weight_kg',
        'goal_target_value' => 80,
        'goal_target_date' => now()->addDays(12)->format('Y-m-d'),
    ]);

    PhysicalMetric::factory()->for($client)->create(['recorded_at' => now()->subDays(20), 'weight_kg' => 90]);
    PhysicalMetric::factory()->for($client)->create(['recorded_at' => now(), 'weight_kg' => 85]);

    Livewire::actingAs($trainer)
        ->test(ClientShow::class, ['client' => $client])
        ->assertSee('50%');

    $this->actingAs($trainer)
        ->get(route('clients.show', $client))
        ->assertOk()
        ->assertSee('50%');
});
