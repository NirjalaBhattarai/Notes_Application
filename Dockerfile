# Base image: PHP 8.2 + Apache
FROM php:8.2-apache

# Install PHP extensions for SQLite
RUN apt-get update && apt-get install -y libsqlite3-dev \
    && docker-php-ext-install pdo pdo_sqlite

# Enable Apache rewrite module (needed for Laravel routing)
RUN a2enmod rewrite

# Copy all Laravel project files into the container
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html

# Set Apache root to Laravel public folder and allow .htaccess overrides
RUN sed -i 's#DocumentRoot /var/www/html#DocumentRoot /var/www/html/public#g' /etc/apache2/sites-available/000-default.conf \
    && echo "<Directory /var/www/html/public>\n\
        Options Indexes FollowSymLinks\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>" >> /etc/apache2/sites-available/000-default.conf

# Give permissions to Laravel folders
RUN chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database /var/www/html/public

# Expose port 80
EXPOSE 80

# Start Apache in foreground
CMD ["apache2-foreground"]
