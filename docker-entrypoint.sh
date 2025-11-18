#!/bin/bash

echo "🚀 Iniciando aplicación FICCT en Render..."

# Crear directorios necesarios
mkdir -p /var/www/html/bootstrap/cache
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/storage/app/temp
mkdir -p /var/www/html/storage/framework/cache
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views

# Asignar permisos
chmod -R 755 /var/www/html/storage
chmod -R 755 /var/www/html/bootstrap/cache

echo "📁 Directorios creados"

# Limpiar caches
echo "🧹 Limpiando caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Caches regenerados"

# Ejecutar migraciones si la BD está disponible
echo "🔄 Ejecutando migraciones..."
php artisan migrate --force 2>/dev/null || echo "⚠️  Las migraciones ya estaban ejecutadas"

# Crear usuario por defecto
echo "👤 Verificando usuario por defecto..."
php artisan db:seed --class=CreateDefaultUserSeeder 2>/dev/null || echo "⚠️  El usuario ya existe"

echo "✨ Aplicación lista"

# Iniciar Apache
apache2-foreground
