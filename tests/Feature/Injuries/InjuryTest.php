<?php

use App\Livewire\Client\ClientShow;
use App\Models\Client;
use App\Models\Injury;
use App\Models\User;
use Livewire\Livewire;

test('guest is redirected to login from the client show page', function () {
    $client = Client::factory()->create();

    $this->get(route('clients.show', $client))->assertRedirect('/login');
});

test('a trainer can log an injury and it appears in the history', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();

    Livewire::actingAs($trainer)
        ->test(ClientShow::class, ['client' => $client])
        ->set('injury_body_part', 'Rodilla derecha')
        ->set('injury_reported_date', '2026-07-20')
        ->set('injury_severity', 6)
        ->set('injury_status', 'active')
        ->set('injury_notes', 'Molestia al sentadillar')
        ->call('saveInjury')
        ->assertHasNoErrors()
        ->assertSee('Rodilla derecha');

    $injury = $client->injuries()->sole();

    expect($injury->body_part)->toBe('Rodilla derecha');
    expect($injury->severity)->toBe(6);
    expect($injury->status)->toBe('active');
    expect($injury->resolved_date)->toBeNull();
});

test('logging an injury with status resolved sets resolved_date immediately', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();

    Livewire::actingAs($trainer)
        ->test(ClientShow::class, ['client' => $client])
        ->set('injury_body_part', 'Tobillo')
        ->set('injury_reported_date', '2026-07-01')
        ->set('injury_severity', 3)
        ->set('injury_status', 'resolved')
        ->call('saveInjury')
        ->assertHasNoErrors();

    $injury = $client->injuries()->sole();

    expect($injury->status)->toBe('resolved');
    expect($injury->resolved_date)->not->toBeNull();
});

test('a trainer can mark an active injury as resolved', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();
    $injury = Injury::create([
        'client_id' => $client->id,
        'body_part' => 'Hombro',
        'reported_date' => '2026-07-10',
        'severity' => 5,
        'status' => 'active',
    ]);

    Livewire::actingAs($trainer)
        ->test(ClientShow::class, ['client' => $client])
        ->call('resolveInjury', $injury->id)
        ->assertHasNoErrors();

    expect($injury->fresh()->status)->toBe('resolved');
    expect($injury->fresh()->resolved_date)->not->toBeNull();
});

test('active injuries count shows as a warning badge on the client header', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();
    Injury::create(['client_id' => $client->id, 'body_part' => 'Espalda baja', 'reported_date' => '2026-07-01', 'severity' => 7, 'status' => 'active']);
    Injury::create(['client_id' => $client->id, 'body_part' => 'Codo', 'reported_date' => '2026-07-05', 'severity' => 4, 'status' => 'recovering']);
    Injury::create(['client_id' => $client->id, 'body_part' => 'Muñeca', 'reported_date' => '2026-06-01', 'severity' => 2, 'status' => 'resolved']);

    expect($client->activeInjuriesCount())->toBe(2);

    $this->actingAs($trainer)
        ->get(route('clients.show', $client))
        ->assertSee('Lesiones activas: 2');
});

test('a trainer gets a 403 viewing the injuries tab for another trainer\'s client', function () {
    $ownerTrainer = User::factory()->create();
    $otherTrainer = User::factory()->create();
    $client = Client::factory()->for($ownerTrainer, 'trainer')->create();

    Livewire::actingAs($otherTrainer)
        ->test(ClientShow::class, ['client' => $client])
        ->assertForbidden();
});

test('validation rejects a severity outside 1-10', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();

    Livewire::actingAs($trainer)
        ->test(ClientShow::class, ['client' => $client])
        ->set('injury_body_part', 'Rodilla')
        ->set('injury_reported_date', '2026-07-20')
        ->set('injury_severity', 15)
        ->call('saveInjury')
        ->assertHasErrors(['injury_severity']);
});
