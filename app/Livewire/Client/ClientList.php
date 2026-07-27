<?php

namespace App\Livewire\Client;

use App\Models\Client;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class ClientList extends Component
{
    use WithPagination;

    public string $search = '';

    public string $status = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function togglePause(Client $client): void
    {
        $this->authorize('update', $client);

        $client->update([
            'status' => $client->status === 'active' ? 'paused' : 'active',
        ]);
    }

    public function delete(Client $client): void
    {
        $this->authorize('delete', $client);

        $client->delete();
    }

    public function render()
    {
        $clients = Client::query()
            ->where('trainer_id', auth()->id())
            ->when($this->search, fn ($query) => $query->where(
                fn ($query) => $query
                    ->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%")
            ))
            ->when($this->status, fn ($query) => $query->where('status', $this->status))
            ->latest()
            ->paginate(15);

        return view('livewire.client.client-list', ['clients' => $clients]);
    }
}
