<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\Notifications\NotificationService;
use App\Services\Notifications\NotificationStrategyFactory;
use App\Services\Notifications\Strategies\EmailNotificationStrategy;
use App\Services\Notifications\Strategies\SlackNotificationStrategy;
use Illuminate\Support\ServiceProvider;

/**
 * Proveedor de servicios para el módulo de notificaciones.
 *
 * Propósito: Registrar en el contenedor de Laravel las estrategias de notificación
 * y la factoría que las orquesta, facilitando la inyección de dependencias.
 */
final class NotificationServiceProvider extends ServiceProvider
{
    /**
     * Registra los servicios de notificación.
     */
    public function register(): void
    {
        // Tagging de estrategias para resolución masiva en la factoría
        $this->app->tag([
            SlackNotificationStrategy::class,
            EmailNotificationStrategy::class,
        ], 'notification.strategies');

        // Registro de la factoría como Singleton
        $this->app->singleton(NotificationStrategyFactory::class, function ($app) {
            return new NotificationStrategyFactory(
                $app->tagged('notification.strategies')
            );
        });

        // Registro del Servicio de Aplicación
        $this->app->singleton(NotificationService::class, function ($app) {
            return new NotificationService(
                $app->make(NotificationStrategyFactory::class)
            );
        });
    }
}
