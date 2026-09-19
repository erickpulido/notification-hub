<?php

declare(strict_types=1);

namespace App\Services\Notifications\Strategies;

use App\DTOs\NotificationDTO;
use App\Exceptions\NotificationDeliveryException;
use App\Mail\NotificationMail;
use App\Services\Notifications\Contracts\NotificationStrategyInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * Estrategia de notificación para el canal Email.
 *
 * Propósito: Implementar el despacho real de mensajes hacia correo electrónico mediante la capa de Mailables de Laravel.
 *
 * Frontera de Dominio: Solo se encarga de la entrega vía Email.
 * No debe procesar lógica de otros canales ni validaciones globales de negocio.
 */
final class EmailNotificationStrategy implements NotificationStrategyInterface
{
    /**
     * Devuelve el identificador único del canal de email.
     *
     * @return string Identificador del canal.
     */
    public static function getChannelIdentifier(): string
    {
        return 'email';
    }

    /**
     * Envía la notificación por correo electrónico utilizando la fachada de Mail.
     *
     * @param NotificationDTO $notification DTO con la información de la notificación.
     * @return void
     * @throws NotificationDeliveryException Si el destinatario no está presente o si ocurre un fallo en el servidor SMTP.
     */
    public function send(NotificationDTO $notification): void
    {
        $recipient = $notification->payload['email'] ?? null;

        if (empty($recipient) || !filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            Log::warning('Intento de envío de email sin un destinatario válido', [
                'dispatch_id' => $notification->dispatchId,
                'payload' => $notification->payload,
            ]);

            throw new NotificationDeliveryException("El payload no contiene una dirección de correo válida en el campo 'email'.");
        }

        try {
            Mail::to($recipient)->send(new NotificationMail($notification));

            Log::info('Notificación enviada exitosamente por Email', [
                'dispatch_id' => $notification->dispatchId,
                'recipient' => $recipient,
            ]);
        } catch (Throwable $e) {
            Log::error('Fallo de infraestructura al enviar correo electrónico', [
                'dispatch_id' => $notification->dispatchId,
                'error' => $e->getMessage(),
            ]);

            throw new NotificationDeliveryException("Fallo en el servicio de correo: {$e->getMessage()}", 0, $e);
        }
    }
}