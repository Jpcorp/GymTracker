<?php

namespace App\Exports;

use App\Models\Client;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ClientMetricsExport
{
    public function __construct(private Client $client) {}

    public function download(string $filename): StreamedResponse
    {
        $spreadsheet = new Spreadsheet;

        $this->fillMetricsSheet($spreadsheet->getActiveSheet());
        $this->fillMeasurementsSheet($spreadsheet->createSheet());

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function fillMetricsSheet(Worksheet $sheet): void
    {
        $sheet->setTitle('Métricas físicas');

        $sheet->fromArray([
            'Fecha', 'Peso (kg)', 'Altura (cm)', 'Grasa corporal (%)',
            'IMC', 'Edad metabólica', 'Kcal basales', 'Grasa visceral',
        ], null, 'A1');

        $rows = $this->client->physicalMetrics()->orderBy('recorded_at')->get()
            ->map(fn ($metric) => [
                $metric->recorded_at->format('Y-m-d'),
                $metric->weight_kg,
                $metric->height_cm,
                $metric->body_fat_percentage,
                $metric->bmi,
                $metric->metabolic_age,
                $metric->basal_kcal,
                $metric->visceral_fat,
            ])->all();

        $sheet->fromArray($rows, null, 'A2');
    }

    private function fillMeasurementsSheet(Worksheet $sheet): void
    {
        $sheet->setTitle('Medidas corporales');

        $sheet->fromArray([
            'Fecha', 'Cintura (cm)', 'Cadera (cm)', 'Pecho (cm)',
            'Brazo derecho (cm)', 'Brazo izquierdo (cm)', 'Muslo derecho (cm)', 'Muslo izquierdo (cm)',
        ], null, 'A1');

        $rows = $this->client->bodyMeasurements()->orderBy('recorded_at')->get()
            ->map(fn ($m) => [
                $m->recorded_at->format('Y-m-d'),
                $m->waist_cm,
                $m->hips_cm,
                $m->chest_cm,
                $m->right_arm_cm,
                $m->left_arm_cm,
                $m->right_thigh_cm,
                $m->left_thigh_cm,
            ])->all();

        $sheet->fromArray($rows, null, 'A2');
    }
}
