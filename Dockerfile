FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libicu-dev \
    nginx \
    supervisor

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql intl

# Copy composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/symfony

# Copy configuration files
COPY ./nginx.conf /etc/nginx/conf.d/default.conf
COPY ./supervisord.conf /etc/supervisor/conf.d/supervisord.conf
RUN echo "max_input_vars = 1000" >> /usr/local/etc/php/conf.d/docker-php-max-input-vars.ini

# Expose ports
EXPOSE 9000
EXPOSE 8080

RUN mkdir -p /var/www/symfony/vendor/symfony

# Copy application files and set permissions
COPY --chown=www-data:www-data . /var/www/symfony

# Set environment
ENV APP_ENV=prod

# Switch to www-data user
USER www-data

# Install dependencies and warm up cache
RUN composer install \
    && composer dump-autoload --optimize \
    && php bin/console cache:clear --env=prod \
    && php bin/console cache:warmup --env=prod

# Switch back to root for supervisord
USER root

# Run supervisord
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/conf.d/supervisord.conf"]