# Use official FrankenPHP image
FROM dunglas/frankenphp:1.4-php8.2-alpine

# Install PHP extensions including PostgreSQL driver
RUN install-php-extensions \
    pdo_pgsql \
    pgsql

# Install system dependencies needed
RUN apk add --no-cache \
    zip \
    unzip \
    git \
    curl

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/task

# Copy composer files first for caching
COPY composer.json composer.lock ./

# Install PHP dependencies
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# Copy application code
COPY . .

# Ensure storage directories exist
RUN mkdir -p /var/task/sessions /var/task/admin/uploads /var/task/writer/uploads \
    && chmod -R 755 /var/task/sessions /var/task/admin/uploads /var/task/writer/uploads

# Expose port (Vercel sets PORT env var)
EXPOSE 8080

# Start FrankenPHP listening on PORT env var (Vercel sets this)
# Using shell to expand $PORT
CMD ["sh", "-c", "frankenphp php-server --listen 0.0.0.0:$PORT"]
