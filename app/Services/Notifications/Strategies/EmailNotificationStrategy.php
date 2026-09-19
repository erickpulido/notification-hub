<?php

declare(strict_types=1);

namespace App\Services\Notifications\Strategies;

use App\DTOs\NotificationDTO;
use App\Services\Notifications\Contracts\NotificationStrategyInterface;
use Illuminate\Support\Facades\Log;

/**
 * Estrategia de notificación para el canal Email.
 *
 * Propósito: Implementar el despacho de mensajes hacia correo electrónico.
 * Por ahora, simula el envío registrando la operación en los logs del sistema.
 *
 * Frontera de Dominio: Solo se encarga de la comunicación vía Email.
 * No debe procesar lógica de otros canales ni validaciones globales de negocio.
 */
final class EmailNotificationStrategy implements NotificationStrategyInterface
{
    /**
     * @inheritDoc
     */
    public static function getChannelIdentifier(): string
    {
        return 'email';
    }

    /**
     * @inheritDoc
     */
    public function send(NotificationDTO $notification): void
    {
        Log::info('Simulando envío de notificación por Email', [
            'dispatch_id' => $notification->dispatchId,
            'event_type' => $notification->eventType,
            'channel' => self::getChannelIdentifier(),
            'payload' => $notification->payload,
        ]);
    }
}
