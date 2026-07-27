<?php

namespace App\Http\Controllers;

use App\Exports\ClientMetricsExport;
use App\Models\Client;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ClientExportController extends Controller
{
    public function __invoke(Client $client): StreamedResponse
    {
        Gate::authorize('view', $client);

        return (new ClientMetricsExport($client))->download('cliente-'.$client->id.'-datos.xlsx');
    }
}
