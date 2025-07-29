# Use PHP 8.2 with Apache as base image
FROM php:8.2-apache

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Install Node.js dependencies and build assets
RUN npm ci && npm run build && npm prune --production

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Configure Apache
RUN a2enmod rewrite
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

# Create startup script
RUN echo '#!/bin/bash' > /startup.sh && \
    echo 'set -e' >> /startup.sh && \
    echo 'echo "Starting Laravel application..."' >> /startup.sh && \
    echo 'if [ ! -f .env ]; then' >> /startup.sh && \
    echo '  cp .env.example .env' >> /startup.sh && \
    echo 'fi' >> /startup.sh && \
    echo 'echo "Laravel application ready!"' >> /startup.sh && \
    echo 'exec apache2-foreground' >> /startup.sh && \
    chmod +x /startup.sh

# Expose port 80
EXPOSE 80

# Start with our custom startup script
CMD ["/startup.sh"] 