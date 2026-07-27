<?php

use App\Models\Client;
use App\Models\User;

test('guest is redirected to login from the client export route', function () {
    $client = Client::factory()->create();

    $this->get(route('clients.export', $client))->assertRedirect('/login');
});

test('a trainer can download the excel export for their own client', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();

    $response = $this->actingAs($trainer)->get(route('clients.export', $client));

    $response->assertOk();
    $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
});

test('a trainer gets a 403 for another trainer\'s client export', function () {
    $trainer = User::factory()->create();
    $otherTrainer = User::factory()->create();
    $client = Client::factory()->for($otherTrainer, 'trainer')->create();

    $this->actingAs($trainer)->get(route('clients.export', $client))->assertForbidden();
});
