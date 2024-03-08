FROM php:8.1-fpm

RUN apt update \
    && apt install -y zlib1g-dev g++ git libicu-dev zip libzip-dev zip libpng-dev wget python3\
    && docker-php-ext-install intl opcache pdo pdo_mysql \
    && pecl install apcu \
    && docker-php-ext-enable apcu \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip \
    && docker-php-ext-install gd \
    && pecl install redis \
    && docker-php-ext-enable redis

RUN pecl install xdebug
RUN docker-php-ext-enable xdebug

WORKDIR /var/www/event-wave

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Add a non-root user for laravel application
RUN groupadd event-wave-group
RUN useradd -ms /bin/bash -g event-wave-group event-wave

USER event-wave

RUN echo 'alias ll="ls -l"' >> ~/.bashrc

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]


