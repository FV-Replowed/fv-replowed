FROM php:8.3-apache

# Install Additional System Dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    curl \
    gnupg

# Install Node.js and npm
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && \
    apt-get install -y nodejs

# Verify installation
RUN node -v && npm -v

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Enable Apache mod_rewrite for URL rewriting
RUN a2enmod rewrite

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql zip

# Configure Apache DocumentRoot to point to Laravel's public directory
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Copy the application code
COPY . /var/www/html

# Set the working directory
WORKDIR /var/www/html

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Install Node.js dependencies
RUN npm install

# Build frontend assets
RUN npm run build

# Generate Key
RUN php artisan key:generate

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
