<?php

use App\Livewire\Client\ClientShow;
use App\Models\Client;
use App\Models\User;
use Livewire\Livewire;

test('guest is redirected to login from the client show page', function () {
    $client = Client::factory()->create();

    $this->get(route('clients.show', $client))->assertRedirect('/login');
});

test('a trainer can record a mood entry and it appears in the history', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();

    Livewire::actingAs($trainer)
        ->test(ClientShow::class, ['client' => $client])
        ->set('mood_week_start', '2026-07-20')
        ->set('mood_week_end', '2026-07-26')
        ->set('mood_level', 8)
        ->set('mood_energy_level', 7)
        ->set('mood_motivation_level', 9)
        ->set('mood_notes', 'Buena semana')
        ->call('saveMood')
        ->assertHasNoErrors();

    $mood = $client->moodRecords()->sole();

    expect($mood->mood_level)->toBe(8);
    expect($mood->week_start->format('Y-m-d'))->toBe('2026-07-20');
});

test('a trainer can record a nutrition log and it appears in the history', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();

    Livewire::actingAs($trainer)
        ->test(ClientShow::class, ['client' => $client])
        ->set('nutrition_log_date', '2026-07-26')
        ->set('nutrition_compliance', 'partial')
        ->set('nutrition_meals_logged', 3)
        ->set('nutrition_meals_planned', 5)
        ->call('saveNutritionLog')
        ->assertHasNoErrors();

    $log = $client->nutritionLogs()->sole();

    expect($log->compliance)->toBe('partial');
    expect($log->meals_logged)->toBe(3);
});

test('a trainer can record a satisfaction survey and it appears in the history', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();

    Livewire::actingAs($trainer)
        ->test(ClientShow::class, ['client' => $client])
        ->set('satisfaction_survey_date', '2026-07-26')
        ->set('satisfaction_overall', 9)
        ->set('satisfaction_trainer', 10)
        ->set('satisfaction_facilities', 8)
        ->set('satisfaction_routines', 7)
        ->call('saveSatisfactionSurvey')
        ->assertHasNoErrors();

    $survey = $client->satisfactionSurveys()->sole();

    expect($survey->overall_satisfaction)->toBe(9);
});

test('a trainer gets a 403 viewing the wellness tab for another trainer\'s client', function () {
    $trainer = User::factory()->create();
    $otherTrainer = User::factory()->create();
    $client = Client::factory()->for($otherTrainer, 'trainer')->create();

    $this->actingAs($trainer)
        ->get(route('clients.show', $client))
        ->assertForbidden();
});

test('mood level outside 1-10 range is rejected', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();

    Livewire::actingAs($trainer)
        ->test(ClientShow::class, ['client' => $client])
        ->set('mood_week_start', '2026-07-20')
        ->set('mood_week_end', '2026-07-26')
        ->set('mood_level', 11)
        ->call('saveMood')
        ->assertHasErrors(['mood_level']);
});
