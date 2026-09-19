<?php

declare(strict_types=1);

namespace App\Services\Notifications;

use App\DTOs\NotificationDTO;

/**
 * Servicio de aplicación para la gestión de notificaciones.
 *
 * Propósito: Orquestar el despacho de una notificación a través de todos los canales
 * solicitados en el DTO, resolviendo la estrategia de entrega correspondiente de manera dinámica.
 *
 * Frontera de Dominio: No realiza el envío directo (delegado a las estrategias concretas)
 * ni maneja la persistencia o las colas directamente.
 */
final class NotificationService
{
    /**
     * @param NotificationStrategyFactory $strategyFactory
     */
    public function __construct(
        private readonly NotificationStrategyFactory $strategyFactory
    ) {}

    /**
     * Despacha la notificación a cada uno de los canales definidos en el DTO.
     *
     * @param NotificationDTO $dto Objeto de transferencia de datos con la información del envío.
     * @return void
     * @throws \InvalidArgumentException Si algún canal no está soportado.
     * @throws \App\Exceptions\NotificationDeliveryException Si falla el envío físico en alguna estrategia.
     */
    public function sendNotification(NotificationDTO $dto): void
    {
        foreach ($dto->channels as $channel) {
            $strategy = $this->strategyFactory->build($channel);
            $strategy->send($dto);
        }
    }
}
