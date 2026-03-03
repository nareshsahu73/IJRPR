#!/bin/bash

echo "Setting up Paper Submission System..."

# Install dependencies
echo "Installing composer dependencies..."
composer install

# Remove Spatie Permission (not needed)
echo "Removing unnecessary packages..."
composer remove spatie/laravel-permission bezhansalleh/filament-shield --no-interaction 2>/dev/null || true

# Copy environment file
if [ ! -f .env ]; then
    echo "Creating .env file..."
    cp .env.example .env
    php artisan key:generate
fi

# Run migrations
echo "Running migrations..."
php artisan migrate

# Create storage link
echo "Creating storage link..."
php artisan storage:link

# Seed admin user
echo "Creating admin user..."
php artisan db:seed --class=SuperAdminSeeder

echo ""
echo "Setup complete!"
echo ""
echo "Admin credentials:"
echo "Email: admin@example.com"
echo "Password: password"
echo ""
echo "Start the server with: php artisan serve"
echo "User Panel: http://localhost:8000"
echo "Admin Panel: http://localhost:8000/admin"
