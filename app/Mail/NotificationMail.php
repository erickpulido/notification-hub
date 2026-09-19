<?php

declare(strict_types=1);

namespace App\Mail;

use App\DTOs\NotificationDTO;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\View;

/**
 * Propósito: Encapsular y construir la estructura del correo electrónico enviado por el sistema,
 * resolviendo dinámicamente la plantilla Blade en función del tipo de evento.
 *
 * Frontera de Dominio: Representa exclusivamente la presentación del mensaje de email.
 */
final class NotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param NotificationDTO $notification Objeto de transferencia de datos con la información del evento.
     */
    public function __construct(
        public readonly NotificationDTO $notification
    ) {}

    /**
     * Define el sobre del correo con el asunto dinámico según el tipo de evento.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Notificación de Sistema: {$this->notification->eventType}",
        );
    }

    /**
     * Resuelve la plantilla Blade adecuada y pasa el DTO a la vista.
     */
    public function content(): Content
    {
        $viewName = 'emails.' . strtolower($this->notification->eventType);

        if (!View::exists($viewName)) {
            $viewName = 'emails.default';
        }

        return new Content(
            view: $viewName,
            with: [
                'notification' => $this->notification,
            ],
        );
    }
}