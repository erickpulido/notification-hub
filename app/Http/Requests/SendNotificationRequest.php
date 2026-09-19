<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\DTOs\NotificationDTO;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Form Request para validar el despacho de notificaciones.
 *
 * Propósito: Validar que el payload cumpla con la estructura requerida
 * y transformar la solicitud validada en un NotificationDTO inmutable.
 */
final class SendNotificationRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado a realizar esta solicitud.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Obtiene las reglas de validación que se aplican a la solicitud.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'dispatch_id' => ['nullable', 'string', 'uuid'],
            'event_type' => ['required', 'string', 'max:100'],
            'channels' => ['required', 'array', 'min:1'],
            'channels.*' => ['required', 'string'],
            'payload' => ['required', 'array'],
        ];
    }

    /**
     * Transforma el payload validado en una instancia de NotificationDTO.
     *
     * @return NotificationDTO
     */
    public function toDTO(): NotificationDTO
    {
        return new NotificationDTO(
            dispatchId: $this->validated('dispatch_id') ?? (string) \Illuminate\Support\Str::uuid(),
            eventType: (string) $this->validated('event_type'),
            channels: (array) $this->validated('channels'),
            payload: (array) $this->validated('payload')
        );
    }
}
