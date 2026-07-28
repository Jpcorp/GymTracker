<?php

namespace App\Console\Commands;

use App\Models\Client;
use App\Notifications\ClientAbsenceAlert;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class AlertAbsences extends Command
{
    protected $signature = 'attendance:alert-absences';

    protected $description = 'Notify trainers about active clients silent for exactly 7 days (dropout risk alert)';

    private const THRESHOLD_DAYS = 7;

    public function handle(): int
    {
        $today = now()->startOfDay();
        $alerted = 0;

        Client::where('status', 'active')
            ->whereNotNull('trainer_id')
            ->each(function (Client $client) use ($today, &$alerted) {
                $last = $client->attendances()->orderByDesc('attendance_date')->first();
                $since = $last?->attendance_date ?? $client->start_date;
                $daysSilent = (int) $since->copy()->startOfDay()->diffInDays($today);

                // ponytail: fires once per 7-day silence window; a client who then attends and goes
                // quiet again re-triggers naturally since the counter resets from their last attendance.
                if ($daysSilent !== self::THRESHOLD_DAYS) {
                    return;
                }

                Notification::send($client->trainer, new ClientAbsenceAlert($client));
                $alerted++;
            });

        $this->info("Alerted trainers for {$alerted} absent clients.");

        return self::SUCCESS;
    }
}
