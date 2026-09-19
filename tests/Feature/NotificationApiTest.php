<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Jobs\SendNotificationJob;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Pruebas de integración para la API de Despacho de Notificaciones.
 *
 * Propósito: Validar que el endpoint POST /api/v1/notifications/dispatch responda con 202
 * y encole correctamente el Job cuando los datos son válidos, y con 422 si la validación falla.
 *
 * Estándar: Cumple con el prefijo "test_" en minúsculas para los métodos de prueba.
 */
final class NotificationApiTest extends TestCase
{
    /**
     * @group api
     * @group notifications
     */
    public function test_it_accepts_valid_notification_requests_and_enqueues_job(): void
    {
        Queue::fake();

        $uuid = Str::uuid()->toString();

        $payload = [
            'dispatch_id' => $uuid,
            'event_type' => 'USER_WELCOME',
            'channels' => ['slack', 'email'],
            'payload' => [
                'message' => 'Bienvenido al sistema de notificaciones.',
                'user_id' => 'usr_123',
            ],
        ];

        $response = $this->postJson('/api/v1/notifications/dispatch', $payload);

        $response->assertStatus(202)
            ->assertJson([
                'status' => 'queued',
                'dispatch_id' => $uuid,
                'message' => 'Notification dispatch successfully enqueued.',
            ]);

        Queue::assertPushed(SendNotificationJob::class, function ($job) use ($uuid) {
            return $job->notification->dispatchId === $uuid
                && $job->notification->eventType === 'USER_WELCOME'
                && $job->notification->channels === ['slack', 'email']
                && $job->notification->payload === ['message' => 'Bienvenido al sistema de notificaciones.', 'user_id' => 'usr_123'];
        });
    }

    /**
     * @group api
     * @group notifications
     */
    public function test_it_returns_422_when_required_validation_fields_are_missing(): void
    {
        Queue::fake();

        // Enviamos payload vacío para forzar fallos de validación
        $response = $this->postJson('/api/v1/notifications/dispatch', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'dispatch_id',
                'event_type',
                'channels',
                'payload',
            ]);

        Queue::assertNothingPushed();
    }

    /**
     * @group api
     * @group notifications
     */
    public function test_it_returns_422_when_dispatch_id_is_not_a_valid_uuid(): void
    {
        Queue::fake();

        $payload = [
            'dispatch_id' => 'invalid-uuid-string',
            'event_type' => 'USER_WELCOME',
            'channels' => ['slack'],
            'payload' => [
                'message' => 'Prueba',
            ],
        ];

        $response = $this->postJson('/api/v1/notifications/dispatch', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['dispatch_id']);

        Queue::assertNothingPushed();
    }
}
