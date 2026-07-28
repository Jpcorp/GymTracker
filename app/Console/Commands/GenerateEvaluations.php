<?php

namespace App\Console\Commands;

use App\Models\BodyMeasurement;
use App\Models\BodyPhoto;
use App\Models\Client;
use App\Models\Evaluation;
use App\Models\PhysicalMetric;
use App\Notifications\EvaluationGenerated;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class GenerateEvaluations extends Command
{
    protected $signature = 'evaluations:generate';

    protected $description = 'Generate the next 21-day evaluation for every active client whose period has elapsed';

    public function handle(): int
    {
        $today = today();
        $generated = 0;

        Client::where('status', 'active')->each(function (Client $client) use ($today, &$generated) {
            if ($client->daysUntilNextEvaluation() > 0) {
                return;
            }

            $last = $client->evaluations()->orderByDesc('evaluation_number')->first();

            $anchor = $last?->period_end ?? $client->start_date;

            $periodStart = $last ? $anchor->copy()->addDay() : $anchor->copy();
            $periodEnd = $periodStart->copy()->addDays(20);

            // idempotency: don't duplicate if this exact period already exists (e.g. command run twice same day)
            $evaluation = Evaluation::firstOrCreate(
                [
                    'client_id' => $client->id,
                    'period_start' => $periodStart->toDateString(),
                    'period_end' => $periodEnd->toDateString(),
                ],
                [
                    'evaluation_number' => ($last->evaluation_number ?? 0) + 1,
                    'evaluated_at' => $today->toDateString(),
                ]
            );

            if (! $evaluation->wasRecentlyCreated) {
                return;
            }

            $generated++;
            $this->backfillEvaluationId($client, $evaluation, $periodStart, $periodEnd);

            if ($client->trainer) {
                Notification::send($client->trainer, new EvaluationGenerated($evaluation));
            }
        });

        $this->info("Generated {$generated} evaluations.");

        return self::SUCCESS;
    }

    // ponytail: one evaluation created per client per run (matches spec's single 21-day check);
    // a client missed for multiple periods catches up over several daily scheduler runs, not in one call.
    private function backfillEvaluationId(Client $client, Evaluation $evaluation, $periodStart, $periodEnd): void
    {
        PhysicalMetric::where('client_id', $client->id)
            ->whereNull('evaluation_id')
            ->whereBetween('recorded_at', [$periodStart->toDateString(), $periodEnd->toDateString()])
            ->update(['evaluation_id' => $evaluation->id]);

        BodyMeasurement::where('client_id', $client->id)
            ->whereNull('evaluation_id')
            ->whereBetween('recorded_at', [$periodStart->toDateString(), $periodEnd->toDateString()])
            ->update(['evaluation_id' => $evaluation->id]);

        BodyPhoto::where('client_id', $client->id)
            ->whereNull('evaluation_id')
            ->whereBetween('photo_date', [$periodStart->toDateString(), $periodEnd->toDateString()])
            ->update(['evaluation_id' => $evaluation->id]);
    }
}
