FROM php:8.4-fpm

# Descargar el instalador de extensiones de PHP (herramienta recomendada)
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

# Instalar dependencias del sistema, extensiones de PHP y Composer en un solo paso
RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions \
    pdo \
    pdo_mysql \
    xml \
    ctype \
    zip \
    mongodb \
    xdebug \
    @composer

# Configuración PHP
RUN cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini

# Crear un grupo con GID 1000 (ignora el error si ya existe)
RUN groupadd -g 1000 app || true

# Crear usuario y añadirlo al grupo app (ignora el error si ya existe)
RUN useradd -u 1000 -g 1000 -m -s /bin/bash app || true

# Asegurar que el directorio existe y darle los permisos al usuario app
RUN mkdir -p /var/www/html && chown -R app:app /var/www/html

# Directorio de trabajo
WORKDIR /var/www/html