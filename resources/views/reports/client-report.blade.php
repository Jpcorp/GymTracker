<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ __('reports.title') }} - {{ $client->name }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        h1 { font-size: 20px; margin-bottom: 4px; }
        h2 { font-size: 14px; margin-top: 20px; margin-bottom: 8px; border-bottom: 1px solid #ccc; padding-bottom: 4px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { text-align: left; padding: 4px 8px; border-bottom: 1px solid #eee; }
        th { background-color: #f5f5f5; }
        .muted { color: #888; font-style: italic; }
        .label { font-weight: bold; width: 30%; }
    </style>
</head>
<body>
    <h1>{{ $client->name }}</h1>

    <table>
        <tr><td class="label">{{ __('clients.email') }}</td><td>{{ $client->email }}</td></tr>
        <tr><td class="label">{{ __('clients.birth_date') }}</td><td>{{ $client->birth_date->format('Y-m-d') }} ({{ $client->birth_date->age }} {{ __('clients.years') }})</td></tr>
        <tr><td class="label">{{ __('clients.start_date') }}</td><td>{{ $client->start_date->format('Y-m-d') }}</td></tr>
        <tr><td class="label">{{ __('clients.goal') }}</td><td>{{ $client->goal ?: '—' }}</td></tr>
        <tr><td class="label">{{ __('clients.status') }}</td><td>{{ __('clients.statuses.'.$client->status) }}</td></tr>
        <tr><td class="label">{{ __('clients.trainer') }}</td><td>{{ $client->trainer->name ?? '—' }}</td></tr>
    </table>

    <h2>{{ __('reports.evaluation_comparison') }}</h2>
    @if ($latestEvaluation)
        <p>
            {{ __('reports.evaluation') }} #{{ $latestEvaluation['evaluation']->evaluation_number }}
            ({{ $latestEvaluation['evaluation']->evaluated_at->format('Y-m-d') }})
        </p>
        @if (count($latestEvaluation['comparison']) > 0)
            <table>
                <thead>
                    <tr>
                        <th>{{ __('reports.metric') }}</th>
                        <th>{{ __('reports.current') }}</th>
                        <th>{{ __('reports.previous') }}</th>
                        <th>{{ __('reports.delta') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach (['weight_kg' => __('clients.metrics.weight_kg'), 'body_fat_percentage' => __('clients.metrics.body_fat_percentage'), 'bmi' => __('clients.metrics.bmi'), 'basal_kcal' => __('clients.metrics.basal_kcal'), 'visceral_fat' => __('clients.metrics.visceral_fat')] as $field => $label)
                        @if (isset($latestEvaluation['comparison'][$field]))
                            <tr>
                                <td>{{ $label }}</td>
                                <td>{{ $latestEvaluation['comparison'][$field]['current'] }}</td>
                                <td>{{ $latestEvaluation['comparison'][$field]['previous'] }}</td>
                                <td>{{ $latestEvaluation['comparison'][$field]['delta'] }}</td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="muted">{{ __('reports.not_enough_data') }}</p>
        @endif

        @if (! empty($latestEvaluation['achievements']))
            <h2>{{ __('reports.achievements') }}</h2>
            <ul>
                @foreach ($latestEvaluation['achievements'] as $achievement)
                    <li>{{ $achievement }}</li>
                @endforeach
            </ul>
        @endif
    @else
        <p class="muted">{{ __('reports.none_generated') }}</p>
    @endif

    <h2>{{ __('reports.latest_metrics') }}</h2>
    @if ($latestMetric)
        <table>
            <tr><td class="label">{{ __('clients.metrics.date') }}</td><td>{{ $latestMetric->recorded_at->format('Y-m-d') }}</td></tr>
            <tr><td class="label">{{ __('clients.metrics.weight_kg') }}</td><td>{{ $latestMetric->weight_kg ?? '—' }}</td></tr>
            <tr><td class="label">{{ __('clients.metrics.height_cm') }}</td><td>{{ $latestMetric->height_cm ?? '—' }}</td></tr>
            <tr><td class="label">{{ __('clients.metrics.body_fat_percentage') }}</td><td>{{ $latestMetric->body_fat_percentage ?? '—' }}</td></tr>
            <tr><td class="label">{{ __('clients.metrics.bmi') }}</td><td>{{ $latestMetric->bmi ?? '—' }}</td></tr>
            <tr><td class="label">{{ __('clients.metrics.metabolic_age') }}</td><td>{{ $latestMetric->metabolic_age ?? '—' }}</td></tr>
            <tr><td class="label">{{ __('clients.metrics.basal_kcal') }}</td><td>{{ $latestMetric->basal_kcal ?? '—' }}</td></tr>
            <tr><td class="label">{{ __('clients.metrics.visceral_fat') }}</td><td>{{ $latestMetric->visceral_fat ?? '—' }}</td></tr>
        </table>
    @else
        <p class="muted">{{ __('clients.metrics.none_recorded') }}</p>
    @endif

    <h2>{{ __('reports.attendance') }}</h2>
    <p>{{ __('reports.this_month') }}: {{ $attendancePercentage }}%</p>

    <h2>{{ __('reports.active_routine') }}</h2>
    @if ($activeRoutine)
        <p>{{ $activeRoutine->name }} — {{ $activeRoutine->exercises_count }} {{ __('reports.exercises') }}</p>
    @else
        <p class="muted">{{ __('reports.no_active_routine') }}</p>
    @endif
</body>
</html>
