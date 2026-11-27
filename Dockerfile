# Use official PHP-FPM Alpine image
FROM php:8.2-fpm-alpine

# Build cache bust
ARG CACHEBUST=6
RUN echo "Cache bust: $CACHEBUST"

# Install PHP extensions
RUN apk add --no-cache \
    postgresql-dev \
    libpq \
    && docker-php-ext-install -j$(nproc) \
    pdo_pgsql \
    pgsql \
    mbstring \
    gd \
    zip \
    intl \
    bcmath \
    opcache \
    sockets

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

# PHP configuration for production
RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.enable_cli=1" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.memory_consumption=128" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.max_accelerated_files=4000" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "session.cookie_secure=1" >> /usr/local/etc/php/conf.d/session.ini \
    && echo "session.cookie_httponly=1" >> /usr/local/etc/php/conf.d/session.ini \
    && echo "session.cookie_samesite=Lax" >> /usr/local/etc/php/conf.d/session.ini \
    && echo "session.use_strict_mode=1" >> /usr/local/etc/php/conf.d/session.ini

# Expose port (Vercel sets PORT env var)
EXPOSE 8080

# Start PHP-FPM listening on PORT env var
CMD ["sh", "-c", "php-fpm -y /usr/local/etc/php-fpm.conf -R"]
