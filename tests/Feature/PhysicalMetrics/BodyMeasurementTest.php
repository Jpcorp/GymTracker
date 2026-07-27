<?php

use App\Livewire\Client\ClientShow;
use App\Models\Client;
use App\Models\User;
use Livewire\Livewire;

test('guest is redirected to login from the client show page', function () {
    $client = Client::factory()->create();

    $this->get(route('clients.show', $client))->assertRedirect('/login');
});

test('a trainer can record a body measurement and it appears in the history', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();

    Livewire::actingAs($trainer)
        ->test(ClientShow::class, ['client' => $client])
        ->set('bm_recorded_at', '2026-07-26')
        ->set('bm_waist_cm', '80.5')
        ->set('bm_hips_cm', '95.2')
        ->call('saveBodyMeasurement')
        ->assertHasNoErrors();

    $measurement = $client->bodyMeasurements()->sole();

    expect((float) $measurement->waist_cm)->toBe(80.5);
    expect((float) $measurement->hips_cm)->toBe(95.2);
});

test('a trainer cannot record a body measurement for another trainer\'s client', function () {
    $trainer = User::factory()->create();
    $otherTrainer = User::factory()->create();
    $client = Client::factory()->for($otherTrainer, 'trainer')->create();

    $this->actingAs($trainer)
        ->get(route('clients.show', $client))
        ->assertForbidden();

    expect($client->bodyMeasurements()->count())->toBe(0);
});

test('validation rejects a body measurement submission with no measurement fields filled', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();

    Livewire::actingAs($trainer)
        ->test(ClientShow::class, ['client' => $client])
        ->call('saveBodyMeasurement')
        ->assertHasErrors(['bm_waist_cm']);

    expect($client->bodyMeasurements()->count())->toBe(0);
});
