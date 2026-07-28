<?php

namespace App\Notifications;

use App\Models\Evaluation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class EvaluationGenerated extends Notification
{
    use Queueable;

    public function __construct(public Evaluation $evaluation)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $evaluation = $this->evaluation;

        return (new MailMessage)
            ->subject("Nueva evaluación generada: {$evaluation->client->name}")
            ->greeting('Nueva evaluación disponible')
            ->line("Se ha generado la evaluación #{$evaluation->evaluation_number} de {$evaluation->client->name}.")
            ->line("Periodo: {$evaluation->period_start->format('d/m/Y')} - {$evaluation->period_end->format('d/m/Y')}.")
            ->line('Ya puedes revisarla y registrar tus notas.');
    }
}
