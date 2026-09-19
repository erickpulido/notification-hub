<?php

declare(strict_types=1);

namespace App\Jobs;

use App\DTOs\NotificationDTO;
use App\Services\Notifications\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Job asíncrono para el envío de notificaciones.
 *
 * Propósito: Encapsular el envío de notificaciones de manera asíncrona a través de las colas.
 *
 * Frontera de Dominio: Delega la lógica de negocio y despacho al NotificationService.
 */
final class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Crea una nueva instancia del Job.
     *
     * @param NotificationDTO $notification El DTO inmutable de la notificación.
     */
    public function __construct(
        public readonly NotificationDTO $notification
    ) {}

    /**
     * Ejecuta el Job inyectando el servicio de aplicación correspondiente.
     *
     * @param NotificationService $service El servicio de aplicación que gestiona el despacho.
     * @return void
     */
    public function handle(NotificationService $service): void
    {
        $service->sendNotification($this->notification);
    }
}
