<?php

declare(strict_types=1);

namespace App\Http\Docs\Endpoints;

use OpenApi\Attributes as OA;

final class NotificationEndpointsDoc
{
    #[OA\Post(
        path: "/v1/notifications/dispatch",
        summary: "Despacha una notificación multicanal de forma asíncrona",
        description: "Punto de entrada único que recibe la petición HTTP, valida el payload y delega la ejecución de forma asíncrona a la cola de Redis.",
        tags: ["Notifications"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/DispatchNotificationRequest")
        ),
        responses: [
            new OA\Response(
                response: 202,
                description: "Petición aceptada y encolada correctamente para su procesamiento en segundo plano.",
                content: new OA\JsonContent(ref: "#/components/schemas/NotificationAcceptedResponse")
            ),
            new OA\Response(
                response: 422,
                description: "Error de validación en la estructura del JSON, campos obligatorios o canales no soportados.",
                content: new OA\JsonContent(ref: "#/components/schemas/ValidationErrorResponse")
            ),
            new OA\Response(
                response: 500,
                description: "Error no controlado en la capa de transporte o servidor."
            )
        ]
    )]
    public static function dispatch(): void {}
}