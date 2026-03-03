#!/bin/bash
set -e

# Fix permissions for media-storage directory
if [ -d "/var/www/html/public/media-storage" ]; then
    chown -R www-data:www-data /var/www/html/public/media-storage
    chmod -R 775 /var/www/html/public/media-storage
fi

# Fix permissions for storage directory
if [ -d "/var/www/html/storage" ]; then
    chown -R www-data:www-data /var/www/html/storage
    chmod -R 775 /var/www/html/storage
fi

# Fix permissions for bootstrap/cache directory
if [ -d "/var/www/html/bootstrap/cache" ]; then
    chown -R www-data:www-data /var/www/html/bootstrap/cache
    chmod -R 775 /var/www/html/bootstrap/cache
fi

# Execute the original command
exec "$@"

