# Use the official PHP image as the base
FROM php:8.3-fpm

# Set working directory
WORKDIR /var/www

# Install system dependencies
RUN apt-get update && apt-get install -y \
    apt-transport-https \
    build-essential \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    unzip \
    curl \
    nano \
    cron \
    supervisor \
    sudo \
    dos2unix \
    procps \
    git \
    ffmpeg \
    libmagickwand-dev \
    libicu-dev \
    netcat-traditional \
    nodejs npm  # Install Node.js & NPM

# Verify Node.js and NPM installation
RUN node -v && npm -v

# Clear the cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql zip exif pcntl

# Install and configure intl extension
RUN docker-php-ext-install intl

# Configure and install the gd extension
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

# Install Redis extension via PECL
RUN pecl install redis \
    && docker-php-ext-enable redis

# Install Redis CLI (redis-tools)
RUN apt-get update && apt-get install -y redis-tools

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Clear Composer cache
RUN composer clear-cache

# Copy php.ini configuration file
COPY ./docker/php/php.ini /usr/local/etc/php/php.ini

COPY ./docker/php/wait-for-it.sh /usr/local/bin/wait-for-it.sh
RUN chmod +x /usr/local/bin/wait-for-it.sh

# Copy existing application directory contents
COPY . /var/www

# Configurations for Supervisor and Cron
COPY ./docker/php/supervisord/supervisord.conf /etc/supervisor/
COPY ./docker/php/supervisord/scripts/* /etc/supervisor/conf.d/
COPY ./docker/php/crons/* /etc/cron.d/

# Set ownership and permissions for the storage and bootstrap/cache directories
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache \
    && chmod -R 777 /var/www/storage /var/www/bootstrap/cache

# Install project dependencies
RUN npm install

# Touch log files
RUN touch /var/log/cron.log \
    && touch /var/www/storage/logs/crontab.log \
    && touch /var/www/storage/logs/supervisord.log

# Fix encryption issues for the schedule file and add it to crontab
RUN dos2unix /etc/cron.d/schedule \
    && crontab /etc/cron.d/schedule \
    && chmod 0644 /etc/cron.d/schedule \
    && service cron restart

# Expose PHP-FPM port
EXPOSE 9000

# Copy the init script into the container
COPY ./docker/php/init.sh /usr/local/bin/init.sh

# Ensure it has execute permissions
RUN chmod +x /usr/local/bin/init.sh

# Start Supervisor
CMD ["/bin/bash", "-c", "npm install && npm run dev & /usr/bin/supervisord -c /etc/supervisor/supervisord.conf"]
