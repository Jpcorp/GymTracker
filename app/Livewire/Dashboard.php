<?php

namespace App\Livewire;

use App\Models\Client;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    public function render()
    {
        $clients = auth()->user()->clients()->latest()->get();

        $alerts = $clients->filter(fn (Client $client) => $client->daysUntilNextEvaluation() <= 3)
            ->sortBy(fn (Client $client) => $client->daysUntilNextEvaluation())
            ->values();

        return view('livewire.dashboard', [
            'clients' => $clients,
            'totalClients' => $clients->count(),
            'activeClients' => $clients->where('status', 'active')->count(),
            'alerts' => $alerts,
        ]);
    }
}
