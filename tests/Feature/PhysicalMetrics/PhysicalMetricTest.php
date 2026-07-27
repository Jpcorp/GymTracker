<?php

use App\Livewire\Client\ClientShow;
use App\Models\Client;
use App\Models\User;
use Livewire\Livewire;

test('guest is redirected to login from the client show page', function () {
    $client = Client::factory()->create();

    $this->get(route('clients.show', $client))->assertRedirect('/login');
});

test('a trainer can view their own client\'s show page', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();

    $this->actingAs($trainer)
        ->get(route('clients.show', $client))
        ->assertOk()
        ->assertSee($client->name);
});

test('a trainer cannot view another trainer\'s client show page', function () {
    $trainer = User::factory()->create();
    $otherTrainer = User::factory()->create();
    $client = Client::factory()->for($otherTrainer, 'trainer')->create();

    $this->actingAs($trainer)
        ->get(route('clients.show', $client))
        ->assertForbidden();
});

test('a trainer can record a physical metric and it appears in the history with the correct auto-computed bmi', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();

    Livewire::actingAs($trainer)
        ->test(ClientShow::class, ['client' => $client])
        ->set('recorded_at', '2026-07-26')
        ->set('weight_kg', '78.5')
        ->set('height_cm', '175')
        ->set('body_fat_percentage', '18.5')
        ->set('metabolic_age', '30')
        ->set('basal_kcal', '1700')
        ->set('visceral_fat', '8')
        ->call('saveMetric')
        ->assertHasNoErrors();

    $metric = $client->physicalMetrics()->sole();

    expect((float) $metric->weight_kg)->toBe(78.5);
    expect((float) $metric->bmi)->toBe(25.63);
});

test('validation rejects a missing or zero weight_kg', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();

    Livewire::actingAs($trainer)
        ->test(ClientShow::class, ['client' => $client])
        ->set('weight_kg', '')
        ->set('height_cm', '175')
        ->call('saveMetric')
        ->assertHasErrors(['weight_kg' => 'required']);

    Livewire::actingAs($trainer)
        ->test(ClientShow::class, ['client' => $client])
        ->set('weight_kg', '0')
        ->set('height_cm', '175')
        ->call('saveMetric')
        ->assertHasErrors(['weight_kg' => 'min']);
});
