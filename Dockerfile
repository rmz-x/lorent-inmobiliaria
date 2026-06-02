FROM php:8.2-apache

# =========================
# Dependencias del sistema
# =========================
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libpng-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    unzip \
    zip \
    git \
    curl \
    && docker-php-ext-install \
    pdo \
    pdo_pgsql \
    mbstring \
    zip \
    gd \
    dom

# =========================
# Composer
# =========================
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# =========================
# Node.js (Vite)
# =========================
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# =========================
# Apache
# =========================
RUN a2enmod rewrite

# =========================
# Copiar proyecto
# =========================
COPY . /var/www/html
WORKDIR /var/www/html

# =========================
# Dependencias PHP
# =========================
RUN composer install --no-dev --optimize-autoloader

# =========================
# Dependencias JS
# =========================
RUN npm install --legacy-peer-deps
RUN npm run build

# =========================
# Apache apunta a /public
# =========================
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' \
    /etc/apache2/sites-available/000-default.conf

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

RUN echo '<Directory /var/www/html/public>' > /etc/apache2/conf-available/laravel.conf && \
    echo 'AllowOverride All' >> /etc/apache2/conf-available/laravel.conf && \
    echo 'Require all granted' >> /etc/apache2/conf-available/laravel.conf && \
    echo '</Directory>' >> /etc/apache2/conf-available/laravel.conf && \
    a2enconf laravel

# =========================
# Permisos
# =========================
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 775 storage bootstrap/cache

# =========================
# Puerto Render
# =========================
EXPOSE 10000

CMD ["apache2-foreground"]