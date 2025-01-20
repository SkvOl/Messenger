FROM php:8.2-fpm

ENV TZ="Europe/Moscow"
RUN date

WORKDIR /var/www/messenger

RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    libzip-dev \
    unzip \
    git \
    libonig-dev \
    curl \
    procps \
    systemd

RUN docker-php-ext-install mbstring zip exif pcntl
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install gd

RUN apt update -q && \
    apt install -q -y libpq-dev && \
    docker-php-ext-install pdo_pgsql pgsql

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer


COPY . /var/www/messenger

# RUN cd /var/www/ && composer create-project laravel/laravel:^11.0 messenger
RUN apt-get install -y nodejs npm && npm i pusher-js && npm i laravel-echo


COPY ./scripts /
RUN chmod +x /entrypoint.sh

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN chmod 777 storage/logs
RUN chmod 777 storage/logs/laravel.log

EXPOSE 9000 9001
# ENTRYPOINT /entrypoint.sh
CMD ["php-fpm"]