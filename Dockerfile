# Use official PHP + Apache image
FROM php:8.2-apache

# Install necessary extensions
RUN docker-php-ext-install pdo pdo_sqlite

# Enable Apache mod_rewrite (needed for Laravel routing)
RUN a2enmod rewrite

# Copy all files to the container
COPY . /var/www/html

# Set working directory
WORKDIR /var/www/html

# Set Laravel’s public directory as the Apache root
RUN echo "<VirtualHost *:80>\n\
    DocumentRoot /var/www/html/public\n\
    <Directory /var/www/html/public>\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
</VirtualHost>" > /etc/apache2/sites-available/000-default.conf

# Give permissions
RUN chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
