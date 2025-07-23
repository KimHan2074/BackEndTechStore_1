# FROM php:8.2-apache

# # Set working directory
# WORKDIR /var/www/html

# # Install dependencies
# RUN apt-get update && apt-get install -y \
#     git \
#     curl \
#     zip \
#     unzip \
#     libzip-dev \
#     libpng-dev \
#     libjpeg-dev \
#     libonig-dev \
#     libxml2-dev \
#     libpq-dev \
#     libcurl4-openssl-dev \
#     libssl-dev \
#     gnupg \
#     ca-certificates

# # Enable Apache Rewrite module
# RUN a2enmod rewrite

# # Install PHP extensions
# RUN docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl bcmath gd

# # Install Composer
# RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer

# # Copy source code
# COPY . .

# # Laravel dependencies
# RUN composer install --no-interaction --prefer-dist --optimize-autoloader --ignore-platform-reqs

# # Laravel setup (comment if fails)
# # RUN php artisan key:generate && \
# #     php artisan config:cache && \
# #     php artisan route:cache && \
# #     php artisan migrate --force

# # Set permissions
# RUN chown -R www-data:www-data /var/www/html && \
#     chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# # Apache config: allow .htaccess
# RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# # Expose port
# EXPOSE 80

# CMD ["apache2-foreground"]

FROM php:8.2-fpm

# Set working directory
WORKDIR /app

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip libzip-dev libpng-dev libjpeg-dev \
    libonig-dev libxml2-dev libpq-dev libcurl4-openssl-dev \
    libssl-dev gnupg ca-certificates

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl bcmath gd

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer

# Copy source code
COPY . .

# Install Laravel dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --ignore-platform-reqs

# Set permissions
RUN chown -R www-data:www-data /app && chmod -R 775 /app/storage /app/bootstrap/cache

# Set environment port
ENV PORT=8080
EXPOSE 8080

# Run Laravel dev server
CMD php artisan serve --host=0.0.0.0 --port=$PORT
