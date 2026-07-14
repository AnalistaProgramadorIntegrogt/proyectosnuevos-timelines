FROM php:8.3-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    postgresql-dev \
    libzip-dev \
    zip \
    unzip \
    curl \
    git \
    oniguruma-dev \
    libpng-dev \
    libxml2-dev \
    icu-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql zip mbstring gd xml bcmath intl

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Node.js 20
RUN apk add --no-cache nodejs npm

# Set working directory
WORKDIR /var/www/html

# Copy entrypoint
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 9000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
