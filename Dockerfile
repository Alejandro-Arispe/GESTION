FROM php:8.2-apache

# Instalar dependencias
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpq-dev \
    libzip-dev \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Instalar extensiones de PHP (PostgreSQL en lugar de MySQL)
RUN docker-php-ext-install \
    pdo_pgsql \
    bcmath \
    zip

# Habilitar mod_rewrite para Laravel
RUN a2enmod rewrite

# Obtener Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Crear directorio de la aplicación
WORKDIR /var/www/html

# Copiar archivos del proyecto
COPY . .

# Crear y dar permisos a directorios necesarios ANTES de Composer
RUN mkdir -p bootstrap/cache storage/logs storage/app \
    && chmod -R 777 bootstrap/cache storage/logs storage/app \
    && chown -R www-data:www-data /var/www/html

# Instalar dependencias de Composer
RUN composer install --no-dev --optimize-autoloader

# Cambiar permisos finales
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Configurar Apache para Laravel (public folder como root)
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Exponer puerto 80
EXPOSE 80
CMD ["apache2-foreground"]