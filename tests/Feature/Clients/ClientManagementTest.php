<?php

use App\Livewire\Client\ClientForm;
use App\Livewire\Client\ClientList;
use App\Models\Client;
use App\Models\User;
use Livewire\Livewire;

test('guest is redirected to login from the clients index', function () {
    $this->get('/clients')->assertRedirect('/login');
});

test('a trainer can create a client and trainer_id is auto-assigned server-side', function () {
    $trainer = User::factory()->create();

    Livewire::actingAs($trainer)
        ->test(ClientForm::class)
        ->set('name', 'Jane Doe')
        ->set('email', 'jane@example.com')
        ->set('phone', '123456789')
        ->set('birth_date', '1990-01-01')
        ->set('gender', 'female')
        ->set('start_date', '2026-01-01')
        ->set('goal', 'Lose weight')
        ->call('save')
        ->assertHasNoErrors();

    $client = Client::sole();

    expect($client->name)->toBe('Jane Doe');
    expect($client->trainer_id)->toBe($trainer->id);
    expect($client->status)->toBe('active');
});

test('a trainer sees only their own clients, paginated', function () {
    $trainer = User::factory()->create();
    $otherTrainer = User::factory()->create();

    Client::factory()->for($trainer, 'trainer')->count(3)->create();
    Client::factory()->for($otherTrainer, 'trainer')->count(2)->create();

    Livewire::actingAs($trainer)
        ->test(ClientList::class)
        ->assertViewHas('clients', fn ($clients) => $clients->total() === 3);
});

test('a trainer can edit their own client', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create(['name' => 'Old Name']);

    Livewire::actingAs($trainer)
        ->test(ClientForm::class, ['client' => $client])
        ->set('name', 'New Name')
        ->call('save')
        ->assertHasNoErrors();

    expect($client->fresh()->name)->toBe('New Name');
});

test('a trainer cannot view or edit another trainer\'s client', function () {
    $trainer = User::factory()->create();
    $otherTrainer = User::factory()->create();
    $client = Client::factory()->for($otherTrainer, 'trainer')->create();

    $this->actingAs($trainer)
        ->get(route('clients.edit', $client))
        ->assertForbidden();
});

test('a trainer can pause and reactivate their own client', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create(['status' => 'active']);

    $component = Livewire::actingAs($trainer)->test(ClientList::class);

    $component->call('togglePause', $client->id);
    expect($client->fresh()->status)->toBe('paused');

    $component->call('togglePause', $client->id);
    expect($client->fresh()->status)->toBe('active');
});

test('a trainer cannot pause another trainer\'s client', function () {
    $trainer = User::factory()->create();
    $otherTrainer = User::factory()->create();
    $client = Client::factory()->for($otherTrainer, 'trainer')->create();

    Livewire::actingAs($trainer)
        ->test(ClientList::class)
        ->call('togglePause', $client->id)
        ->assertForbidden();
});

test('soft deleting a client removes it from the list but keeps it in the database', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();

    Livewire::actingAs($trainer)
        ->test(ClientList::class)
        ->call('delete', $client->id)
        ->assertViewHas('clients', fn ($clients) => $clients->total() === 0);

    $this->assertSoftDeleted($client);
});
