#!/bin/sh
# Startup script for Vercel - starts nginx + PHP-FPM

export PORT=${PORT:-8080}

# Create PHP-FPM pool config (listens on 127.0.0.1:9000 for nginx)
cat > /usr/local/etc/php-fpm.d/www.conf <<EOF
[www]
user = www-data
group = www-data
listen = 127.0.0.1:9000
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

# Create nginx config
cat > /etc/nginx/nginx.conf <<EOF
user www-data;
worker_processes auto;
error_log /var/log/nginx/error.log warn;
pid /var/run/nginx.pid;

events {
    worker_connections 1024;
}

http {
    include /etc/nginx/mime.types;
    default_type application/octet-stream;

    log_format main '\$remote_addr - \$remote_user [\$time_local] "\$request" '
                    '\$status \$body_bytes_sent "\$http_referer" '
                    '"\$http_user_agent" "\$http_x_forwarded_for"';

    access_log /var/log/nginx/access.log main;

    sendfile on;
    tcp_nopush on;
    tcp_nodelay on;
    keepalive_timeout 65;
    types_hash_max_size 2048;

    # Gzip
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css text/xml text/javascript application/javascript application/json application/xml;

    upstream php-fpm {
        server 127.0.0.1:9000;
    }

    server {
        listen ${PORT};
        listen [::]:${PORT};
        server_name _;

        root /var/task;
        index index.php;

        # Security headers
        add_header X-Frame-Options "SAMEORIGIN";
        add_header X-Content-Type-Options "nosniff";
        add_header X-XSS-Protection "1; mode=block";

        # Static assets caching
        location ~* \.(?:css|js|jpg|jpeg|gif|png|ico|svg|woff|woff2|ttf|eot)$ {
            expires 1y;
            add_header Cache-Control "public, immutable";
            try_files \$uri =404;
        }

        # Favicon
        location = /favicon.ico {
            try_files /favicon.ico =404;
        }
        location = /favicon.png {
            try_files /favicon.png =404;
        }

        # PHP handling
        location ~ \.php$ {
            fastcgi_pass php-fpm;
            fastcgi_index index.php;
            fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
            include fastcgi_params;
            fastcgi_read_timeout 30s;
        }

        # Main front controller
        location / {
            try_files \$uri \$uri/ /index.php?\$query_string;
        }

        # Health & diagnose endpoints (direct PHP)
        location ~ ^/(health|diagnose)\.php$ {
            fastcgi_pass php-fpm;
            fastcgi_index index.php;
            fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
            include fastcgi_params;
            fastcgi_read_timeout 30s;
        }

        # Deny access to sensitive files
        location ~ /\.(?!well-known) {
            deny all;
        }
        location ~* \.(?:env|log|sql|sqlite|db)$ {
            deny all;
        }
    }
}
EOF

# Ensure log directories exist
mkdir -p /var/log/nginx /var/log/php-fpm

# Start PHP-FPM in background
php-fpm -D -R

# Start nginx in foreground (blocking)
exec nginx -g "daemon off;"