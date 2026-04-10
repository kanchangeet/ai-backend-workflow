#!/bin/sh
set -e

# When starting as PHP-FPM (the web server), generate Swagger docs first.
# Queue workers skip this — they don't serve HTTP.
if [ $# -eq 0 ]; then
    php artisan l5-swagger:generate
    exec php-fpm
else
    exec "$@"
fi
