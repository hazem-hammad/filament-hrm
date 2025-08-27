#!/bin/bash

# Wait for MySQL to be ready before running migrations
echo "Waiting for MySQL to be ready..."
/usr/local/bin/wait-for-it.sh mysql:3306 --timeout=60 --strict -- echo "MySQL is up!"

echo "MySQL is up and running!"

# Navigate to the application directory
cd /var/www || exit

# Copy .env.example to .env if .env doesn't exist
if [ ! -f .env ]; then
    echo "Copying .env.example to .env..."
    cp .env.example .env
else
    echo ".env file already exists. Skipping copy."
fi


# Check if APP_KEY is not set and generate a new key
if ! grep -q "^APP_KEY=" .env || [ -z "$(grep '^APP_KEY=' .env | cut -d '=' -f2)" ]; then
    echo "Generating application key..."
    php artisan key:generate
else
    echo "Application key already set."
fi

# Check if database exists, if not create it
echo "Checking if database ${DB_DATABASE} exists..."
DB_EXISTS=$(mysql -h mysql -u${DB_USERNAME} -p${DB_PASSWORD} -e "SHOW DATABASES LIKE '${DB_DATABASE}';" | grep "${DB_DATABASE}" > /dev/null; echo $?)
if [ $DB_EXISTS -ne 0 ]; then
    echo "Database ${DB_DATABASE} does not exist. Creating..."
    mysql -h mysql -u${DB_USERNAME} -p${DB_PASSWORD} -e "CREATE DATABASE ${DB_DATABASE};"
else
    echo "Database ${DB_DATABASE} already exists."
fi

# Install Composer dependencies
echo "Running Composer install..."
composer install

# Run database migrations
echo "Running database migrations..."
php artisan migrate --force

# Run database seeding (optional)
php artisan db:seed --force

# Create Super Admin
php artisan shield:super-admin


# Clear and cache configurations
echo "Clearing and caching configurations..."
php artisan cache:clear
php artisan config:clear

echo "Storage:link..."
php artisan storage:link

# Set proper permissions
echo "Setting permissions..."
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Check if APP_KEY is not set and generate a new key
if ! grep -q "^APP_KEY=" .env || [ -z "$(grep '^APP_KEY=' .env | cut -d '=' -f2)" ]; then
    echo "Generating application key..."
    php artisan key:generate
else
    echo "Application key already set."
fi

# Run npm install and build
echo "Running npm install and build..."
npm install
npm run build

echo "Initialization complete!"

