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

# 2. Limpieza preventiva de contenedores y espera activa de liberación de red
echo "--> Cleaning up existing Docker resources..."
if [ -f "./vendor/bin/sail" ]; then
    ./vendor/bin/sail down -v --remove-orphans > /dev/null 2>&1 || true
else
    docker compose down -v --remove-orphans > /dev/null 2>&1 || true
fi

# 3. Instalar dependencias mediante contenedor de Composer con PHP 8.4
if [ ! -d vendor ]; then
    echo "--> Installing Composer dependencies via Docker PHP 8.4 image..."
    docker run --rm \
        -u "$(id -u):$(id -g)" \
        -v "$(pwd):/var/www/html" \
        -w /var/www/html \
        laravelsail/php84-composer:latest \
        composer install --no-interaction --prefer-dist
else
    echo "--> Vendor directory exists. Verifying autoloader..."
fi

# 4. Levantar infraestructura Docker en segundo plano
echo "--> Starting Sail containers..."
./vendor/bin/sail up -d

# 5. Healthcheck: Esperar a que la base de datos esté lista para recibir conexiones
echo "--> Waiting for PostgreSQL database connection..."
until ./vendor/bin/sail exec pgsql pg_isready -U postgres > /dev/null 2>&1; do
    echo -n "."
    sleep 2
done
echo " [Database Ready]"

# 6. Generar clave de aplicación e iniciar migraciones
echo "--> Generating application key..."
./vendor/bin/sail artisan key:generate --force

echo "--> Running database migrations..."
./vendor/bin/sail artisan migrate:fresh --force

# 7. Reiniciar el worker de colas
echo "--> Restarting queue worker..."
./vendor/bin/sail restart queue-worker

echo "=================================================="
echo " Setup Completed Successfully!"
echo "=================================================="
echo " - API Endpoint: http://localhost:${APP_PORT:-8080}/api/v1/notifications/dispatch"
echo " - Swagger UI: http://localhost:${APP_PORT:-8080}/api/documentation"
echo " - Mailpit Web UI: http://localhost:${FORWARD_MAILPIT_DASHBOARD_PORT:-8025}"
echo " - Test Suite: ./vendor/bin/sail test"
echo "=================================================="
