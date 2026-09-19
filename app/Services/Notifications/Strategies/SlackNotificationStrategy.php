<?php

declare(strict_types=1);

namespace App\Services\Notifications\Strategies;

use App\DTOs\NotificationDTO;
use App\Services\Notifications\Contracts\NotificationStrategyInterface;
use Illuminate\Support\Facades\Log;

/**
 * Estrategia de notificación para el canal Slack.
 *
 * Propósito: Implementar el despacho de mensajes hacia canales de Slack.
 * Por ahora, simula el envío registrando la operación en los logs del sistema.
 *
 * Frontera de Dominio: Solo se encarga de la comunicación con Slack.
 * No debe procesar lógica de otros canales ni validaciones globales de negocio.
 */
final class SlackNotificationStrategy implements NotificationStrategyInterface
{
    /**
     * @inheritDoc
     */
    public static function getChannelIdentifier(): string
    {
        return 'slack';
    }

    /**
     * @inheritDoc
     */
    public function send(NotificationDTO $notification): void
    {
        Log::info('Simulando envío de notificación a Slack', [
            'dispatch_id' => $notification->dispatchId,
            'event_type' => $notification->eventType,
            'channel' => self::getChannelIdentifier(),
            'payload' => $notification->payload,
        ]);
    }
}
