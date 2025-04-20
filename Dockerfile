FROM php:8.4-fpm

ENV COMPOSER_ALLOW_SUPERUSER=1 \
    APP_ENV=local \
    HOME=/var/www/html \
    XDG_CONFIG_HOME=/var/www/html/.config \
    PSYSH_CONFIG_DIR=/var/www/html/.config/psysh

# 1) Instala deps de SO
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip \
    sqlite3 libsqlite3-dev libzip-dev bash \
    default-mysql-client \
    netcat-openbsd \
  && docker-php-ext-install pdo pdo_mysql pdo_sqlite mbstring \
     bcmath exif pcntl gd zip \
  && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2) Node & Composer
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
  && apt-get install -y nodejs \
  && npm install -g npm@latest \
  && curl -sS https://getcomposer.org/installer \
     | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html

# 3) Copia o entrypoint primeiro
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# 4) Copia o resto do código
COPY . .

# 5) Instala dependências
RUN composer install --no-scripts \
    && composer dump-autoload --optimize

# 6) Garante que os dirs existam e ajusta permissão
RUN mkdir -p storage/framework/{sessions,views,cache} bootstrap/cache \
    && chmod -R 777 storage bootstrap/cache

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]