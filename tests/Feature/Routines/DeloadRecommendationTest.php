<?php

use App\Livewire\Client\ClientShow;
use App\Models\Client;
use App\Models\Routine;
use App\Models\User;
use Livewire\Livewire;

test('deloadRecommended is true when the only routine is non-deload and older than 6 weeks with no deload ever', function () {
    $client = Client::factory()->create();
    Routine::factory()->for($client)->create([
        'phase' => 'accumulation',
        'start_date' => now()->subWeeks(8),
        'end_date' => null,
    ]);

    expect($client->deloadRecommended())->toBeTrue();
});

test('deloadRecommended is false when the most recent deload routine ended less than 6 weeks ago', function () {
    $client = Client::factory()->create();
    Routine::factory()->for($client)->create([
        'phase' => 'accumulation',
        'start_date' => now()->subWeeks(20),
        'end_date' => now()->subWeeks(10),
    ]);
    Routine::factory()->for($client)->create([
        'phase' => 'deload',
        'start_date' => now()->subWeeks(3),
        'end_date' => now()->subWeeks(2),
    ]);

    expect($client->deloadRecommended())->toBeFalse();
});

test('deloadRecommended is false when the client has no routines at all', function () {
    $client = Client::factory()->create();

    expect($client->deloadRecommended())->toBeFalse();
});

test('deloadRecommended is false when the only routine is recent and non-deload', function () {
    $client = Client::factory()->create();
    Routine::factory()->for($client)->create([
        'phase' => 'accumulation',
        'start_date' => now()->subWeeks(2),
        'end_date' => null,
    ]);

    expect($client->deloadRecommended())->toBeFalse();
});

test('the client-show attendance tab renders the deload banner when recommended', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();
    Routine::factory()->for($client)->create([
        'phase' => 'accumulation',
        'start_date' => now()->subWeeks(8),
        'end_date' => null,
    ]);

    $this->actingAs($trainer)
        ->get(route('clients.show', $client))
        ->assertOk()
        ->assertSee(__('clients.deload.recommended_text'));
});

test('the client-show attendance tab does not render the deload banner when not recommended', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();

    $this->actingAs($trainer)
        ->get(route('clients.show', $client))
        ->assertOk()
        ->assertDontSee(__('clients.deload.recommended_text'));
});
