<?php

use App\Livewire\Dashboard;
use App\Models\Client;
use App\Models\Evaluation;
use App\Models\User;
use Livewire\Livewire;

test('guest is redirected to login from the dashboard', function () {
    $this->get('/dashboard')->assertRedirect('/login');
});

test('a trainer sees only their own clients stats on the dashboard', function () {
    $trainer = User::factory()->create();
    $otherTrainer = User::factory()->create();

    Client::factory()->for($trainer, 'trainer')->count(2)->create(['status' => 'active']);
    Client::factory()->for($trainer, 'trainer')->create(['status' => 'paused']);
    Client::factory()->for($otherTrainer, 'trainer')->count(5)->create(['status' => 'active']);

    Livewire::actingAs($trainer)
        ->test(Dashboard::class)
        ->assertViewHas('totalClients', 3)
        ->assertViewHas('activeClients', 2)
        ->assertViewHas('clients', fn ($clients) => $clients->count() === 3);
});

test('a client with an evaluation due within 3 days appears in the alert list', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create(['status' => 'active']);

    Evaluation::factory()->for($client)->create([
        'evaluation_number' => 1,
        'period_start' => today()->subDays(39),
        'period_end' => today()->subDays(19),
    ]);

    Livewire::actingAs($trainer)
        ->test(Dashboard::class)
        ->assertViewHas('alerts', fn ($alerts) => $alerts->count() === 1 && $alerts->first()->is($client))
        ->assertSee($client->name);
});

test('a client with 10+ days remaining does not appear in the alert list', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create(['status' => 'active']);

    Evaluation::factory()->for($client)->create([
        'evaluation_number' => 1,
        'period_start' => today()->subDays(20),
        'period_end' => today(),
    ]);

    Livewire::actingAs($trainer)
        ->test(Dashboard::class)
        ->assertViewHas('alerts', fn ($alerts) => $alerts->count() === 0);
});
