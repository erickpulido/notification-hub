<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Throwable;

/**
 * Excepción de dominio para errores durante la entrega de notificaciones.
 *
 * Propósito: Encapsular y señalar fallos ocurridos durante el despacho o comunicación
 * con proveedores externos de mensajería (Slack, Email, Telegram, etc.).
 *
 * Frontera de Dominio: Representa exclusivamente errores en la capa de entrega/transporte,
 * no errores de validación de entrada ni fallas de infraestructura de colas.
 */
class NotificationDeliveryException extends Exception
{
    /**
     * @param string $message Mensaje descriptivo del fallo de entrega.
     * @param int $code Código numérico opcional de excepción.
     * @param Throwable|null $previous Excepción previa/original si la hubiese.
     */
    public function __construct(
        string $message = "",
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
