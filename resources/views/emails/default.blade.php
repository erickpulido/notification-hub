<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Notificación de Sistema</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">
    <h2>Notificación: {{ $notification->eventType }}</h2>
    <p><strong>Detalles de la notificación:</strong></p>
    <ul>
        @foreach($notification->payload as $key => $value)
            <li><strong>{{ $key }}:</strong> {{ is_array($value) ? json_encode($value) : $value }}</li>
        @endforeach
    </ul>
</body>
</html>