<?php

declare(strict_types=1);

namespace App\Http\Docs;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    title: "Notification Hub API",
    description: "Microservicio asíncrono y escalable para el despacho multicanal de notificaciones resuelto mediante Arquitectura Limpia y patrones de diseño (Strategy, Factory, DTOs)."
)]
#[OA\Server(
    url: "http://localhost:8080/api",
    description: "Entorno de Desarrollo Local"
)]
final class AppInfoDoc
{
    // Clase virtual para metainformación global de OpenAPI
}