<?php

declare(strict_types=1);

namespace App\DTOs;

/**
 * Data Transfer Object inmutable que representa la información central de una notificación.
 *
 * Propósito: Encapsular de forma estricta e inmutable los datos recibidos de un evento de notificación
 * (dispatch_id, event_type, channels, payload) para su transporte entre los controladores, jobs y estrategias.
 *
 * Frontera de Dominio: No contiene lógica de negocio ni de despacho de notificaciones, ni interactúa
 * con la capa de persistencia o servicios externos.
 */
final readonly class NotificationDTO
{
    /**
     * @param string $dispatchId Identificador único de trazabilidad e idempotencia (UUIDv4).
     * @param string $eventType Tipo de evento de negocio (ej. 'USER_WELCOME').
     * @param array<int, string> $channels Lista de canales solicitados (ej. ['slack', 'email']).
     * @param array<string, mixed> $payload Estructura de datos requerida para el mensaje y sus metadatos.
     */
    public function __construct(
        public string $dispatchId,
        public string $eventType,
        public array $channels,
        public array $payload,
    ) {}

    /**
     * Construye una instancia de NotificationDTO a partir de un array asociativo de datos.
     *
     * @param array{
     *     dispatch_id: string,
     *     event_type: string,
     *     channels: array<int, string>,
     *     payload: array<string, mixed>
     * } $data Array asociativo con la estructura recibida de la solicitud.
     * @return self Nueva instancia inmutable de NotificationDTO.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            dispatchId: $data['dispatch_id'],
            eventType: $data['event_type'],
            channels: $data['channels'],
            payload: $data['payload'],
        );
    }

    /**
     * Convierte la instancia del DTO a su representación en array asociativo.
     *
     * @return array{
     *     dispatch_id: string,
     *     event_type: string,
     *     channels: array<int, string>,
     *     payload: array<string, mixed>
     * } Array asociativo con la estructura de datos del DTO.
     */
    public function toArray(): array
    {
        return [
            'dispatch_id' => $this->dispatchId,
            'event_type' => $this->eventType,
            'channels' => $this->channels,
            'payload' => $this->payload,
        ];
    }
}
