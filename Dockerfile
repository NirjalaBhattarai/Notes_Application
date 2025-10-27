# Use official PHP + Apache image
FROM php:8.2-apache

# Install PHP extensions
RUN apt-get update && apt-get install -y libsqlite3-dev \
    && docker-php-ext-install pdo pdo_sqlite

# Enable Apache rewrite module
RUN a2enmod rewrite

# Copy project files
COPY . /var/www/html/

# Set working directory to Laravel's public folder
WORKDIR /var/www/html/public

# Give permissions
RUN chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
