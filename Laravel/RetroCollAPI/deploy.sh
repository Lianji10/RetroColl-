#!/bin/bash
# Script de despliegue de RetroColl en Hostinger
# Ejecutar desde la raíz del usuario SSH: /home/u673282592

echo "=== Desplegando RetroColl ==="

# Ir al directorio del proyecto
cd ~/domains/giancweb.com/public_html/retrocoll || exit 1

# Instalar dependencias de PHP (sin dev)
composer install --no-dev --optimize-autoloader 2>&1

# Copiar .env de producción
cp .env.production .env

# Generar clave de aplicación (si no existe)
php artisan key:generate --force

# Crear enlace simbólico para storage
php artisan storage:link

# Ejecutar migraciones
php artisan migrate --force

# Ejecutar seeders (solo si es la primera vez)
php artisan db:seed --force

# Limpiar y cachear configuración
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Establecer permisos
chmod -R 775 storage bootstrap/cache

echo "=== Despliegue completado ==="
echo "Visita: https://retrocoll.giancweb.com"
