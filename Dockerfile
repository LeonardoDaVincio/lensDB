# Use Apache with PHP installed
FROM php:7.0-apache
# Copy Files
COPY ./src/ /var/www/
# Create Database in /var/www/
WORKDIR /var/www/
RUN php /var/www/createDB.php
# Change Workdir to webroot
WORKDIR /var/www/html/
# Set Ownership of all Files to apache user
run chown www-data:www-data /var/www -R
# Set entrypoint to apache2 webserver
ENTRYPOINT "apache2-foreground"