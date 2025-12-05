# 1. Usar una imagen oficial de PHP con Apache
FROM php:8.0-apache

# 2. Instalar la extensión mysqli para la conexión a la base de datos
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# 3. Instalar Composer y sus dependencias
RUN apt-get update && apt-get install -y unzip \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer install --no-dev --optimize-autoloader

# 4. Copiar el código de la aplicación al directorio web del contenedor
# Se respetará el .dockerignore que creamos antes
COPY . /var/www/html/

# 5. Modificar el archivo de configuración para que apunte a la BD del host (tu Mac)
RUN sed -i "s/'127.0.0.1'/'host.docker.internal'/g" /var/www/html/Config/Config.php

# 6. Habilitar el módulo rewrite de Apache para las URLs amigables
RUN a2enmod rewrite

# 7. Exponer el puerto 80 del contenedor
EXPOSE 80
