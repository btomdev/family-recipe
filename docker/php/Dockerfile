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
        shadow

# Install necessary php extentions
# https://github.com/mlocati/docker-php-extension-installer?tab=readme-ov-file#downloading-the-script-on-the-fly-with-add
ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN install-php-extensions \
    gd \
    pdo_mysql \
    sodium \
    zip \
    xsl

# Production
#RUN install-php-extensions \
#    opcache \

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

ARG USER_ID
ARG USER_NAME
ARG GROUP_ID

RUN groupadd -f -g ${GROUP_ID} ${USER_NAME}
RUN useradd -u ${USER_ID} -g ${GROUP_ID} ${USER_NAME}

USER ${USER_NAME}