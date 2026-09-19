# Notification Hub Microservice

Microservicio desacoplado y de alto rendimiento para la ingesta y despacho asíncrono de notificaciones multicanal (Slack, Email, Telegram). Construido sobre **Laravel 13**, **PHP 8.4**, **Redis** y **PostgreSQL**.

---

## Especificación Técnica (RFC / TDD)

La documentación de arquitectura completa, diagramas C4, contrato de eventos, patrones de diseño (Strategy + Factory) y justificación de trade-offs se encuentra en:  
**[RFC-0001: Technical Design Document](docs/rfc/0001-notification-hub.md)**

---

## Inicio Rápido

No necesitas instalar PHP, Composer, Redis ni PostgreSQL localmente. Todo el entorno está contenedorizado con **Docker** y **Laravel Sail**.

### 1. Clonar el repositorio y ejecutar el setup automatizado

```bash
git clone https://github.com/erickpulido/notification-hub.git
cd notification-hub
chmod +x setup.sh
./setup.sh
```

El script `./setup.sh` se encarga de:
1. Crear el archivo `.env` a partir del `.env.example`.
2. Levantar los contenedores de Docker (Nginx, PHP, PostgreSQL, Redis, Mailpit).
3. Instalar las dependencias de Composer.
4. Generar las claves de la aplicación y ejecutar las migraciones de BD.
5. Iniciar el procesador de colas (*Queue Worker*).

---

## Panel de Control de Servicios (Servicios Locales)

Una vez ejecutado `./setup.sh`, tendrás acceso inmediato a los siguientes paneles:

| Servicio | URL Local | Descripción |
| :--- | :--- | :--- |
| **Documentación Swagger UI** | `http://localhost:8080/api/documentation` | Prueba interactiva de la API |
| **Servidor de Correos (Mailpit)** | `http://localhost:8025` | Bandeja de entrada simulada para Email |
| **API Endpoint Directo** | `http://localhost:8080/api/v1/notifications/dispatch` | Enpoint principal de despacho |

---

## Pruebas y Validación del Sistema

### Ejecutar la Suite Completa de Tests

Para verificar que todos los contratos, validaciones, estrategias y logs funcionan al 100%:

```bash
./vendor/bin/sail test
```

*Resultado esperado:* **18 passed (50 assertions)**.

---

## Guía Rápida de Uso de la API (Manual del Usuario)

### Enviar una Notificación Multicanal

Envía una petición `POST` a `http://localhost:8080/api/v1/notifications/dispatch`:

#### Payload de Ejemplo (JSON)

```json
{
  "event_type": "USER_WELCOME",
  "channels": ["slack", "email"],
  "payload": {
    "message": "Bienvenido a la plataforma.",
    "email": "usuario@ejemplo.com"
  }
}
```

> **Nota:** El campo `dispatch_id` (UUIDv4) es opcional. Si lo omites, la API genera uno automáticamente para garantizar la trazabilidad e idempotencia.

#### Ejemplo de Comando cURL

```bash
curl -X POST http://localhost:8080/api/v1/notifications/dispatch \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "event_type": "USER_WELCOME",
    "channels": ["slack", "email"],
    "payload": {
      "message": "Bienvenido a la plataforma.",
      "email": "usuario@ejemplo.com"
    }
  }'
```

#### Respuesta de Exito (`HTTP 202 Accepted`)

```json
{
  "status": "success",
  "message": "Notification event accepted for processing.",
  "data": {
    "dispatch_id": "a9d7c041-3b7c-47ea-a2b1-91d120a1789c",
    "event_type": "USER_WELCOME",
    "queued_channels": [
      {
        "channel": "slack",
        "queue": "notifications_slack",
        "job_id": "f51b6890-a320-410e-953e-5ef3c591bfb4"
      },
      {
        "channel": "email",
        "queue": "notifications_email",
        "job_id": "76ba181c-8e4d-4952-bc62-3bf2c37e6113"
      }
    ],
    "created_at": "2026-09-18T02:13:44.102Z"
  }
}
```

---

## Comandos Utilitarios Frecuentes

* **Ver estado de los contenedores:**
  ```bash
  ./vendor/bin/sail ps
  ```
* **Reiniciar servicios de Docker:**
  ```bash
  ./vendor/bin/sail restart
  ```
* **Ver logs de los workers en tiempo real:**
  ```bash
  ./vendor/bin/sail logs -f
  ```
* **Ver trabajos fallidos en la Dead Letter Queue (DLQ):**
  ```bash
  ./vendor/bin/sail artisan queue:failed
  ```