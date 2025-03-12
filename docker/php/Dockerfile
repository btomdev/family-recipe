ARG PHP_VERSION

# Use the official PHP FPM Alpine image
FROM php:${PHP_VERSION}-fpm-alpine

# Install necessary packages
RUN apk update && \
    apk add --no-cache \
        freetype-dev \
        libjpeg-turbo-dev \
        libpng-dev \
        git \
        nodejs \
        npm \
        bash \
        mysql-client \
        libsodium-dev \
        libzip-dev \
        libxslt-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install pdo_mysql sodium zip \
    && docker-php-ext-install xsl

# Clean up package cache
RUN rm -rf /var/cache/apk/*

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install Symfony CLI
RUN curl -sS https://get.symfony.com/cli/installer | bash && \
    mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

# Copy default php config
COPY php.ini /usr/local/etc/php/php.ini

# Set working directory
WORKDIR /var/www