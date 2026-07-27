<?php

use App\Models\Client;
use App\Models\Evaluation;
use App\Models\PhysicalMetric;
use App\Models\User;

test('guest is redirected to login from the client report route', function () {
    $client = Client::factory()->create();

    $this->get(route('clients.report', $client))->assertRedirect('/login');
});

test('a trainer can download the PDF report for their own client', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();

    $evaluation = Evaluation::factory()->for($client)->create(['evaluation_number' => 1]);
    PhysicalMetric::factory()->for($client)->create([
        'evaluation_id' => $evaluation->id,
        'recorded_at' => now(),
    ]);

    $response = $this->actingAs($trainer)->get(route('clients.report', $client));

    $response->assertOk();
    $response->assertHeader('Content-Type', 'application/pdf');
});

test('a trainer gets a 403 for another trainer\'s client report', function () {
    $trainer = User::factory()->create();
    $otherTrainer = User::factory()->create();
    $client = Client::factory()->for($otherTrainer, 'trainer')->create();

    $this->actingAs($trainer)->get(route('clients.report', $client))->assertForbidden();
});

test('the report route works for a client with zero evaluations or metrics', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();

    $response = $this->actingAs($trainer)->get(route('clients.report', $client));

    $response->assertOk();
    $response->assertHeader('Content-Type', 'application/pdf');
});
