#!/bin/bash
set -e

# Crear directorios de almacenamiento con permisos
mkdir -p storage/logs storage/framework/sessions storage/framework/views storage/framework/cache bootstrap/cache
chmod -R 777 storage bootstrap/cache

# Ejecutar migraciones
php artisan migrate --force

# Cachear configuración
php artisan config:cache
php artisan route:cache

# Iniciar Apache
apache2-foreground
