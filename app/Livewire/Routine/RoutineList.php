<?php

namespace App\Livewire\Routine;

use App\Models\Client;
use App\Models\Routine;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class RoutineList extends Component
{
    public Client $client;

    public function mount(Client $client): void
    {
        $this->authorize('view', $client);

        $this->client = $client;
    }

    public function delete(Routine $routine): void
    {
        $this->authorize('update', $this->client);
        abort_unless($routine->client_id === $this->client->id, 404);

        $routine->delete();
    }

    public function render()
    {
        return view('livewire.routine.routine-list', [
            'routines' => $this->client->routines()
                ->withCount('exercises')
                ->orderByDesc('is_active')
                ->orderByDesc('start_date')
                ->get(),
        ]);
    }
}
