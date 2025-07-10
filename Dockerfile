FROM php:8.2-apache

# Instalar dependencias necesarias para compilar pdo_pgsql
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Copia todos los archivos de tu proyecto al directorio raíz del servidor
COPY . /var/www/html/

# Da permisos adecuados a los archivos
RUN chown -R www-data:www-data /var/www/html

# Habilita mod_rewrite (opcional, pero útil)
RUN a2enmod rewrite

# Expone el puerto 80 para que Render lo use
EXPOSE 80
