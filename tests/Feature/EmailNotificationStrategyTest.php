<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\DTOs\NotificationDTO;
use App\Exceptions\NotificationDeliveryException;
use App\Mail\NotificationMail;
use App\Services\Notifications\Strategies\EmailNotificationStrategy;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Cobertura de pruebas para la estrategia de notificaciones por correo electrónico.
 */
final class EmailNotificationStrategyTest extends TestCase
{
    private EmailNotificationStrategy $strategy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->strategy = new EmailNotificationStrategy();
    }

    public function test_sends_email_successfully_when_valid_recipient_provided(): void
    {
        Mail::fake();

        $dto = new NotificationDTO(
            dispatchId: '22701876-c48c-4110-bdad-316f8bb31c8c',
            eventType: 'USER_WELCOME',
            channels: ['email'],
            payload: [
                'user_id' => '12345',
                'email' => 'dev@example.com',
                'message' => 'Bienvenido a la plataforma'
            ]
        );

        $this->strategy->send($dto);

        Mail::assertSent(NotificationMail::class, function (NotificationMail $mail) use ($dto) {
            return $mail->hasTo('dev@example.com') &&
                   $mail->notification->dispatchId === $dto->dispatchId;
        });
    }

    public function test_throws_exception_when_email_is_missing_in_payload(): void
    {
        Mail::fake();

        $dto = new NotificationDTO(
            dispatchId: '22701876-c48c-4110-bdad-316f8bb31c8c',
            eventType: 'USER_WELCOME',
            channels: ['email'],
            payload: [
                'user_id' => '12345',
                'message' => 'Sin email presente'
            ]
        );

        $this->expectException(NotificationDeliveryException::class);
        $this->expectExceptionMessage("El payload no contiene una dirección de correo válida en el campo 'email'.");

        $this->strategy->send($dto);

        Mail::assertNothingSent();
    }
}