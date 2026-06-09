FROM php:8.4-fpm

# Instalar extensiones del sistema y dependencias de PostgreSQL
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libpq-dev \
    zip \
    unzip \
    git \
    curl

# Instalar Node.js y NPM (Versión 18 LTS)
RUN curl -sL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Instalar pnpm de forma global para mayor seguridad y velocidad
RUN npm install -g pnpm

# Instalar extensiones de PHP necesarias para Laravel y PostgreSQL
RUN docker-php-ext-install pdo_pgsql gd

# Instalar Composer (la última versión oficial)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar el directorio de trabajo
WORKDIR /var/www

# Copiar los archivos del proyecto al contenedor
COPY . .

# Exponer el puerto para que PHP-FPM funcione (opcional pero recomendado)
EXPOSE 9000