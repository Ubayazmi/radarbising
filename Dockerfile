# Vercel PHP Docker Runtime - Official
# Uses vercel/php:8.2 image with full extension support
FROM vercel/php:8.2

# Install additional extensions if needed (pdo_pgsql, pgsql already included)
# RUN apt-get update && apt-get install -y libpq-dev && docker-php-ext-install pdo_pgsql pgsql

# Copy application files
COPY . /var/task

# Set working directory
WORKDIR /var/task

# Install Composer dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Ensure storage directories exist
RUN mkdir -p /var/task/sessions /var/task/admin/uploads /var/task/writer/uploads

# Expose port (not used by Vercel but good practice)
EXPOSE 8080

# Start PHP built-in server (Vercel handles routing)
CMD ["php", "-S", "0.0.0.0:8080", "api/index.php"]