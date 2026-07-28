<div x-data="{ tab: 'metrics' }" class="space-y-6">

    {{-- Header --}}
    <x-ui.card class="space-y-5">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-5">
            <div class="flex items-start sm:items-center gap-4">
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-gradient-to-tr from-cyan-500 to-blue-600 flex items-center justify-center text-white font-black text-2xl shrink-0 shadow-lg">
                    {{ mb_strtoupper(mb_substr($client->name, 0, 1)) }}
                </div>

                <div class="space-y-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <h1 class="text-xl sm:text-2xl font-black text-white tracking-tight">{{ $client->name }}</h1>
                        @php
                            $statusColor = ['active' => 'emerald', 'paused' => 'amber', 'inactive' => 'rose'][$client->status] ?? 'slate';
                        @endphp
                        <x-ui.badge :color="$statusColor">{{ __('clients.statuses.'.$client->status) }}</x-ui.badge>
                    </div>

                    @if ($client->goal)
                        <p class="text-xs font-semibold text-slate-200">{{ $client->goal }}</p>
                    @endif

                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-[11px] text-slate-400 pt-1">
                        <span>{{ __('clients.email') }}: {{ $client->email }}</span>
                        <span>{{ __('clients.age') }}: {{ $client->birth_date->age }}</span>
                        <span>{{ __('clients.start_date') }}: {{ $client->start_date->format('Y-m-d') }}</span>
                        <span>{{ __('clients.trainer') }}: <strong class="text-slate-300">{{ $client->trainer?->name }}</strong></span>
                    </div>

                    @if ($goalProgress)
                        <div class="pt-2 max-w-sm">
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
                            @if ($goalProgress['days_remaining'] !== null)
                                <p class="text-[11px] text-slate-500 mt-1">
                                    @if ($goalProgress['days_remaining'] < 0)
                                        {{ __('clients.goal_overdue', ['days' => abs($goalProgress['days_remaining'])]) }}
                                    @else
                                        {{ __('clients.goal_days_remaining', ['days' => $goalProgress['days_remaining']]) }}
                                    @endif
                                </p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <x-ui.button href="{{ route('clients.report', $client) }}" variant="secondary">
                    <x-ui.icon name="file-text" class="w-3.5 h-3.5 text-emerald-400" />
                    {{ __('clients.download_pdf') }}
                </x-ui.button>

                <x-ui.button href="{{ route('clients.export', $client) }}" variant="secondary">
                    <x-ui.icon name="file-text" class="w-3.5 h-3.5 text-emerald-400" />
                    {{ __('clients.export_excel') }}
                </x-ui.button>

                <x-ui.button href="{{ route('clients.edit', $client) }}" wire:navigate variant="secondary">
                    <x-ui.icon name="edit" class="w-3.5 h-3.5" />
                    {{ __('clients.edit') }}
                </x-ui.button>

                <x-ui.button href="{{ route('clients.index') }}" wire:navigate variant="secondary">
                    {{ __('clients.back_to_list') }}
                </x-ui.button>
            </div>
        </div>

        {{-- Tabs --}}
        <div class="flex items-center gap-1 overflow-x-auto pb-1 border-b border-slate-800 scrollbar-none">
            <x-ui.tabs :tabs="[
                ['id' => 'metrics', 'label' => __('clients.metrics.title'), 'icon' => 'chart'],
                ['id' => 'photos', 'label' => __('clients.photos.title'), 'icon' => 'camera'],
                ['id' => 'evaluations', 'label' => __('clients.evaluations.title'), 'icon' => 'award'],
                ['id' => 'chart', 'label' => __('clients.chart.title'), 'icon' => 'chart'],
                ['id' => 'attendance', 'label' => __('clients.attendance.title'), 'icon' => 'clock'],
                ['id' => 'wellness', 'label' => __('wellness.title'), 'icon' => 'award'],
            ]" />

            <a href="{{ route('clients.routines.index', $client) }}" wire:navigate
               class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 whitespace-nowrap shrink-0 text-slate-400 hover:text-slate-200 hover:bg-slate-800">
                <x-ui.icon name="dumbbell" class="w-3.5 h-3.5" />
                <span>{{ __('clients.routines') }}</span>
            </a>
        </div>
    </x-ui.card>

    {{-- Tab: Metricas --}}
    <div x-show="tab === 'metrics'" class="space-y-6">
        <x-ui.card>
            <h2 class="text-sm font-bold text-white mb-4">{{ __('clients.metrics.title') }}</h2>

            <form wire:submit="saveMetric" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <x-ui.input label="{{ __('clients.metrics.date') }}" name="recorded_at" wire:model="recorded_at" type="date" />
                <x-ui.input label="{{ __('clients.metrics.weight_kg') }}" name="weight_kg" wire:model="weight_kg" type="number" step="0.01" />
                <x-ui.input label="{{ __('clients.metrics.height_cm') }}" name="height_cm" wire:model="height_cm" type="number" step="0.01" />
                <x-ui.input label="{{ __('clients.metrics.body_fat_percentage') }}" name="body_fat_percentage" wire:model="body_fat_percentage" type="number" step="0.01" />
                <x-ui.input label="{{ __('clients.metrics.metabolic_age') }}" name="metabolic_age" wire:model="metabolic_age" type="number" />
                <x-ui.input label="{{ __('clients.metrics.basal_kcal') }}" name="basal_kcal" wire:model="basal_kcal" type="number" />
                <x-ui.input label="{{ __('clients.metrics.visceral_fat') }}" name="visceral_fat" wire:model="visceral_fat" type="number" />

                <div class="sm:col-span-3 flex justify-end">
                    <x-ui.button type="submit" variant="primary">{{ __('clients.metrics.save') }}</x-ui.button>
                </div>
            </form>
        </x-ui.card>

        <x-ui.card padding="p-0">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-800 text-sm">
                    <thead class="bg-slate-800/50">
                        <tr>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase">{{ __('clients.metrics.date') }}</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase">{{ __('clients.metrics.weight_kg') }}</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase">{{ __('clients.metrics.height_cm') }}</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase">{{ __('clients.metrics.bmi') }}</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase">{{ __('clients.metrics.body_fat_percentage') }}</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase">{{ __('clients.metrics.metabolic_age') }}</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase">{{ __('clients.metrics.basal_kcal') }}</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase">{{ __('clients.metrics.visceral_fat') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @forelse ($metrics as $metric)
                            <tr wire:key="metric-{{ $metric->id }}">
                                <td class="px-4 py-3 text-slate-200">{{ $metric->recorded_at->format('Y-m-d') }}</td>
                                <td class="px-4 py-3 text-slate-400">{{ $metric->weight_kg }}</td>
                                <td class="px-4 py-3 text-slate-400">{{ $metric->height_cm ?? '—' }}</td>
                                <td class="px-4 py-3 text-slate-400">{{ $metric->bmi ?? '—' }}</td>
                                <td class="px-4 py-3 text-slate-400">{{ $metric->body_fat_percentage ?? '—' }}</td>
                                <td class="px-4 py-3 text-slate-400">{{ $metric->metabolic_age ?? '—' }}</td>
                                <td class="px-4 py-3 text-slate-400">{{ $metric->basal_kcal ?? '—' }}</td>
                                <td class="px-4 py-3 text-slate-400">{{ $metric->visceral_fat ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-6 text-center text-slate-400">{{ __('clients.metrics.none_recorded') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card>

        <x-ui.card>
            <h2 class="text-sm font-bold text-white mb-4">{{ __('clients.measurements.title') }}</h2>

            <form wire:submit="saveBodyMeasurement" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <x-ui.input label="{{ __('clients.metrics.date') }}" name="bm_recorded_at" wire:model="bm_recorded_at" type="date" />
                <x-ui.input label="{{ __('clients.measurements.waist_cm') }}" name="bm_waist_cm" wire:model="bm_waist_cm" type="number" step="0.01" />
                <x-ui.input label="{{ __('clients.measurements.hips_cm') }}" name="bm_hips_cm" wire:model="bm_hips_cm" type="number" step="0.01" />
                <x-ui.input label="{{ __('clients.measurements.chest_cm') }}" name="bm_chest_cm" wire:model="bm_chest_cm" type="number" step="0.01" />
                <x-ui.input label="{{ __('clients.measurements.right_arm_cm') }}" name="bm_right_arm_cm" wire:model="bm_right_arm_cm" type="number" step="0.01" />
                <x-ui.input label="{{ __('clients.measurements.left_arm_cm') }}" name="bm_left_arm_cm" wire:model="bm_left_arm_cm" type="number" step="0.01" />
                <x-ui.input label="{{ __('clients.measurements.right_thigh_cm') }}" name="bm_right_thigh_cm" wire:model="bm_right_thigh_cm" type="number" step="0.01" />
                <x-ui.input label="{{ __('clients.measurements.left_thigh_cm') }}" name="bm_left_thigh_cm" wire:model="bm_left_thigh_cm" type="number" step="0.01" />

                <div class="sm:col-span-3 flex justify-end">
                    <x-ui.button type="submit" variant="primary">{{ __('clients.measurements.save') }}</x-ui.button>
                </div>
            </form>
        </x-ui.card>

        <x-ui.card padding="p-0">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-800 text-sm">
                    <thead class="bg-slate-800/50">
                        <tr>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase">{{ __('clients.metrics.date') }}</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase">{{ __('clients.measurements.waist_cm') }}</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase">{{ __('clients.measurements.hips_cm') }}</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase">{{ __('clients.measurements.chest_cm') }}</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase">{{ __('clients.measurements.right_arm_cm') }}</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase">{{ __('clients.measurements.left_arm_cm') }}</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase">{{ __('clients.measurements.right_thigh_cm') }}</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase">{{ __('clients.measurements.left_thigh_cm') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @forelse ($measurements as $measurement)
                            <tr wire:key="measurement-{{ $measurement->id }}">
                                <td class="px-4 py-3 text-slate-200">{{ $measurement->recorded_at->format('Y-m-d') }}</td>
                                <td class="px-4 py-3 text-slate-400">{{ $measurement->waist_cm ?? '—' }}</td>
                                <td class="px-4 py-3 text-slate-400">{{ $measurement->hips_cm ?? '—' }}</td>
                                <td class="px-4 py-3 text-slate-400">{{ $measurement->chest_cm ?? '—' }}</td>
                                <td class="px-4 py-3 text-slate-400">{{ $measurement->right_arm_cm ?? '—' }}</td>
                                <td class="px-4 py-3 text-slate-400">{{ $measurement->left_arm_cm ?? '—' }}</td>
                                <td class="px-4 py-3 text-slate-400">{{ $measurement->right_thigh_cm ?? '—' }}</td>
                                <td class="px-4 py-3 text-slate-400">{{ $measurement->left_thigh_cm ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-6 text-center text-slate-400">{{ __('clients.measurements.none_recorded') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card>

        <x-ui.card>
            <h2 class="text-sm font-bold text-white mb-4 flex items-center gap-1.5">
                {{ __('clients.chart.symmetry_title') }}
                <x-ui.chart-help text="Compara la medida derecha vs. izquierda de brazo y muslo del registro más reciente. Una diferencia mayor a 1-2 cm puede indicar un desequilibrio muscular a corregir en la rutina." />
            </h2>

            @if ($symmetryChartData['hasEnoughData'])
                <div id="symmetry-chart-{{ $client->id }}" data-progress-chart="{{ json_encode($symmetryChartData) }}" wire:ignore></div>
            @else
                <p class="text-sm text-slate-400">{{ __('clients.chart.not_enough_data') }}</p>
            @endif
        </x-ui.card>
    </div>

    {{-- Tab: Fotos --}}
    <div x-show="tab === 'photos'" class="space-y-6">
        <x-ui.card>
            <h2 class="text-sm font-bold text-white mb-4">{{ __('clients.photos.title') }}</h2>

            <form wire:submit="uploadPhotos" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <x-ui.input label="{{ __('clients.metrics.date') }}" name="photo_date" wire:model="photo_date" type="date" />

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">{{ __('clients.photos.front') }}</label>
                    <input type="file" wire:model="front_photo" accept="image/*" class="block w-full text-xs text-slate-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-slate-800 file:text-slate-200 file:text-xs hover:file:bg-slate-700" />
                    @error('front_photo') <span class="text-[11px] text-rose-400">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">{{ __('clients.photos.back') }}</label>
                    <input type="file" wire:model="back_photo" accept="image/*" class="block w-full text-xs text-slate-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-slate-800 file:text-slate-200 file:text-xs hover:file:bg-slate-700" />
                    @error('back_photo') <span class="text-[11px] text-rose-400">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">{{ __('clients.photos.left_side') }}</label>
                    <input type="file" wire:model="left_side_photo" accept="image/*" class="block w-full text-xs text-slate-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-slate-800 file:text-slate-200 file:text-xs hover:file:bg-slate-700" />
                    @error('left_side_photo') <span class="text-[11px] text-rose-400">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">{{ __('clients.photos.right_side') }}</label>
                    <input type="file" wire:model="right_side_photo" accept="image/*" class="block w-full text-xs text-slate-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-slate-800 file:text-xs hover:file:bg-slate-700" />
                    @error('right_side_photo') <span class="text-[11px] text-rose-400">{{ $message }}</span> @enderror
                </div>

                <div class="sm:col-span-2 lg:col-span-5 flex justify-end">
                    <x-ui.button type="submit" variant="primary">{{ __('clients.photos.save') }}</x-ui.button>
                </div>
            </form>
        </x-ui.card>

        <x-ui.card class="space-y-6">
            <h2 class="text-sm font-bold text-white">{{ __('clients.photos.gallery') }}</h2>

            @forelse ($photosByDate as $date => $photos)
                <div wire:key="gallery-{{ $date }}">
                    <h3 class="text-xs font-semibold text-slate-300 mb-2">{{ $date }}</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        @foreach (['front' => __('clients.photos.front'), 'back' => __('clients.photos.back'), 'left_side' => __('clients.photos.left_side'), 'right_side' => __('clients.photos.right_side')] as $viewType => $label)
                            @php $photo = $photos->firstWhere('view_type', $viewType); @endphp
                            <div class="text-center">
                                @if ($photo)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($photo->photo_path) }}" alt="{{ $label }}" class="w-full h-40 object-cover rounded-xl border border-slate-800" />
                                @else
                                    <div class="w-full h-40 rounded-xl bg-slate-800/60 border border-slate-800 flex items-center justify-center text-[11px] text-slate-500">{{ __('clients.photos.no_photo') }}</div>
                                @endif
                                <span class="text-[11px] text-slate-400">{{ $label }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <p class="text-sm text-slate-400">{{ __('clients.photos.none_uploaded') }}</p>
            @endforelse
        </x-ui.card>
    </div>

    {{-- Tab: Evaluaciones --}}
    <div x-show="tab === 'evaluations'" class="space-y-6">
        <x-ui.card class="space-y-6">
            <h2 class="text-sm font-bold text-white">{{ __('clients.evaluations.title') }}</h2>

            @forelse ($evaluations as $row)
                @php [$evaluation, $metric, $comparison, $achievements] = [$row['evaluation'], $row['metric'], $row['comparison'], $row['achievements']]; @endphp
                <div wire:key="evaluation-{{ $evaluation->id }}" class="relative pl-5 border-l-2 border-slate-800">
                    <span class="absolute -left-[7px] top-1.5 w-3 h-3 rounded-full bg-amber-400"></span>
                <div class="border border-slate-800 rounded-xl p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-xs font-bold text-white flex items-center gap-1.5">
                            <x-ui.icon name="award" class="w-3.5 h-3.5 text-amber-400" />
                            {{ __('clients.evaluations.evaluation') }} #{{ $evaluation->evaluation_number }}
                        </h3>
                        <span class="text-[11px] text-slate-400">{{ $evaluation->period_start->format('Y-m-d') }} &rarr; {{ $evaluation->period_end->format('Y-m-d') }}</span>
                    </div>

                    @if (! $metric)
                        <p class="text-sm text-slate-400">{{ __('clients.evaluations.none_recorded') }}</p>
                    @elseif (! $row['has_previous'])
                        <table class="min-w-full text-sm">
                            <tbody class="divide-y divide-slate-800">
                                @foreach (['weight_kg' => __('clients.metrics.weight_kg'), 'body_fat_percentage' => __('clients.metrics.body_fat_percentage'), 'bmi' => __('clients.metrics.bmi'), 'basal_kcal' => __('clients.metrics.basal_kcal'), 'visceral_fat' => __('clients.metrics.visceral_fat')] as $field => $label)
                                    @if ($metric->$field !== null)
                                        <tr>
                                            <td class="py-1 pr-4 text-slate-400">{{ $label }}</td>
                                            <td class="py-1 font-medium text-slate-200">{{ $metric->$field }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-[10px] text-slate-400 uppercase">
                                    <th class="text-left py-1 pr-4">{{ __('clients.evaluations.metric') }}</th>
                                    <th class="text-left py-1 pr-4">{{ __('clients.evaluations.current') }}</th>
                                    <th class="text-left py-1 pr-4">{{ __('clients.evaluations.previous') }}</th>
                                    <th class="text-left py-1">{{ __('clients.evaluations.delta') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800">
                                @foreach (['weight_kg' => __('clients.metrics.weight_kg'), 'body_fat_percentage' => __('clients.metrics.body_fat_percentage'), 'bmi' => __('clients.metrics.bmi'), 'basal_kcal' => __('clients.metrics.basal_kcal'), 'visceral_fat' => __('clients.metrics.visceral_fat')] as $field => $label)
                                    @if (isset($comparison[$field]))
                                        <tr>
                                            <td class="py-1 pr-4 text-slate-400">{{ $label }}</td>
                                            <td class="py-1 pr-4 font-medium text-slate-200">{{ $comparison[$field]['current'] }}</td>
                                            <td class="py-1 pr-4 text-slate-500">{{ $comparison[$field]['previous'] }}</td>
                                            <td class="py-1 {{ $comparison[$field]['delta'] < 0 ? 'text-rose-400' : ($comparison[$field]['delta'] > 0 ? 'text-emerald-400' : 'text-slate-500') }}">{{ $comparison[$field]['delta'] }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    @endif

                    @if (! empty($achievements))
                        <div class="mt-3 pt-3 border-t border-slate-800">
                            <h4 class="text-[11px] font-bold text-amber-400 mb-1.5">{{ __('clients.evaluations.achievements') }}</h4>
                            <ul class="space-y-1">
                                @foreach ($achievements as $achievement)
                                    <li class="text-xs text-slate-300 flex items-start gap-1.5">
                                        <span class="text-emerald-400">&check;</span>
                                        <span>{{ $achievement }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
                </div>
            @empty
                <p class="text-sm text-slate-400">{{ __('clients.evaluations.none_generated') }}</p>
            @endforelse
        </x-ui.card>
    </div>

    {{-- Tab: Grafico --}}
    <div x-show="tab === 'chart'" class="space-y-6">
        <x-ui.card>
            <h2 class="text-sm font-bold text-white mb-4 flex items-center gap-1.5">
                {{ __('clients.chart.title') }}
                <x-ui.chart-help text="Evolución de peso, grasa corporal e IMC en el tiempo. Una tendencia descendente en peso y grasa corporal suele indicar progreso hacia la mayoría de los objetivos de composición corporal." />
            </h2>

            @if ($chartData['hasEnoughData'])
                {{-- ponytail: wire:ignore keeps ApexCharts' injected SVG safe from Livewire's morph, but it also means
                     the chart won't refresh in place after saveMetric(); reload/navigate to see a newly added metric.
                     Upgrade path: dispatch a browser event with fresh chartData and re-render on it if live updates matter. --}}
                <div id="progress-chart-{{ $client->id }}" data-progress-chart="{{ json_encode($chartData) }}" wire:ignore></div>
            @else
                <p class="text-sm text-slate-400">{{ __('clients.chart.not_enough_data') }}</p>
            @endif
        </x-ui.card>
    </div>

    {{-- Tab: Asistencia --}}
    <div x-show="tab === 'attendance'" class="space-y-6">
        <x-ui.card>
            <div class="flex items-center justify-between mb-4">
                <x-ui.button wire:click="previousMonth" aria-label="{{ __('clients.attendance.calendar.previous') }}">
                    <x-ui.icon name="chevron" class="w-4 h-4 rotate-180" />
                </x-ui.button>
                <h2 class="text-sm font-bold text-white capitalize">{{ $calendarLabel }}</h2>
                <x-ui.button wire:click="nextMonth" aria-label="{{ __('clients.attendance.calendar.next') }}">
                    <x-ui.icon name="chevron" class="w-4 h-4" />
                </x-ui.button>
            </div>

            <div class="grid grid-cols-7 gap-1 text-center">
                @foreach ($calendarWeekdayLabels as $label)
                    <div class="text-[10px] font-bold text-slate-400 uppercase py-1">{{ $label }}</div>
                @endforeach

                @foreach ($calendarWeeks as $week)
                    @foreach ($week as $day)
                        <div
                            wire:key="cal-{{ $day['date']->format('Y-m-d') }}"
                            class="relative aspect-square flex items-center justify-center rounded-lg text-xs
                                {{ $day['inMonth'] ? 'text-slate-200' : 'text-slate-600' }}
                                {{ $day['isToday'] ? 'ring-1 ring-cyan-400' : '' }}"
                        >
                            {{ $day['date']->day }}
                            @if ($day['attended'])
                                <span class="absolute bottom-1 size-1.5 rounded-full bg-cyan-400"></span>
                            @endif
                        </div>
                    @endforeach
                @endforeach
            </div>
        </x-ui.card>

        <x-ui.card>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-bold text-white">{{ __('clients.attendance.title') }}</h2>
                <span class="text-xs text-slate-400">{{ __('clients.attendance.this_month') }}: <span class="font-bold text-cyan-400">{{ $attendancePercentage }}%</span></span>
            </div>

            <form wire:submit="checkIn" class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <x-ui.input label="{{ __('clients.metrics.date') }}" name="attendance_date" wire:model="attendance_date" type="date" />
                <x-ui.input label="{{ __('clients.attendance.check_in') }}" name="check_in" wire:model="check_in" type="time" />
                <x-ui.input label="{{ __('clients.attendance.check_out') }}" name="check_out" wire:model="check_out" type="time" />

                <x-ui.select label="{{ __('clients.attendance.session_type') }}" name="session_type" wire:model="session_type">
                    <option value="personal">{{ __('clients.attendance.sessions.personal') }}</option>
                    <option value="group">{{ __('clients.attendance.sessions.group') }}</option>
                    <option value="free">{{ __('clients.attendance.sessions.free') }}</option>
                </x-ui.select>

                <div class="sm:col-span-4 flex justify-end">
                    <x-ui.button type="submit" variant="primary">{{ __('clients.attendance.save') }}</x-ui.button>
                </div>
            </form>
        </x-ui.card>

        <x-ui.card padding="p-0">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-800 text-sm">
                    <thead class="bg-slate-800/50">
                        <tr>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase">{{ __('clients.metrics.date') }}</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase">{{ __('clients.attendance.check_in') }}</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase">{{ __('clients.attendance.check_out') }}</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase">{{ __('clients.attendance.session_type') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @forelse ($attendances as $attendance)
                            <tr wire:key="attendance-{{ $attendance->id }}">
                                <td class="px-4 py-3 text-slate-200">{{ $attendance->attendance_date->format('Y-m-d') }}</td>
                                <td class="px-4 py-3 text-slate-400">{{ $attendance->check_in ? \Illuminate\Support\Carbon::parse($attendance->check_in)->format('H:i') : '—' }}</td>
                                <td class="px-4 py-3 text-slate-400">{{ $attendance->check_out ? \Illuminate\Support\Carbon::parse($attendance->check_out)->format('H:i') : '—' }}</td>
                                <td class="px-4 py-3 text-slate-400">{{ __('clients.attendance.sessions.'.$attendance->session_type) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-slate-400">{{ __('clients.attendance.none_recorded') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card>

        <x-ui.card>
            <h2 class="text-sm font-bold text-white mb-4 flex items-center gap-1.5">
                {{ __('clients.chart.acwr_title') }}
                <x-ui.chart-help text="Compara la carga de entrenamiento de la última semana contra el promedio de las últimas 4. Entre 0.8 y 1.5 es zona saludable: por debajo hay pérdida de forma física, por encima aumenta el riesgo de lesión o sobreentrenamiento." />
            </h2>

            @if ($acwrChartData['hasEnoughData'])
                <div id="acwr-chart-{{ $client->id }}" data-progress-chart="{{ json_encode($acwrChartData) }}" wire:ignore></div>
                <p class="text-xs text-slate-400 mt-2">{{ __('clients.chart.acwr_caption') }}</p>
            @else
                <p class="text-sm text-slate-400">{{ __('clients.chart.not_enough_data') }}</p>
            @endif
        </x-ui.card>
    </div>

    {{-- Tab: Bienestar --}}
    <div x-show="tab === 'wellness'" class="space-y-6">
        <x-ui.card>
            <h2 class="text-sm font-bold text-white mb-4">{{ __('wellness.mood.title') }}</h2>

            <form wire:submit="saveMood" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <x-ui.input label="{{ __('wellness.mood.week_start') }}" name="mood_week_start" wire:model="mood_week_start" type="date" />
                <x-ui.input label="{{ __('wellness.mood.week_end') }}" name="mood_week_end" wire:model="mood_week_end" type="date" />
                <div></div>
                <x-ui.input label="{{ __('wellness.mood.mood_level') }}" name="mood_level" wire:model="mood_level" type="number" min="1" max="10" />
                <x-ui.input label="{{ __('wellness.mood.energy_level') }}" name="mood_energy_level" wire:model="mood_energy_level" type="number" min="1" max="10" />
                <x-ui.input label="{{ __('wellness.mood.motivation_level') }}" name="mood_motivation_level" wire:model="mood_motivation_level" type="number" min="1" max="10" />
                <x-ui.input label="{{ __('wellness.mood.sleep_hours') }}" name="mood_sleep_hours" wire:model="mood_sleep_hours" type="number" step="0.5" min="0" max="24" />
                <x-ui.input label="{{ __('wellness.mood.sleep_quality') }}" name="mood_sleep_quality" wire:model="mood_sleep_quality" type="number" min="1" max="10" />
                <x-ui.textarea label="{{ __('wellness.mood.notes') }}" name="mood_notes" wire:model="mood_notes" class="sm:col-span-3" rows="2" />

                <div class="sm:col-span-3 flex justify-end">
                    <x-ui.button type="submit" variant="primary">{{ __('wellness.mood.save') }}</x-ui.button>
                </div>
            </form>
        </x-ui.card>

        <x-ui.card padding="p-0">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-800 text-sm">
                    <thead class="bg-slate-800/50">
                        <tr>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase">{{ __('wellness.mood.week_start') }}</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase">{{ __('wellness.mood.week_end') }}</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase">{{ __('wellness.mood.mood_level') }}</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase">{{ __('wellness.mood.energy_level') }}</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase">{{ __('wellness.mood.motivation_level') }}</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase">{{ __('wellness.mood.sleep_hours') }}</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase">{{ __('wellness.mood.sleep_quality') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @forelse ($moodRecords as $mood)
                            <tr wire:key="mood-{{ $mood->id }}">
                                <td class="px-4 py-3 text-slate-200">{{ $mood->week_start->format('Y-m-d') }}</td>
                                <td class="px-4 py-3 text-slate-400">{{ $mood->week_end->format('Y-m-d') }}</td>
                                <td class="px-4 py-3 text-slate-400">{{ $mood->mood_level }}</td>
                                <td class="px-4 py-3 text-slate-400">{{ $mood->energy_level ?? '—' }}</td>
                                <td class="px-4 py-3 text-slate-400">{{ $mood->motivation_level ?? '—' }}</td>
                                <td class="px-4 py-3 text-slate-400">{{ $mood->sleep_hours ?? '—' }}</td>
                                <td class="px-4 py-3 text-slate-400">{{ $mood->sleep_quality ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-6 text-center text-slate-400">{{ __('wellness.mood.none_recorded') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card>

        <x-ui.card>
            <h2 class="text-sm font-bold text-white mb-4 flex items-center gap-1.5">
                {{ __('wellness.mood.title') }}
                <x-ui.chart-help text="Ánimo, energía y motivación semanales en escala 1-10. Una tendencia sostenida a la baja puede anticipar fatiga o riesgo de abandono antes de que se note en la asistencia." />
            </h2>

            @if ($moodChartData['hasEnoughData'])
                <div id="mood-chart-{{ $client->id }}" data-progress-chart="{{ json_encode($moodChartData) }}" wire:ignore></div>
            @else
                <p class="text-sm text-slate-400">{{ __('clients.chart.not_enough_data') }}</p>
            @endif
        </x-ui.card>

        <x-ui.card>
            <h2 class="text-sm font-bold text-white mb-4">{{ __('wellness.nutrition.title') }}</h2>

            <form wire:submit="saveNutritionLog" class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <x-ui.input label="{{ __('wellness.nutrition.date') }}" name="nutrition_log_date" wire:model="nutrition_log_date" type="date" />

                <x-ui.select label="{{ __('wellness.nutrition.compliance') }}" name="nutrition_compliance" wire:model="nutrition_compliance">
                    <option value="complete">{{ __('wellness.nutrition.compliances.complete') }}</option>
                    <option value="partial">{{ __('wellness.nutrition.compliances.partial') }}</option>
                    <option value="missed">{{ __('wellness.nutrition.compliances.missed') }}</option>
                </x-ui.select>

                <x-ui.input label="{{ __('wellness.nutrition.meals_logged') }}" name="nutrition_meals_logged" wire:model="nutrition_meals_logged" type="number" min="0" />
                <x-ui.input label="{{ __('wellness.nutrition.meals_planned') }}" name="nutrition_meals_planned" wire:model="nutrition_meals_planned" type="number" min="0" />
                <x-ui.textarea label="{{ __('wellness.nutrition.notes') }}" name="nutrition_notes" wire:model="nutrition_notes" class="sm:col-span-4" rows="2" />

                <div class="sm:col-span-4 flex justify-end">
                    <x-ui.button type="submit" variant="primary">{{ __('wellness.nutrition.save') }}</x-ui.button>
                </div>
            </form>
        </x-ui.card>

        <x-ui.card padding="p-0">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-800 text-sm">
                    <thead class="bg-slate-800/50">
                        <tr>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase">{{ __('wellness.nutrition.date') }}</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase">{{ __('wellness.nutrition.compliance') }}</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase">{{ __('wellness.nutrition.meals_logged') }}</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase">{{ __('wellness.nutrition.meals_planned') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @forelse ($nutritionLogs as $log)
                            <tr wire:key="nutrition-{{ $log->id }}">
                                <td class="px-4 py-3 text-slate-200">{{ $log->log_date->format('Y-m-d') }}</td>
                                <td class="px-4 py-3 text-slate-400">{{ __('wellness.nutrition.compliances.'.$log->compliance) }}</td>
                                <td class="px-4 py-3 text-slate-400">{{ $log->meals_logged ?? '—' }}</td>
                                <td class="px-4 py-3 text-slate-400">{{ $log->meals_planned ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-slate-400">{{ __('wellness.nutrition.none_recorded') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card>

        <x-ui.card>
            <h2 class="text-sm font-bold text-white mb-4 flex items-center gap-1.5">
                {{ __('wellness.nutrition.title') }}
                <x-ui.chart-help text="Porcentaje de cumplimiento del plan alimentario por registro. Valores sostenidos bajo 70% sugieren revisar el plan o reforzar la adherencia con el cliente." />
            </h2>

            @if ($nutritionChartData['hasEnoughData'])
                <div id="nutrition-chart-{{ $client->id }}" data-progress-chart="{{ json_encode($nutritionChartData) }}" wire:ignore></div>
            @else
                <p class="text-sm text-slate-400">{{ __('clients.chart.not_enough_data') }}</p>
            @endif
        </x-ui.card>

        <x-ui.card>
            <h2 class="text-sm font-bold text-white mb-4">{{ __('wellness.satisfaction.title') }}</h2>

            <form wire:submit="saveSatisfactionSurvey" class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <x-ui.input label="{{ __('wellness.satisfaction.date') }}" name="satisfaction_survey_date" wire:model="satisfaction_survey_date" type="date" />
                <x-ui.input label="{{ __('wellness.satisfaction.overall_satisfaction') }}" name="satisfaction_overall" wire:model="satisfaction_overall" type="number" min="1" max="10" />
                <x-ui.input label="{{ __('wellness.satisfaction.trainer_satisfaction') }}" name="satisfaction_trainer" wire:model="satisfaction_trainer" type="number" min="1" max="10" />
                <x-ui.input label="{{ __('wellness.satisfaction.facilities_satisfaction') }}" name="satisfaction_facilities" wire:model="satisfaction_facilities" type="number" min="1" max="10" />
                <x-ui.input label="{{ __('wellness.satisfaction.routines_satisfaction') }}" name="satisfaction_routines" wire:model="satisfaction_routines" type="number" min="1" max="10" />
                <x-ui.textarea label="{{ __('wellness.satisfaction.comments') }}" name="satisfaction_comments" wire:model="satisfaction_comments" class="sm:col-span-3" rows="2" />

                <div class="sm:col-span-4 flex justify-end">
                    <x-ui.button type="submit" variant="primary">{{ __('wellness.satisfaction.save') }}</x-ui.button>
                </div>
            </form>
        </x-ui.card>

        <x-ui.card padding="p-0">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-800 text-sm">
                    <thead class="bg-slate-800/50">
                        <tr>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase">{{ __('wellness.satisfaction.date') }}</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase">{{ __('wellness.satisfaction.overall_satisfaction') }}</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase">{{ __('wellness.satisfaction.trainer_satisfaction') }}</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase">{{ __('wellness.satisfaction.facilities_satisfaction') }}</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase">{{ __('wellness.satisfaction.routines_satisfaction') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @forelse ($satisfactionSurveys as $survey)
                            <tr wire:key="satisfaction-{{ $survey->id }}">
                                <td class="px-4 py-3 text-slate-200">{{ $survey->survey_date->format('Y-m-d') }}</td>
                                <td class="px-4 py-3 text-slate-400">{{ $survey->overall_satisfaction }}</td>
                                <td class="px-4 py-3 text-slate-400">{{ $survey->trainer_satisfaction ?? '—' }}</td>
                                <td class="px-4 py-3 text-slate-400">{{ $survey->facilities_satisfaction ?? '—' }}</td>
                                <td class="px-4 py-3 text-slate-400">{{ $survey->routines_satisfaction ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-slate-400">{{ __('wellness.satisfaction.none_recorded') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card>
    </div>

</div>
