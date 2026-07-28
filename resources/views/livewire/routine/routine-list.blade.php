<div class="space-y-6">
    <x-ui.card class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <h1 class="text-xl font-extrabold text-white flex items-center gap-2">
            <x-ui.icon name="dumbbell" class="w-5 h-5 text-cyan-400" />
            {{ __('routines.title') }} &mdash; {{ $client->name }}
        </h1>

        <div class="flex items-center gap-2">
            <x-ui.button href="{{ route('clients.routines.create', $client) }}" wire:navigate variant="primary">
                <x-ui.icon name="plus" class="w-4 h-4 stroke-[3]" />
                {{ __('routines.new_routine') }}
            </x-ui.button>
            <x-ui.button href="{{ route('clients.show', $client) }}" wire:navigate variant="secondary">
                {{ __('routines.back_to_client') }}
            </x-ui.button>
        </div>
    </x-ui.card>

    <x-ui.card padding="p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-800 text-sm">
                <thead class="bg-slate-800/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase">{{ __('routines.name') }}</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase">{{ __('routines.weekly_frequency') }}</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase">{{ __('routines.dates') }}</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase">{{ __('routines.exercises') }}</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase">{{ __('routines.status') }}</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @php
                        $phaseColors = ['accumulation' => 'cyan', 'intensification' => 'amber', 'realization' => 'emerald', 'deload' => 'violet'];
                    @endphp
                    @forelse ($routines as $routine)
                        <tr wire:key="routine-{{ $routine->id }}">
                            <td class="px-4 py-3 text-slate-200">
                                <a href="{{ route('clients.routines.show', [$client, $routine]) }}" wire:navigate class="font-semibold text-cyan-400 hover:text-cyan-300">{{ $routine->name }}</a>
                                <x-ui.badge :color="$phaseColors[$routine->phase] ?? 'slate'">{{ __('routines.phases.'.$routine->phase) }}</x-ui.badge>
                            </td>
                            <td class="px-4 py-3 text-slate-400">{{ $routine->weekly_frequency }}x/{{ __('routines.week') }}</td>
                            <td class="px-4 py-3 text-slate-400">{{ $routine->start_date->format('Y-m-d') }} &rarr; {{ $routine->end_date?->format('Y-m-d') ?? __('routines.ongoing') }}</td>
                            <td class="px-4 py-3 text-slate-400">{{ $routine->exercises_count }}</td>
                            <td class="px-4 py-3">
                                @if ($routine->is_active)
                                    <x-ui.badge color="emerald">{{ __('routines.statuses.active') }}</x-ui.badge>
                                @else
                                    <x-ui.badge color="slate">{{ __('routines.statuses.inactive') }}</x-ui.badge>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('clients.routines.edit', [$client, $routine]) }}" wire:navigate
                                       title="{{ __('routines.edit') }}"
                                       class="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-cyan-400 transition">
                                        <x-ui.icon name="edit" class="w-3.5 h-3.5" />
                                    </a>
                                    <button type="button" wire:click="delete({{ $routine->id }})" wire:confirm="{{ __('routines.delete_confirm') }}"
                                            title="{{ __('routines.delete') }}"
                                            class="p-1.5 rounded-lg bg-slate-800 hover:bg-rose-950 text-slate-400 hover:text-rose-400 transition">
                                        <x-ui.icon name="trash" class="w-3.5 h-3.5" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-slate-400">{{ __('routines.none_found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>
</div>
