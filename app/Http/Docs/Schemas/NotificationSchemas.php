<?php

declare(strict_types=1);

namespace App\Http\Docs\Schemas;

use OpenApi\Attributes as OA;

final class NotificationSchemas
{
    /**
     * Payload de entrada para el despacho de notificaciones.
     */
    #[OA\Schema(
        schema: "DispatchNotificationRequest",
        title: "Dispatch Notification Request",
        description: "Estructura de la petición HTTP para encolar el despacho de una notificación.",
        required: ["event_type", "channels", "payload"],
        properties: [
            new OA\Property(
                property: "dispatch_id",
                type: "string",
                format: "uuid",
                nullable: true,
                example: "f47ac10b-58cc-4372-a567-0e02b2c3d479",
                description: "Opcional. Identificador único para garantizar idempotencia. Si se omite, el servidor generará un UUID v4 automáticamente."
            ),
            new OA\Property(
                property: "event_type",
                type: "string",
                example: "USER_WELCOME",
                description: "Identificador del evento de negocio que dispara la notificación."
            ),
            new OA\Property(
                property: "channels",
                type: "array",
                items: new OA\Items(type: "string", example: "email"),
                description: "Lista de canales de entrega destino solicitados (ej: email, slack)."
            ),
            new OA\Property(
                property: "payload",
                type: "object",
                example: [
                    "user_id" => 12345,
                    "email" => "desarrollador@local.test",
                    "message" => "¡Bienvenido a la plataforma!"
                ],
                description: "Diccionario clave-valor con la información contextual del evento."
            )
        ]
    )]
    public static function requestSchema(): void {}

    /**
     * Respuesta de éxito (202 Accepted).
     */
    #[OA\Schema(
        schema: "NotificationAcceptedResponse",
        title: "Notification Accepted Response",
        description: "Respuesta retornada cuando la notificación fue validada y encolada en Redis.",
        properties: [
            new OA\Property(property: "status", type: "string", example: "accepted"),
            new OA\Property(property: "message", type: "string", example: "Notification queued successfully."),
            new OA\Property(property: "dispatch_id", type: "string", example: "f47ac10b-58cc-4372-a567-0e02b2c3d479", description: "UUID único de rastreo de la operación.")
        ]
    )]
    public static function acceptedResponseSchema(): void {}

    /**
     * Respuesta de error de validación (422 Unprocessable Entity).
     */
    #[OA\Schema(
        schema: "ValidationErrorResponse",
        title: "Validation Error Response",
        description: "Estructura estándar de errores de validación en parámetros de entrada o canales no soportados.",
        properties: [
            new OA\Property(property: "message", type: "string", example: "The selected channels.0 is invalid."),
            new OA\Property(
                property: "errors",
                type: "object",
                example: [
                    "channels.0" => ["The selected channels.0 is invalid."],
                    "payload.email" => ["The payload.email field must be a valid email address."]
                ]
            )
        ]
    )]
    public static function validationErrorSchema(): void {}
}