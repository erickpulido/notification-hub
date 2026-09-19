<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>¡Bienvenido!</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">
    <h1 style="color: #2b6cb0;">¡Bienvenido a la plataforma!</h1>
    <p>Hola,</p>
    <p>{{ $notification->payload['message'] ?? 'Gracias por unirte a nosotros.' }}</p>
    <p>Tu ID de usuario es: <strong>{{ $notification->payload['user_id'] ?? 'N/A' }}</strong></p>
    <hr style="border: none; border-top: 1px solid #eee; margin: 20px 0;">
    <small style="color: #718096;">Este es un mensaje automático enviado por el Hub de Notificaciones.</small>
</body>
</html>