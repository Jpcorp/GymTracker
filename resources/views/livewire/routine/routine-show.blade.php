<div class="space-y-6">
    @php
        $phaseColors = ['accumulation' => 'cyan', 'intensification' => 'amber', 'realization' => 'emerald', 'deload' => 'violet'];
    @endphp
    <x-ui.card class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-extrabold text-white">{{ $routine->name }}</h1>
            <p class="text-xs text-slate-400 mt-1 flex flex-wrap items-center gap-x-2">
                <span>{{ $client->name }}</span> &middot;
                <span>{{ $routine->weekly_frequency }}x/{{ __('routines.week') }}</span> &middot;
                <span>{{ $routine->start_date->format('Y-m-d') }} &rarr; {{ $routine->end_date?->format('Y-m-d') ?? __('routines.ongoing') }}</span> &middot;
                @if ($routine->is_active)
                    <x-ui.badge color="emerald">{{ __('routines.statuses.active') }}</x-ui.badge>
                @else
                    <x-ui.badge color="slate">{{ __('routines.statuses.inactive') }}</x-ui.badge>
                @endif
                <x-ui.badge :color="$phaseColors[$routine->phase] ?? 'slate'">{{ __('routines.phases.'.$routine->phase) }}</x-ui.badge>
            </p>
            @if ($routine->description)
                <p class="text-xs text-slate-300 mt-2">{{ $routine->description }}</p>
            @endif
        </div>

        <div class="flex items-center gap-2">
            <x-ui.button href="{{ route('clients.routines.edit', [$client, $routine]) }}" wire:navigate variant="secondary">
                <x-ui.icon name="edit" class="w-3.5 h-3.5" />
                {{ __('routines.edit_routine') }}
            </x-ui.button>
            <x-ui.button href="{{ route('clients.routines.index', $client) }}" wire:navigate variant="secondary">
                {{ __('routines.form.back_to_routines') }}
            </x-ui.button>
        </div>
    </x-ui.card>

    <x-ui.card>
        <h2 class="text-sm font-bold text-white mb-4">{{ $editingExerciseId ? __('routines.exercise.edit_title') : __('routines.exercise.add_title') }}</h2>

        <form wire:submit="saveExercise" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <x-ui.input label="{{ __('routines.exercise.name') }}" name="exercise_name" wire:model="exercise_name" type="text" />
            <x-ui.input label="{{ __('routines.exercise.muscle_group') }}" name="exercise_muscle_group" wire:model="exercise_muscle_group" type="text" />
            <x-ui.input label="{{ __('routines.exercise.sets') }}" name="exercise_sets" wire:model="exercise_sets" type="number" min="1" />
            <x-ui.input label="{{ __('routines.exercise.reps_range') }}" name="exercise_reps_range" wire:model="exercise_reps_range" type="text" placeholder="8-12" />
            <x-ui.input label="{{ __('routines.exercise.rest_seconds') }}" name="exercise_rest_seconds" wire:model="exercise_rest_seconds" type="number" min="0" />
            <x-ui.input label="{{ __('routines.exercise.notes') }}" name="exercise_notes" wire:model="exercise_notes" type="text" />

            <div class="sm:col-span-3 flex justify-end gap-3">
                @if ($editingExerciseId)
                    <x-ui.button type="button" wire:click="cancelEditExercise" variant="secondary">{{ __('routines.exercise.cancel') }}</x-ui.button>
                @endif
                <x-ui.button type="submit" variant="primary">{{ $editingExerciseId ? __('routines.exercise.update') : __('routines.exercise.add') }}</x-ui.button>
            </div>
        </form>
    </x-ui.card>

    <x-ui.card>
        <h2 class="text-sm font-bold text-white mb-4 flex items-center gap-1.5">
            {{ __('routines.performance.volume_title') }}
            <x-ui.chart-help text="Series × repeticiones × peso por semana, agrupado por grupo muscular. Permite ver si el entrenamiento está balanceado entre grupos o si alguno está quedando sub-entrenado." />
        </h2>
        @if ($volumeChartData['hasEnoughData'])
            <div id="volume-chart-{{ $routine->id }}" data-progress-chart="{{ json_encode($volumeChartData) }}" wire:ignore></div>
        @else
            <p class="text-sm text-slate-400">{{ __('clients.chart.not_enough_data') }}</p>
        @endif
    </x-ui.card>

    <x-ui.card>
        <h2 class="text-sm font-bold text-white mb-4 flex items-center gap-1.5">
            {{ __('routines.performance.rpe_title') }}
            <x-ui.chart-help text="Esfuerzo percibido promedio por sesión (escala 1-10). Valores sostenidos en 9-10 pueden anticipar fatiga acumulada; lo ideal es variar el esfuerzo según la fase del plan." />
        </h2>
        @if ($rpeChartData['hasEnoughData'])
            <div id="rpe-chart-{{ $routine->id }}" data-progress-chart="{{ json_encode($rpeChartData) }}" wire:ignore></div>
        @else
            <p class="text-sm text-slate-400">{{ __('clients.chart.not_enough_data') }}</p>
        @endif
    </x-ui.card>

    <div class="space-y-6">
        @forelse ($exercises as $exercise)
            <x-ui.card wire:key="exercise-{{ $exercise->id }}" class="space-y-4">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-sm font-bold text-white flex items-center gap-1.5 flex-wrap">
                            {{ $exercise->name }}
                            @if (in_array($exercise->id, $staleExerciseIds))
                                <x-ui.badge color="amber">{{ __('routines.performance.stale_test_badge') }}</x-ui.badge>
                                <x-ui.chart-help text="{{ __('routines.performance.stale_test_help') }}" />
                            @endif
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5">
                            {{ $exercise->muscle_group ?: '—' }} &middot; {{ $exercise->sets }} {{ __('routines.exercise.sets_unit') }} &middot; {{ $exercise->reps_range }} {{ __('routines.exercise.reps_unit') }}
                            @if ($exercise->rest_seconds) &middot; {{ $exercise->rest_seconds }}s {{ __('routines.exercise.rest_unit') }} @endif
                        </p>
                        @if ($exercise->notes)
                            <p class="text-xs text-slate-500 mt-1">{{ $exercise->notes }}</p>
                        @endif
                    </div>
                    <div class="flex items-center gap-1 shrink-0">
                        <button type="button" wire:click="editExercise({{ $exercise->id }})" title="{{ __('routines.edit') }}"
                                class="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-cyan-400 transition">
                            <x-ui.icon name="edit" class="w-3.5 h-3.5" />
                        </button>
                        <button type="button" wire:click="deleteExercise({{ $exercise->id }})" wire:confirm="{{ __('routines.exercise.delete_confirm') }}" title="{{ __('routines.delete') }}"
                                class="p-1.5 rounded-lg bg-slate-800 hover:bg-rose-950 text-slate-400 hover:text-rose-400 transition">
                            <x-ui.icon name="trash" class="w-3.5 h-3.5" />
                        </button>
                        @if ($loggingExerciseId !== $exercise->id)
                            <button type="button" wire:click="startLogging({{ $exercise->id }})" title="{{ __('routines.exercise.log_workout') }}"
                                    class="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-emerald-400 transition">
                                <x-ui.icon name="clock" class="w-3.5 h-3.5" />
                            </button>
                        @endif
                    </div>
                </div>

                @if ($loggingExerciseId === $exercise->id)
                    <form wire:submit="logWorkout" class="grid grid-cols-1 sm:grid-cols-5 gap-4 border-t border-slate-800 pt-4">
                        <x-ui.input label="{{ __('routines.workout_log.date') }}" name="workout_date" wire:model="workout_date" type="date" />
                        <x-ui.input label="{{ __('routines.workout_log.weight_kg') }}" name="weight_kg" wire:model="weight_kg" type="number" step="0.01" />
                        <x-ui.input label="{{ __('routines.workout_log.completed_sets') }}" name="completed_sets" wire:model="completed_sets" type="number" />
                        <x-ui.input label="{{ __('routines.workout_log.completed_reps') }}" name="completed_reps" wire:model="completed_reps" type="text" />
                        <x-ui.input label="{{ __('routines.workout_log.rpe') }}" name="rpe" wire:model="rpe" type="number" min="1" max="10" />

                        <div class="sm:col-span-5">
                            <x-ui.input label="{{ __('routines.workout_log.notes') }}" name="notes" wire:model="notes" type="text" />
                        </div>

                        <div class="sm:col-span-5 flex justify-end gap-3">
                            <x-ui.button type="button" wire:click="cancelLogging" variant="secondary">{{ __('routines.workout_log.cancel') }}</x-ui.button>
                            <x-ui.button type="submit" variant="primary">{{ __('routines.workout_log.save') }}</x-ui.button>
                        </div>
                    </form>
                @endif

                @if ($exercise->workoutLogs->isNotEmpty())
                    <div class="overflow-x-auto border-t border-slate-800 pt-4">
                        <table class="min-w-full divide-y divide-slate-800 text-sm">
                            <thead>
                                <tr class="text-[10px] text-slate-400 uppercase">
                                    <th class="text-left py-1 pr-4">{{ __('routines.workout_log.date') }}</th>
                                    <th class="text-left py-1 pr-4">{{ __('routines.workout_log.weight_kg') }}</th>
                                    <th class="text-left py-1 pr-4">{{ __('routines.workout_log.sets') }}</th>
                                    <th class="text-left py-1 pr-4">{{ __('routines.workout_log.reps') }}</th>
                                    <th class="text-left py-1">{{ __('routines.workout_log.rpe_short') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800">
                                @foreach ($exercise->workoutLogs as $log)
                                    <tr wire:key="log-{{ $log->id }}">
                                        <td class="py-1 pr-4 text-slate-200">{{ $log->workout_date->format('Y-m-d') }}</td>
                                        <td class="py-1 pr-4 text-slate-400">{{ $log->weight_kg ?? '—' }}</td>
                                        <td class="py-1 pr-4 text-slate-400">{{ $log->completed_sets ?? '—' }}</td>
                                        <td class="py-1 pr-4 text-slate-400">{{ $log->completed_reps ?? '—' }}</td>
                                        <td class="py-1 text-slate-400">{{ $log->rpe ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-xs text-slate-500 border-t border-slate-800 pt-4">{{ __('routines.workout_log.none_recorded') }}</p>
                @endif

                <div class="border-t border-slate-800 pt-4">
                    <h4 class="text-xs font-bold text-white mb-3 flex items-center gap-1.5 flex-wrap">
                        {{ __('routines.performance.e1rm_title') }}
                        <x-ui.chart-help text="Fuerza máxima estimada (fórmula de Epley) a partir del peso y las repeticiones logradas en cada sesión. Una curva ascendente indica ganancia real de fuerza, no solo más peso levantado con más repeticiones." />
                        @php $strengthLevel = $strengthLevels[$exercise->id] ?? null; @endphp
                        @if ($strengthLevel)
                            @php
                                $levelColors = ['novice' => 'slate', 'intermediate' => 'cyan', 'advanced' => 'amber', 'elite' => 'emerald'];
                            @endphp
                            <x-ui.badge :color="$levelColors[$strengthLevel['level']] ?? 'slate'">
                                {{ __('routines.performance.strength_level_badge', ['level' => __('routines.performance.strength_levels.'.$strengthLevel['level']), 'ratio' => $strengthLevel['ratio']]) }}
                            </x-ui.badge>
                        @endif
                    </h4>
                    @if ($e1rmCharts[$exercise->id]['hasEnoughData'])
                        <div id="e1rm-chart-{{ $exercise->id }}" data-progress-chart="{{ json_encode($e1rmCharts[$exercise->id]) }}" wire:ignore></div>
                    @else
                        <p class="text-xs text-slate-500">{{ __('clients.chart.not_enough_data') }}</p>
                    @endif
                </div>
            </x-ui.card>
        @empty
            <x-ui.card class="text-center text-sm text-slate-400">{{ __('routines.exercise.none_added') }}</x-ui.card>
        @endforelse
    </div>
</div>
