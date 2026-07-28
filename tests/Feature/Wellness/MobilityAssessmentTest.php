<?php

use App\Livewire\Client\ClientShow;
use App\Models\Client;
use App\Models\User;
use Livewire\Livewire;

test('a trainer can log a mobility assessment and it appears in the history', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();

    Livewire::actingAs($trainer)
        ->test(ClientShow::class, ['client' => $client])
        ->set('mobility_assessment_date', '2026-07-26')
        ->set('mobility_test_name', 'Sit and reach')
        ->set('mobility_score', 12.5)
        ->set('mobility_notes', 'Buena flexibilidad isquiotibial')
        ->call('saveMobilityAssessment')
        ->assertHasNoErrors()
        ->assertSee('Sit and reach')
        ->assertSee('12.5');

    $assessment = $client->mobilityAssessments()->sole();

    expect($assessment->test_name)->toBe('Sit and reach');
    expect((float) $assessment->score)->toBe(12.5);
    expect($assessment->assessment_date->format('Y-m-d'))->toBe('2026-07-26');
});

test('mobility assessment requires a test name and score', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();

    Livewire::actingAs($trainer)
        ->test(ClientShow::class, ['client' => $client])
        ->set('mobility_assessment_date', '2026-07-26')
        ->set('mobility_test_name', '')
        ->set('mobility_score', null)
        ->call('saveMobilityAssessment')
        ->assertHasErrors(['mobility_test_name', 'mobility_score']);
});
