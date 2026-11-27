# Use official FrankenPHP image which has most extensions or an easy installer
FROM dunglas/frankenphp:1.4-php8.2-alpine

# Install PHP extensions using the built-in helper (much more reliable)
RUN install-php-extensions \
    pdo_pgsql \
    pgsql \
    gd \
    zip \
    intl \
    bcmath \
    opcache \
    sockets \
    mbstring

# Install system dependencies
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
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# Copy application code
COPY . .

# Ensure storage directories exist
RUN mkdir -p /var/task/sessions /var/task/admin/uploads /var/task/writer/uploads \
    && chmod -R 755 /var/task/sessions /var/task/admin/uploads /var/task/writer/uploads

# Expose port (Vercel Container runtime uses PORT env var)
EXPOSE 8080

# Start FrankenPHP and make it listen on the PORT env var
CMD ["frankenphp", "php-server", "--listen", "${PORT:-8080}"]