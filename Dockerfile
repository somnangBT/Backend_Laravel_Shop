FROM php:8.2-fpm-alpine

# Install system dependencies
RUN apk add --no-cache nginx wget nodejs npm git openssh-client bash \
    libpng-dev libjpeg-turbo-dev freetype-dev libzip-dev zip unzip

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql gd zip

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Setup working directory
WORKDIR /var/www

# Copy project files
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Setup Nginx configuration
RUN mkdir -p /run/nginx
COPY .render/nginx.conf /etc/nginx/nginx.conf

# Permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 80

CMD ["sh", "-c", "nginx && php-fpm"]