<?php

use App\Livewire\Client\ClientShow;
use App\Models\BodyPhoto;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

test('guest is redirected to login from the client show page', function () {
    $client = Client::factory()->create();

    $this->get(route('clients.show', $client))->assertRedirect('/login');
});

test('a trainer can upload a body photo for their own client', function () {
    Storage::fake('public');

    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();

    Livewire::actingAs($trainer)
        ->test(ClientShow::class, ['client' => $client])
        ->set('photo_date', '2026-07-20')
        ->set('front_photo', UploadedFile::fake()->image('front.jpg', 2000, 1500))
        ->call('uploadPhotos')
        ->assertHasNoErrors();

    $photo = BodyPhoto::sole();

    expect($photo->client_id)->toBe($client->id);
    expect($photo->view_type)->toBe('front');
    expect($photo->photo_date->format('Y-m-d'))->toBe('2026-07-20');
    expect($photo->evaluation_id)->toBeNull();

    Storage::disk('public')->assertExists($photo->photo_path);
});

test('a trainer gets 403 uploading a body photo to another trainer\'s client', function () {
    Storage::fake('public');

    $trainer = User::factory()->create();
    $otherTrainer = User::factory()->create();
    $client = Client::factory()->for($otherTrainer, 'trainer')->create();

    $this->actingAs($trainer)
        ->get(route('clients.show', $client))
        ->assertForbidden();

    expect(BodyPhoto::count())->toBe(0);
});

test('validation rejects a non-image file for a body photo', function () {
    Storage::fake('public');

    $trainer = User::factory()->create();
    $client = Client::factory()->for($trainer, 'trainer')->create();

    Livewire::actingAs($trainer)
        ->test(ClientShow::class, ['client' => $client])
        ->set('photo_date', '2026-07-20')
        ->set('front_photo', UploadedFile::fake()->create('document.pdf', 100))
        ->call('uploadPhotos')
        ->assertHasErrors(['front_photo' => 'image']);

    expect(BodyPhoto::count())->toBe(0);
});
