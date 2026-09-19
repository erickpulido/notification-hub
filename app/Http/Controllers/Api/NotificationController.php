<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SendNotificationRequest;
use App\Jobs\SendNotificationJob;
use Illuminate\Http\JsonResponse;

/**
 * Controlador de API para despachar notificaciones.
 *
 * Propósito: Recibir solicitudes HTTP, validar el payload, generar el DTO
 * y encolar el job asíncrono para el procesamiento desacoplado.
 */
final class NotificationController extends Controller
{
    /**
     * Procesa la solicitud de despacho de notificaciones.
     *
     * @param SendNotificationRequest $request
     * @return JsonResponse
     */
    public function store(SendNotificationRequest $request): JsonResponse
    {
        $dto = $request->toDTO();

        // Encolamiento asíncrono del despacho de notificaciones
        SendNotificationJob::dispatch($dto);

        return response()->json([
            'status' => 'queued',
            'dispatch_id' => $dto->dispatchId,
            'message' => 'Notification dispatch successfully enqueued.',
        ], 202);
    }
}
