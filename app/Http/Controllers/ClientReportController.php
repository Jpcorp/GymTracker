<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class ClientReportController extends Controller
{
    public function __invoke(Client $client): Response
    {
        Gate::authorize('view', $client);

        $latestEvaluation = $client->evaluationsWithComparison()->last();

        $data = [
            'client' => $client,
            'latestMetric' => $client->physicalMetrics()->orderByDesc('recorded_at')->first(),
            'latestEvaluation' => $latestEvaluation,
            'attendancePercentage' => $client->monthlyAttendancePercentage(),
            'activeRoutine' => $client->routines()
                ->where('is_active', true)
                ->withCount('exercises')
                ->latest('start_date')
                ->first(),
        ];

        $pdf = Pdf::loadView('reports.client-report', $data);

        return $pdf->download('cliente-'.$client->id.'-reporte.pdf');
    }
}
