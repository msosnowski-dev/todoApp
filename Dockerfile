FROM php:8.2-fpm-alpine

WORKDIR /var/www/html

RUN apk add --no-cache \
    nginx \
    build-base \
    sqlite-dev \
    libzip-dev \
    libpng-dev \
    jpeg-dev \
    libwebp-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    icu-dev \
    git \
    nodejs \
    npm \
    rsync \
    oniguruma-dev


RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) \
        pdo_sqlite \
        pdo_mysql \
        mysqli \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        intl \
        zip \
    && rm -rf /tmp/*

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

EXPOSE 9000

CMD ["php-fpm"]
