FROM node:22-alpine AS frontend
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .
COPY --from=frontend /app/public/build ./public/build

RUN composer install --no-dev --optimize-autoloader --no-interaction

RUN chmod -R 777 storage bootstrap/cache

EXPOSE 8000

CMD sh -c "php artisan migrate --force && php artisan storage:link --force && php artisan config:cache && php artisan route:cache && php artisan serve --host=0.0.0.0 --port=${PORT:-8000}"
