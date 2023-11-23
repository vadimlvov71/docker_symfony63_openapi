# Start with the official PHP 8.2 image
FROM php:8.2-fpm

# Install system dependencies and clean cache
RUN apt-get update && apt-get install -y \
 git \
 curl \
 libpng-dev \
 libonig-dev \
 libxml2-dev \
 zip \
 unzip

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Copy Composer from the official Composer image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/symfony_api
 
#TODO: change root to user
CMD ["bash", "-c", "composer install; php-fpm; chown -R www-data var/www/symfony_api"]
RUN chown -R www-data:www-data  /var/www/symfony_api

# Change current user to www
USER www-data





