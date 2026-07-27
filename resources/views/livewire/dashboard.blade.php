@php
    $statusColors = ['active' => 'emerald', 'paused' => 'amber', 'inactive' => 'rose'];

    $countdownLabel = function (int $days) {
        if ($days < 0) {
            return __('dashboard.overdue');
        }
        if ($days === 0) {
            return __('dashboard.due_today');
        }

        return trans_choice('dashboard.days_remaining', $days, ['days' => $days]);
    };

    $countdownColor = fn (int $days) => $days <= 3 ? 'rose' : ($days <= 7 ? 'amber' : 'emerald');
@endphp

<div class="space-y-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <x-ui.card class="flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-cyan-500/15 flex items-center justify-center shrink-0">
                <x-ui.icon name="users" class="w-5 h-5 text-cyan-400" />
            </div>
            <div>
                <p class="text-2xl font-extrabold text-white">{{ $totalClients }}</p>
                <p class="text-xs text-slate-400">{{ __('dashboard.total_clients') }}</p>
            </div>
        </x-ui.card>

        <x-ui.card class="flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-emerald-500/15 flex items-center justify-center shrink-0">
                <x-ui.icon name="award" class="w-5 h-5 text-emerald-400" />
            </div>
            <div>
                <p class="text-2xl font-extrabold text-white">{{ $activeClients }}</p>
                <p class="text-xs text-slate-400">{{ __('dashboard.active_clients') }}</p>
            </div>
        </x-ui.card>
    </div>

    <x-ui.card>
        <h2 class="text-sm font-bold text-white flex items-center gap-2 mb-4">
            <x-ui.icon name="clock" class="w-4 h-4 text-rose-400" />
            {{ __('dashboard.alerts_title') }} ({{ $alerts->count() }})
        </h2>

        @forelse ($alerts as $client)
            @php $days = $client->daysUntilNextEvaluation(); @endphp
            <a href="{{ route('clients.show', $client) }}" wire:navigate wire:key="alert-{{ $client->id }}"
               class="flex items-center justify-between gap-3 py-2.5 border-b border-slate-800/80 last:border-0 hover:bg-slate-800/40 -mx-2 px-2 rounded-lg transition">
                <span class="text-sm text-slate-200 font-medium truncate">{{ $client->name }}</span>
                <x-ui.badge :color="$countdownColor($days)">{{ $countdownLabel($days) }}</x-ui.badge>
            </a>
        @empty
            <p class="text-sm text-slate-400">{{ __('dashboard.no_alerts') }}</p>
        @endforelse
    </x-ui.card>

    <x-ui.card>
        <h2 class="text-sm font-bold text-white flex items-center gap-2 mb-4">
            <x-ui.icon name="users" class="w-4 h-4 text-cyan-400" />
            {{ __('dashboard.clients_title') }}
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse ($clients as $client)
                @php $days = $client->daysUntilNextEvaluation(); @endphp
                <a href="{{ route('clients.show', $client) }}" wire:navigate wire:key="client-{{ $client->id }}"
                   class="block bg-slate-950/60 border border-slate-800 rounded-xl p-4 hover:border-cyan-500/40 transition space-y-2">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-sm font-bold text-white truncate">{{ $client->name }}</span>
                        <x-ui.badge :color="$statusColors[$client->status] ?? 'slate'">
                            {{ __('clients.statuses.'.$client->status) }}
                        </x-ui.badge>
                    </div>
                    <div class="flex items-center justify-between text-xs text-slate-400">
                        <span>{{ __('dashboard.next_evaluation') }}</span>
                        <x-ui.badge :color="$countdownColor($days)">{{ $countdownLabel($days) }}</x-ui.badge>
                    </div>
                </a>
            @empty
                <div class="col-span-full">
                    <p class="text-sm text-slate-400">{{ __('dashboard.no_clients') }}</p>
                </div>
            @endforelse
        </div>
    </x-ui.card>
</div>
