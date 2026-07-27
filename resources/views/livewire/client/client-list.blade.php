<div class="space-y-6">
    <x-ui.card class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <h1 class="text-xl font-extrabold text-white flex items-center gap-2">
            <x-ui.icon name="users" class="w-5 h-5 text-cyan-400" />
            {{ __('clients.title') }} ({{ $clients->total() }})
        </h1>

        <x-ui.button href="{{ route('clients.create') }}" wire:navigate variant="primary" class="self-start sm:self-auto">
            <x-ui.icon name="plus" class="w-4 h-4 stroke-[3]" />
            {{ __('clients.new_client') }}
        </x-ui.button>
    </x-ui.card>

    <div class="flex flex-wrap gap-3">
        <div class="relative flex-1 min-w-[200px] max-w-md">
            <x-ui.icon name="search" class="w-4 h-4 text-slate-500 absolute left-3 top-1/2 -translate-y-1/2" />
            <input type="text" wire:model.live.debounce.300ms="search"
                   placeholder="{{ __('clients.search_placeholder') }}"
                   class="w-full bg-slate-900 text-xs text-slate-200 placeholder-slate-500 pl-9 pr-4 py-2 rounded-xl border border-slate-800 focus:outline-none focus:border-cyan-500 transition" />
        </div>

        <select wire:model.live="status"
                class="bg-slate-900 text-xs text-slate-200 px-3 py-2 rounded-xl border border-slate-800 focus:outline-none focus:border-cyan-500 transition">
            <option value="">{{ __('clients.all_statuses') }}</option>
            <option value="active">{{ __('clients.statuses.active') }}</option>
            <option value="paused">{{ __('clients.statuses.paused') }}</option>
            <option value="inactive">{{ __('clients.statuses.inactive') }}</option>
        </select>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse ($clients as $client)
            @php
                $statusColor = ['active' => 'emerald', 'paused' => 'amber', 'inactive' => 'rose'][$client->status] ?? 'slate';
            @endphp
            <div wire:key="client-{{ $client->id }}"
                 class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-lg hover:border-cyan-500/40 transition flex flex-col justify-between">
                <div class="space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-tr from-cyan-500 to-blue-600 flex items-center justify-center text-white font-bold text-lg shrink-0">
                            {{ mb_strtoupper(mb_substr($client->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="font-bold text-sm text-white truncate">{{ $client->name }}</p>
                            <x-ui.badge :color="$statusColor">{{ __('clients.statuses.'.$client->status) }}</x-ui.badge>
                        </div>
                    </div>

                    <p class="text-xs text-slate-300 font-medium truncate">{{ $client->email }}</p>

                    <div class="text-[11px] text-slate-400 space-y-0.5">
                        <p>{{ __('clients.trainer') }}: <strong class="text-slate-200">{{ $client->trainer?->name }}</strong></p>
                        <p>{{ __('clients.start_date') }}: {{ $client->start_date->format('Y-m-d') }}</p>
                    </div>
                </div>

                <div class="pt-4 mt-4 border-t border-slate-800/80 flex items-center justify-between">
                    <a href="{{ route('clients.show', $client) }}" wire:navigate
                       class="text-xs font-semibold text-cyan-400 hover:text-cyan-300 flex items-center gap-1 group">
                        {{ __('clients.view') }}
                        <x-ui.icon name="chevron" class="w-3.5 h-3.5 group-hover:translate-x-1 transition" />
                    </a>

                    <div class="flex items-center gap-1">
                        <a href="{{ route('clients.edit', $client) }}" wire:navigate
                           title="{{ __('clients.edit') }}"
                           class="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-cyan-400 transition">
                            <x-ui.icon name="edit" class="w-3.5 h-3.5" />
                        </a>

                        <button type="button" wire:click="togglePause({{ $client->id }})"
                                title="{{ $client->status === 'active' ? __('clients.pause') : __('clients.reactivate') }}"
                                class="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-amber-400 transition">
                            <x-ui.icon name="clock" class="w-3.5 h-3.5" />
                        </button>

                        <button type="button" wire:click="delete({{ $client->id }})"
                                onclick="return confirm('{{ __('clients.delete_confirm') }}')"
                                title="{{ __('clients.delete') }}"
                                class="p-1.5 rounded-lg bg-slate-800 hover:bg-rose-950 text-slate-400 hover:text-rose-400 transition">
                            <x-ui.icon name="trash" class="w-3.5 h-3.5" />
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <x-ui.card class="text-center text-sm text-slate-400">
                    {{ __('clients.none_found') }}
                </x-ui.card>
            </div>
        @endforelse
    </div>

    <div>
        {{ $clients->links() }}
    </div>
</div>
