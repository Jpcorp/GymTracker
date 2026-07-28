<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Contracts\View\View;

class ClientPortalController extends Controller
{
    /**
     * Read-only self-service view for a client, reachable only via a signed URL the trainer
     * shares — no client login/auth system, matches this app's scope (personal-training gym,
     * not a consumer product). The `signed` route middleware rejects any tampered or expired URL.
     */
    public function __invoke(Client $client): View
    {
        return view('portal.show', [
            'client' => $client,
            'latestMetric' => $client->physicalMetrics()->orderByDesc('recorded_at')->first(),
            'goalProgress' => $client->goalProgress(),
            'evaluations' => $client->evaluationsWithComparison(),
            'chartData' => $this->progressChartData($client),
            'attendancePercentage' => $client->monthlyAttendancePercentage(),
        ]);
    }

    private function progressChartData(Client $client): array
    {
        $metrics = $client->physicalMetrics()->orderBy('recorded_at')->get();

        $series = [];
        foreach (['weight_kg' => 'Peso (kg)', 'body_fat_percentage' => 'Grasa corporal (%)', 'bmi' => 'IMC'] as $field => $label) {
            if ($metrics->every(fn ($metric) => $metric->$field === null)) {
                continue;
            }

            $series[] = [
                'name' => $label,
                'data' => $metrics->map(fn ($metric) => $metric->$field !== null ? (float) $metric->$field : null)->values()->all(),
            ];
        }

        return [
            'labels' => $metrics->map(fn ($metric) => $metric->recorded_at->format('Y-m-d'))->values()->all(),
            'series' => $series,
            'hasEnoughData' => $metrics->count() >= 2,
        ];
    }
}
