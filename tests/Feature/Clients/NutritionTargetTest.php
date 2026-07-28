<?php

use App\Livewire\Client\ClientForm;
use App\Models\Client;
use App\Models\User;
use Livewire\Livewire;

test('a trainer can set nutrition targets when creating a client', function () {
    $trainer = User::factory()->create();

    Livewire::actingAs($trainer)
        ->test(ClientForm::class)
        ->set('name', 'Jane Doe')
        ->set('email', 'jane@example.com')
        ->set('birth_date', '1990-01-01')
        ->set('gender', 'female')
        ->set('start_date', '2026-01-01')
        ->set('nutrition_target_kcal', '2200')
        ->set('nutrition_target_protein_g', '160')
        ->set('nutrition_target_notes', 'Fase de volumen — superávit moderado')
        ->call('save')
        ->assertHasNoErrors();

    $client = Client::sole();

    expect($client->nutrition_target_kcal)->toBe(2200);
    expect($client->nutrition_target_protein_g)->toBe(160);
    expect($client->nutrition_target_notes)->toBe('Fase de volumen — superávit moderado');
});

test('a trainer can set nutrition targets when editing a client', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();

    Livewire::actingAs($trainer)
        ->test(ClientForm::class, ['client' => $client])
        ->set('nutrition_target_kcal', '1800')
        ->set('nutrition_target_protein_g', '140')
        ->set('nutrition_target_notes', 'Fase de definición')
        ->call('save')
        ->assertHasNoErrors();

    $client->refresh();

    expect($client->nutrition_target_kcal)->toBe(1800);
    expect($client->nutrition_target_protein_g)->toBe(140);
    expect($client->nutrition_target_notes)->toBe('Fase de definición');
});

test('the client-show page renders the nutrition target reference line when targets are set', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create([
        'nutrition_target_kcal' => 2200,
        'nutrition_target_protein_g' => 160,
        'nutrition_target_notes' => 'Fase de volumen',
    ]);

    $this->actingAs($trainer)
        ->get(route('clients.show', $client))
        ->assertOk()
        ->assertSee('2200 kcal')
        ->assertSee('160 g proteína')
        ->assertSee('Fase de volumen');
});

test('the client-show page does not render the nutrition target reference line when targets are null', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create([
        'nutrition_target_kcal' => null,
        'nutrition_target_protein_g' => null,
        'nutrition_target_notes' => null,
    ]);

    $this->actingAs($trainer)
        ->get(route('clients.show', $client))
        ->assertOk()
        ->assertDontSee('kcal proteína')
        ->assertDontSee('g proteína');
});
