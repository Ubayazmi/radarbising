#!/bin/sh
# Startup script for Vercel - configures PHP-FPM to listen on $PORT

# Default PORT to 8080 if not set (Vercel sets this)
export PORT=${PORT:-8080}

# Create PHP-FPM pool config that listens on $PORT
cat > /usr/local/etc/php-fpm.d/www.conf <<EOF
[www]
user = www-data
group = www-data
listen = 0.0.0.0:${PORT}
listen.owner = www-data
listen.group = www-data
listen.mode = 0660

pm = dynamic
pm.max_children = 5
pm.start_servers = 2
pm.min_spare_servers = 1
pm.max_spare_servers = 3

request_terminate_timeout = 30s
request_slowlog_timeout = 10s
slowlog = /var/log/php-fpm-slow.log

php_admin_value[error_log] = /var/log/php-fpm-error.log
php_admin_flag[log_errors] = on

catch_workers_output = yes
decorate_workers_output = no
EOF

# Ensure log directory exists
mkdir -p /var/log

# Start PHP-FPM in foreground
exec php-fpm -F -R