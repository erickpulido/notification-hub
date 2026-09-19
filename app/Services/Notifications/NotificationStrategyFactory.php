<?php

declare(strict_types=1);

namespace App\Services\Notifications;

use App\Services\Notifications\Contracts\NotificationStrategyInterface;
use InvalidArgumentException;

/**
 * Fábrica para resolver estrategias de notificación por canal.
 *
 * Propósito: Proveer una instancia de NotificationStrategyInterface basada en el nombre del canal.
 * Utiliza el patrón Factory para desacoplar la selección de la estrategia de su ejecución.
 *
 * Frontera de Dominio: Solo gestiona el mapeo y resolución de estrategias registradas.
 * No debe contener lógica de envío ni validación de payloads.
 */
final class NotificationStrategyFactory
{
    /**
     * @var array<string, NotificationStrategyInterface>
     */
    private array $strategies = [];

    /**
     * @param iterable<NotificationStrategyInterface> $strategies
     */
    public function __construct(iterable $strategies)
    {
        foreach ($strategies as $strategy) {
            $this->strategies[$strategy::getChannelIdentifier()] = $strategy;
        }
    }

    /**
     * Resuelve la estrategia correspondiente para un canal dado.
     *
     * @param string $channel Identificador del canal (ej. 'slack', 'email').
     * @return NotificationStrategyInterface
     * @throws InvalidArgumentException Si el canal no está soportado por ninguna estrategia.
     */
    public function build(string $channel): NotificationStrategyInterface
    {
        return $this->strategies[$channel] ?? throw new InvalidArgumentException(
            sprintf('Channel [%s] is not supported by any registered strategy.', $channel)
        );
    }

    /**
     * Retorna la lista de identificadores de canales soportados.
     *
     * @return array<int, string>
     */
    public function getSupportedChannels(): array
    {
        return array_keys($this->strategies);
    }
}
