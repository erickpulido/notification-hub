<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\DTOs\NotificationDTO;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Pruebas unitarias para el DTO NotificationDTO.
 *
 * Propósito: Certificar la correcta instanciación, asignación de propiedades,
 * conversión bidireccional hacia/desde array y la inmutabilidad estricta del DTO.
 *
 * Frontera de Dominio: Verifica únicamente la estructura interna e inmutabilidad
 * de la clase DTO, sin dependencias de infraestructura ni base de datos.
 */
final class NotificationDTOTest extends TestCase
{
    /**
     * Verifica que el DTO se instancie correctamente con sus valores mediante el constructor.
     *
     * @return void
     */
    public function test_can_be_instantiated_via_constructor(): void
    {
        $dispatchId = 'a9d7c041-3b7c-47ea-a2b1-91d120a1789c';
        $eventType = 'USER_WELCOME';
        $channels = ['slack', 'email'];
        $payload = [
            'user_id' => 'usr_123',
            'message' => 'Bienvenido al sistema',
        ];

        $dto = new NotificationDTO(
            dispatchId: $dispatchId,
            eventType: $eventType,
            channels: $channels,
            payload: $payload,
        );

        $this->assertSame($dispatchId, $dto->dispatchId);
        $this->assertSame($eventType, $dto->eventType);
        $this->assertSame($channels, $dto->channels);
        $this->assertSame($payload, $dto->payload);
    }

    /**
     * Verifica la instanciación e hidratación a través del método de factoría estática fromArray.
     *
     * @return void
     */
    public function test_can_be_instantiated_from_array(): void
    {
        $data = [
            'dispatch_id' => 'b8c6b123-12a3-45ef-98bc-123456789abc',
            'event_type' => 'ORDER_CREATED',
            'channels' => ['telegram'],
            'payload' => [
                'order_id' => 'ord_999',
                'message' => 'Nueva orden generada',
            ],
        ];

        $dto = NotificationDTO::fromArray($data);

        $this->assertSame($data['dispatch_id'], $dto->dispatchId);
        $this->assertSame($data['event_type'], $dto->eventType);
        $this->assertSame($data['channels'], $dto->channels);
        $this->assertSame($data['payload'], $dto->payload);
    }

    /**
     * Verifica la conversión a array asociativo con toArray().
     *
     * @return void
     */
    public function test_can_be_converted_to_array(): void
    {
        $data = [
            'dispatch_id' => 'c7b5a432-67d8-49ba-81ab-987654321def',
            'event_type' => 'PASSWORD_RESET',
            'channels' => ['email'],
            'payload' => [
                'email' => 'user@example.com',
                'message' => 'Restablece tu contraseña',
            ],
        ];

        $dto = NotificationDTO::fromArray($data);

        $this->assertSame($data, $dto->toArray());
    }

    /**
     * Verifica que la clase NotificationDTO sea inmutable (declarada readonly y final).
     *
     * @return void
     */
    public function test_class_is_strictly_immutable_and_readonly(): void
    {
        $reflection = new ReflectionClass(NotificationDTO::class);

        $this->assertTrue($reflection->isReadOnly(), 'NotificationDTO must be declared as a readonly class.');
        $this->assertTrue($reflection->isFinal(), 'NotificationDTO must be declared as a final class.');
    }
}
