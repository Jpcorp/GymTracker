@extends('layouts.portal')

@section('content')

    <x-ui.card class="space-y-1">
        <h1 class="text-xl font-black text-white tracking-tight">{{ $client->name }}</h1>
        @if ($client->goal)
            <p class="text-xs font-semibold text-slate-300">{{ $client->goal }}</p>
        @endif
        <p class="text-[11px] text-slate-500">Asistencia este mes: <span class="text-cyan-400 font-bold">{{ $attendancePercentage }}%</span></p>
    </x-ui.card>

    @if ($goalProgress)
        <x-ui.card class="space-y-2">
            <h2 class="text-sm font-bold text-white">Tu meta</h2>
            <div class="flex items-center justify-between text-[11px] text-slate-400 mb-1">
                <span>
                    {{ __('clients.goal_progress', [
                        'metric' => __('clients.metrics.'.$goalProgress['metric']),
                        'current' => $goalProgress['current'],
                        'target' => $goalProgress['target'],
                        'percent' => $goalProgress['percent'],
                    ]) }}
                </span>
            </div>
            <div class="w-full h-1.5 bg-slate-800 rounded-full overflow-hidden">
                <div class="h-full bg-cyan-500 rounded-full" style="width: {{ $goalProgress['percent'] }}%"></div>
            </div>
        </x-ui.card>
    @endif

    <x-ui.card>
        <h2 class="text-sm font-bold text-white mb-4">Evolución de tus métricas</h2>

        @if ($chartData['hasEnoughData'])
            <div id="portal-progress-chart" data-progress-chart="{{ json_encode($chartData) }}" wire:ignore></div>
        @else
            <p class="text-sm text-slate-400">{{ __('clients.chart.not_enough_data') }}</p>
        @endif
    </x-ui.card>

    <x-ui.card class="space-y-6">
        <h2 class="text-sm font-bold text-white">Tus logros por período</h2>

        @forelse ($evaluations as $row)
            @php [$evaluation, $achievements] = [$row['evaluation'], $row['achievements']]; @endphp
            <div wire:key="evaluation-{{ $evaluation->id }}" class="relative pl-5 border-l-2 border-slate-800">
                <span class="absolute -left-[7px] top-1.5 w-3 h-3 rounded-full bg-amber-400"></span>
                <div class="border border-slate-800 rounded-xl p-4">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-xs font-bold text-white flex items-center gap-1.5">
                            <x-ui.icon name="award" class="w-3.5 h-3.5 text-amber-400" />
                            Evaluación #{{ $evaluation->evaluation_number }}
                        </h3>
                        <span class="text-[11px] text-slate-400">{{ $evaluation->period_start->format('Y-m-d') }} &rarr; {{ $evaluation->period_end->format('Y-m-d') }}</span>
                    </div>

                    @if (! empty($achievements))
                        <ul class="space-y-1">
                            @foreach ($achievements as $achievement)
                                <li class="text-xs text-slate-300 flex items-start gap-1.5">
                                    <span class="text-emerald-400">&check;</span>
                                    <span>{{ $achievement }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-xs text-slate-500">Sin logros destacados en este período.</p>
                    @endif
                </div>
            </div>
        @empty
            <p class="text-sm text-slate-400">Todavía no hay evaluaciones generadas.</p>
        @endforelse
    </x-ui.card>

@endsection
