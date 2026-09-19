<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\DTOs\NotificationDTO;
use App\Jobs\SendNotificationJob;
use App\Mail\NotificationMail;
use App\Services\Notifications\NotificationService;
use App\Services\Notifications\NotificationStrategyFactory;
use App\Services\Notifications\Contracts\NotificationStrategyInterface;
use App\Services\Notifications\Strategies\EmailNotificationStrategy;
use App\Services\Notifications\Strategies\SlackNotificationStrategy;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Mockery;
use Tests\TestCase;

/**
 * Pruebas unitarias para la Capa de Aplicación de Notificaciones (NotificationService y SendNotificationJob).
 *
 * Propósito: Validar que el servicio de aplicación itere y despache correctamente por cada canal solicitado
 * y que el Job asíncrono invoque correctamente al servicio.
 *
 * Estándar: Cumple con el prefijo "test_" en minúsculas para los métodos de prueba.
 */
final class NotificationServiceTest extends TestCase
{
    /**
     * @group notifications
     * @group application
     */
    public function test_notification_service_dispatches_to_all_requested_channels(): void
    {
        // Mail::fake() para evitar intento real de SMTP
        Mail::fake();
    
        // 1. Creamos un DTO simulado para dos canales: slack y email
        $dto = new NotificationDTO(
            dispatchId: 'a9d7c041-3b7c-47ea-a2b1-91d120a1789c',
            eventType: 'USER_WELCOME',
            channels: ['slack', 'email'],
            payload: ['message' => 'Bienvenido', 'email' => 'user@example.com']
        );

        // 2. Dado que NotificationStrategyFactory y NotificationService son "final",
        // no podemos mockearlos directamente con Mockery.
        // En su lugar, mockeamos el Log de Laravel para verificar que las estrategias reales
        // se resuelvan y se ejecute el send() registrando en logs.
        Log::shouldReceive('info')
            ->once()
            ->with('Simulando envío de notificación a Slack', [
                'dispatch_id' => $dto->dispatchId,
                'event_type' => $dto->eventType,
                'channel' => 'slack',
                'payload' => $dto->payload,
            ]);

        Log::shouldReceive('info')
            ->once()
            ->with('Notificación enviada exitosamente por Email', [
                'dispatch_id' => $dto->dispatchId,
                'recipient' => 'user@example.com',
            ]);

        Log::shouldReceive('warning')->zeroOrMoreTimes();
        Log::shouldReceive('error')->zeroOrMoreTimes();

        // 3. Resolvemos la factoría y el servicio reales del contenedor (que usan el ServiceProvider registrado)
        $factory = $this->app->make(NotificationStrategyFactory::class);
        $service = new NotificationService($factory);

        // 4. Invocamos el método
        $service->sendNotification($dto);

        Mail::assertSent(NotificationMail::class);
    }   

    /**
     * @group notifications
     * @group application
     */
    public function test_send_notification_job_delegates_to_notification_service(): void
    {
        $dto = new NotificationDTO(
            dispatchId: 'a9d7c041-3b7c-47ea-a2b1-91d120a1789c',
            eventType: 'USER_WELCOME',
            channels: ['slack'],
            payload: ['message' => 'Bienvenido']
        );

        // Mockeamos el Log para que no falle al simular el envío de Slack en la ejecución real del job
        Log::shouldReceive('info')
            ->once()
            ->with('Simulando envío de notificación a Slack', [
                'dispatch_id' => $dto->dispatchId,
                'event_type' => $dto->eventType,
                'channel' => 'slack',
                'payload' => $dto->payload,
            ]);

        // Resolvemos el servicio real
        $service = $this->app->make(NotificationService::class);

        // Instanciamos el Job y llamamos a handle()
        $job = new SendNotificationJob($dto);
        $job->handle($service);

        $this->assertTrue(true);
    }
}
