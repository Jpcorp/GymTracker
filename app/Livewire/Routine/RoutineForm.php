<?php

namespace App\Livewire\Routine;

use App\Models\Client;
use App\Models\Routine;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class RoutineForm extends Component
{
    public Client $client;

    public ?Routine $routine = null;

    public string $name = '';

    public ?string $description = null;

    public ?string $weekly_frequency = null;

    public ?string $start_date = null;

    public ?string $end_date = null;

    public bool $is_active = true;

    public string $phase = 'accumulation';

    public function mount(Client $client, ?Routine $routine = null): void
    {
        $this->authorize('update', $client);

        $this->client = $client;

        if ($routine && $routine->exists) {
            abort_unless($routine->client_id === $client->id, 404);

            $this->routine = $routine;
            $this->name = $routine->name;
            $this->description = $routine->description;
            $this->weekly_frequency = (string) $routine->weekly_frequency;
            $this->start_date = $routine->start_date->format('Y-m-d');
            $this->end_date = $routine->end_date?->format('Y-m-d');
            $this->is_active = $routine->is_active;
            $this->phase = $routine->phase;
        } else {
            $this->start_date = now()->format('Y-m-d');
        }
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'weekly_frequency' => ['required', 'integer', 'min:1', 'max:7'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'is_active' => ['boolean'],
            'phase' => ['required', 'in:accumulation,intensification,realization,deload'],
        ];
    }

    public function save(): void
    {
        $data = $this->validate();

        if ($this->routine) {
            $this->routine->update($data);
            $routine = $this->routine;
        } else {
            $routine = $this->client->routines()->create($data);
        }

        $this->redirect(route('clients.routines.show', [$this->client, $routine]), navigate: true);
    }

    public function render()
    {
        return view('livewire.routine.routine-form');
    }
}
