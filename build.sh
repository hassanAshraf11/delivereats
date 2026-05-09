#!/usr/bin/env bash
# Exit on error
set -o errexit

# Install PHP dependencies
composer install --no-interaction --prefer-dist --optimize-autoloader

# Install Node dependencies and build assets
npm install
npm run build

# Clear caches
php artisan optimize:clear

# Run migrations (Force is required for production)
php artisan migrate --force

# Seed the database if it is empty (optional, useful for demo)
# php artisan db:seed --force
