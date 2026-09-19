<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\Notifications\NotificationStrategyFactory;
use App\Services\Notifications\Strategies\EmailNotificationStrategy;
use App\Services\Notifications\Strategies\SlackNotificationStrategy;
use InvalidArgumentException;
use Tests\TestCase;

/**
 * Pruebas unitarias para NotificationStrategyFactory.
 *
 * Propósito: Validar que la factoría resuelva correctamente las estrategias registradas
 * y maneje adecuadamente los casos de canales no soportados.
 *
 * Estándar: Cumple estrictamente con el prefijo "test_" en minúsculas para los nombres de métodos.
 */
final class NotificationStrategyFactoryTest extends TestCase
{
    private NotificationStrategyFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Obtenemos la factoría del contenedor para validar la integración del ServiceProvider
        $this->factory = $this->app->make(NotificationStrategyFactory::class);
    }

    /**
     * @group notifications
     * @group architecture
     */
    public function test_it_resolves_slack_strategy_correctly(): void
    {
        $strategy = $this->factory->build('slack');

        $this->assertInstanceOf(SlackNotificationStrategy::class, $strategy);
        $this->assertEquals('slack', $strategy::getChannelIdentifier());
    }

    /**
     * @group notifications
     * @group architecture
     */
    public function test_it_resolves_email_strategy_correctly(): void
    {
        $strategy = $this->factory->build('email');

        $this->assertInstanceOf(EmailNotificationStrategy::class, $strategy);
        $this->assertEquals('email', $strategy::getChannelIdentifier());
    }

    /**
     * @group notifications
     * @group architecture
     */
    public function test_it_throws_exception_for_unsupported_channel(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Channel [whatsapp] is not supported by any registered strategy.');

        $this->factory->build('whatsapp');
    }

    /**
     * @group notifications
     * @group architecture
     */
    public function test_it_returns_list_of_supported_channels(): void
    {
        $channels = $this->factory->getSupportedChannels();

        $this->assertCount(2, $channels);
        $this->assertContains('slack', $channels);
        $this->assertContains('email', $channels);
    }
}
