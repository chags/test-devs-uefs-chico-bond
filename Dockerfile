FROM php:8.4-fpm

ARG USER_ID=1000
ARG GROUP_ID=1000

ENV COMPOSER_ALLOW_SUPERUSER=1 \
    APP_ENV=local \
    HOME=/var/www/html \
    XDG_CONFIG_HOME=/var/www/html/.config \
    PSYSH_CONFIG_DIR=/var/www/html/.config/psysh

RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip \
    sqlite3 libsqlite3-dev libzip-dev bash \
    default-mysql-client netcat-openbsd procps \
  && docker-php-ext-install pdo pdo_mysql pdo_sqlite mbstring \
     bcmath exif pcntl gd zip \
  && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
  && apt-get install -y nodejs \
  && npm install -g npm@latest \
  && curl -sS https://getcomposer.org/installer \
     | php -- --install-dir=/usr/local/bin --filename=composer

# Cria usuário compatível com o host
RUN groupadd -g ${GROUP_ID} laravel \
    && useradd -u ${USER_ID} -g laravel -m laravel

WORKDIR /var/www/html

COPY . .

RUN mkdir -p storage/framework/{sessions,views,cache} bootstrap/cache \
    && chown -R laravel:laravel /var/www/html \
    && chmod -R 775 storage bootstrap/cache

COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

RUN composer install --no-scripts \
    && composer dump-autoload --optimize

USER laravel

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]
