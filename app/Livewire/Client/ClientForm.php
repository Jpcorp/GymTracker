<?php

namespace App\Livewire\Client;

use App\Models\Client;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ClientForm extends Component
{
    public ?Client $client = null;

    public string $name = '';

    public string $email = '';

    public ?string $phone = null;

    public ?string $birth_date = null;

    public string $gender = '';

    public ?string $start_date = null;

    public ?string $goal = null;

    public function mount(?Client $client = null): void
    {
        if ($client && $client->exists) {
            $this->authorize('update', $client);

            $this->client = $client;
            $this->name = $client->name;
            $this->email = $client->email;
            $this->phone = $client->phone;
            $this->birth_date = $client->birth_date->format('Y-m-d');
            $this->gender = $client->gender;
            $this->start_date = $client->start_date->format('Y-m-d');
            $this->goal = $client->goal;
        }
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', Rule::unique('clients', 'email')->ignore($this->client?->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'birth_date' => ['required', 'date', 'before_or_equal:today'],
            'gender' => ['required', 'in:male,female,other'],
            'start_date' => ['required', 'date'],
            'goal' => ['nullable', 'string'],
        ];
    }

    public function save(): void
    {
        $data = $this->validate();

        if ($this->client) {
            $this->client->update($data);
        } else {
            Client::create($data + [
                'trainer_id' => auth()->id(),
                'status' => 'active',
            ]);
        }

        $this->redirect(route('clients.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.client.client-form');
    }
}
