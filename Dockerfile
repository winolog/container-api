FROM php:8.3-cli

# Установка зависимостей
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Установка Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Установка необходимых PHP расширений
RUN docker-php-ext-install pdo_mysql