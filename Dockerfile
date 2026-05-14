FROM php:8.2-apache

# Dependencias del sistema
RUN apt-get update && apt-get install -y \
    libpq-dev unzip git curl \
    && docker-php-ext-install pdo pdo_pgsql

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Node.js (para Vite)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Apache rewrite
RUN a2enmod rewrite

# Copiar proyecto
COPY . /var/www/html/
WORKDIR /var/www/html



# PHP deps
RUN composer install --no-dev --optimize-autoloader

#--------------------
RUN php artisan storage:link
#---------------------

# JS deps + build
RUN npm install --legacy-peer-deps
RUN npm run build

#Js deeps + build
RUN ls -la public
RUN ls -la public/build
RUN cat public/build/manifest.json

# Apache apunta a public/
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' \
    /etc/apache2/sites-available/000-default.conf

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf


 # Laravel Apache config
RUN echo '<Directory /var/www/html/public>' > /etc/apache2/conf-available/laravel.conf && \
    echo 'AllowOverride All' >> /etc/apache2/conf-available/laravel.conf && \
    echo 'Require all granted' >> /etc/apache2/conf-available/laravel.conf && \
    echo '</Directory>' >> /etc/apache2/conf-available/laravel.conf && \
    a2enconf laravel


# Permisos Laravel
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/public \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Puerto
EXPOSE 10000
#EXPOSE ${PORT}

# PROCESO PRINCIPAL (CLAVE)

CMD ["apache2-foreground"]

#CMD php artisan migrate --force || true && apache2-foreground