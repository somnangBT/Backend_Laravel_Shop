FROM php:8.2-fpm-alpine

# Install system dependencies (បានថែម postgresql-dev និង libzip-dev រួចរាល់)
RUN apk add --no-cache nginx wget nodejs npm git openssh-client bash \
    libpng-dev libjpeg-turbo-dev freetype-dev libzip-dev zip unzip postgresql-dev

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

# Setup Nginx configuration directly
RUN mkdir -p /run/nginx && echo 'server { listen 80; root /var/www/public; index index.php index.html; charset utf-8; location / { try_files $uri $uri/ /index.php?$query_string; } location ~ \.php$ { fastcgi_pass 127.0.0.1:9000; fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name; include fastcgi_params; } }' > /etc/nginx/http.d/default.conf

# Permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 80


CMD sh -c "php artisan config:clear && php artisan route:clear && php artisan cache:clear && php-fpm & nginx -g 'daemon off;'"