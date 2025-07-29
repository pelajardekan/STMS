# Use PHP 8.2 FPM Alpine as base image
FROM php:8.2-fpm-alpine

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm \
    supervisor \
    nginx \
    oniguruma-dev \
    autoconf \
    g++ \
    make \
    gettext

# Configure and install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application files first
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Install Node.js dependencies (including dev dependencies for build)
RUN npm ci

# Build frontend assets
RUN npm run build

# Remove dev dependencies to reduce image size
RUN npm prune --production

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Copy configuration files
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini

# Create startup script
RUN echo '#!/bin/sh' > /startup.sh && \
    echo 'set -e' >> /startup.sh && \
    echo 'echo "Starting Laravel application..."' >> /startup.sh && \
    echo 'if [ ! -f .env ]; then' >> /startup.sh && \
    echo '  cp .env.example .env' >> /startup.sh && \
    echo 'fi' >> /startup.sh && \
    echo 'echo "Laravel application ready!"' >> /startup.sh && \
    echo 'exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf' >> /startup.sh && \
    chmod +x /startup.sh

# Create necessary directories
RUN mkdir -p /tmp /var/log/nginx

# Expose port 80 (Azure App Service will override this with PORT env var)
EXPOSE 80

# Start with our custom startup script
CMD ["/startup.sh"] 