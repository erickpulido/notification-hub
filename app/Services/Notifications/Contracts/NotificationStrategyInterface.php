<?php

declare(strict_types=1);

namespace App\Services\Notifications\Contracts;

use App\DTOs\NotificationDTO;

/**
 * Contrato genérico para las estrategias de despacho de notificaciones por canal.
 *
 * Propósito: Definir la interfaz unificada que deben implementar todas las estrategias concretas
 * de notificación (Slack, Email, Telegram, etc.) siguiendo el patrón Strategy.
 *
 * Frontera de Dominio: No debe contener lógica concreta de formateo o envío HTTP/SMTP, únicamente
 * la definición de la firma de los métodos obligatorios.
 */
interface NotificationStrategyInterface
{
    /**
     * Retorna el identificador unívoco del canal gestionado por la estrategia (ej. 'slack', 'email').
     *
     * @return string Identificador en formato snake_case o texto en minúsculas del canal.
     */
    public static function getChannelIdentifier(): string;

    /**
     * Ejecuta el despacho del payload hacia el proveedor externo correspondiente al canal.
     *
     * @param NotificationDTO $notification Objeto DTO inmutable con la información del evento y su payload.
     * @return void
     * @throws \App\Exceptions\NotificationDeliveryException Cuando el proveedor externo falla o retorna un error de entrega.
     */
    public function send(NotificationDTO $notification): void;
}
