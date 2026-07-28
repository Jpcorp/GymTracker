<?php

use App\Models\Client;
use App\Models\Evaluation;
use App\Models\PhysicalMetric;
use App\Models\User;
use Illuminate\Support\Facades\URL;

test('an unsigned request to the portal is rejected', function () {
    $client = Client::factory()->create();

    $this->get(route('client.portal', $client))->assertForbidden();
});

test('a valid signed portal link shows the client their own progress', function () {
    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create(['name' => 'Ana Torres']);

    $evaluation = Evaluation::factory()->for($client)->create(['evaluation_number' => 1]);
    PhysicalMetric::factory()->for($client)->create([
        'evaluation_id' => $evaluation->id,
        'recorded_at' => now(),
    ]);

    $url = URL::signedRoute('client.portal', ['client' => $client->id]);

    $response = $this->get($url);

    $response->assertOk();
    $response->assertSee('Ana Torres');
});

test('a tampered signed portal link (different client id) is rejected', function () {
    $client = Client::factory()->create();
    $otherClient = Client::factory()->create();

    $url = URL::signedRoute('client.portal', ['client' => $client->id]);
    $tampered = str_replace((string) $client->id, (string) $otherClient->id, $url);

    $this->get($tampered)->assertForbidden();
});

test('the portal requires no authentication', function () {
    $client = Client::factory()->create();
    $url = URL::signedRoute('client.portal', ['client' => $client->id]);

    // no actingAs() call — this must work as a fully anonymous guest
    $this->get($url)->assertOk();
});
