<?php

namespace App\Notifications;

use App\Models\Client;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClientAbsenceAlert extends Notification
{
    public function __construct(private readonly Client $client) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $last = $this->client->attendances()->orderByDesc('attendance_date')->first();
        $since = $last?->attendance_date ?? $this->client->start_date;
        $days = (int) $since->copy()->startOfDay()->diffInDays(now()->startOfDay());

        return (new MailMessage)
            ->subject("Alerta de inasistencia: {$this->client->name}")
            ->greeting('Alerta de posible abandono')
            ->line("El cliente {$this->client->name} lleva {$days} días sin asistir.")
            ->line("Su última visita registrada fue el {$since->format('d/m/Y')}.")
            ->line('Te recomendamos contactarlo para evitar el abandono del programa.');
    }
}
