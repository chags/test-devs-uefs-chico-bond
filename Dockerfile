# Imagem oficial do PHP 8.4 com FPM
FROM php:8.4-fpm

# Variáveis de ambiente
ENV COMPOSER_ALLOW_SUPERUSER=1 \
    APP_ENV=production \
    HOME=/var/www/html \
    XDG_CONFIG_HOME=/var/www/html/.config \
    PSYSH_CONFIG_DIR=/var/www/html/.config/psysh

# Instala dependências do sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    sqlite3 \
    libsqlite3-dev \
    libzip-dev \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        pdo_sqlite \
        mbstring \
        bcmath \
        exif \
        pcntl \
        gd \
        zip

# Cria diretórios com permissões corretas
RUN mkdir -p /var/www/html/storage/framework/{sessions,views,cache} \
    && mkdir -p /var/www/html/bootstrap/cache \
    && mkdir -p $XDG_CONFIG_HOME/psysh \
    && chown -R www-data:www-data /var/www/html $XDG_CONFIG_HOME

# Define o diretório de trabalho
WORKDIR /var/www/html

# Copia o código do Laravel para dentro do container
COPY . .


# Instala dependências do Laravel (modo produção) apenas se 'vendor' não existir
RUN if [ ! -d vendor ]; then \
        composer install --optimize-autoloader --no-dev; \
    fi


# Expõe a porta do PHP-FPM
EXPOSE 9000

# Comando padrão
CMD ["php-fpm"]
