#!/usr/bin/env bash

set -e

echo "=================================================="
echo " Starting Notification Hub Environment Setup"
echo "=================================================="

# 1. Copiar archivo .env si no existe
if [ ! -f .env ]; then
    echo "--> Copying .env.example to .env..."
    cp .env.example .env
else
    echo "--> .env file already exists. Skipping copy."
fi

# 2. Instalar dependencias mediante contenedor temporal de Composer si vendor no existe
if [ ! -d vendor ]; then
    echo "--> Installing Composer dependencies via Docker temporary container..."
    docker run --rm \
        -u "$(id -u):$(id -g)" \
        -v "$(pwd):/var/www/html" \
        -w /var/www/html \
        laravelsail/php84-composer:latest \
        composer install
else
    echo "--> Vendor directory already exists. Skipping initial composer install."
fi

# 3. Levantar la infraestructura de Laravel Sail (Docker Compose)
echo "--> Starting Sail containers in detached mode..."
./vendor/bin/sail up -d

# 4. Generar clave de aplicación de Laravel si no está configurada
echo "--> Generating application key..."
./vendor/bin/sail artisan key:generate --force

# 5. Ejecutar migraciones de base de datos
echo "--> Running database migrations..."
./vendor/bin/sail artisan migrate --force

# 6. Reiniciar el worker para asegurar que cargue la versión más reciente del código
echo "--> Restarting queue worker..."
./vendor/bin/sail artisan queue:restart

echo "=================================================="
echo " Setup Complete Successfully! "
echo "=================================================="
echo " - API Endpoint: http://localhost:8080/api/v1/notifications/dispatch"
echo " - Swagger UI: http://localhost:8080/api/documentation"
echo " - Mailpit Web UI: http://localhost:8025"
echo " - Test Suite Command: ./vendor/bin/sail test"
echo "=================================================="
