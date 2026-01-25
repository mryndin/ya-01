# Temperature API Dockerfile
FROM php:8.2-alpine AS builder

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Копирование только composer файлов
COPY composer.json composer.lock* ./

# Установка зависимостей
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Финальный образ
FROM php:8.2-alpine

# Мета информация
LABEL maintainer="Smart Home Team <dev@smarthome.local>"
LABEL version="1.0.0"
LABEL description="Temperature API for Smart Home System"

# Установка системных зависимостей
RUN apk add --no-cache \
    bash \
    curl \
    tzdata

# Настройка временной зоны
ENV TZ=Europe/Moscow
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# Создание рабочей директории
WORKDIR /var/www/html

# Копирование зависимостей из builder stage
COPY --from=builder /app/vendor ./vendor

# Копирование исходного кода
COPY . .

# Установка прав доступа
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Экспорт порта
EXPOSE 8081

# Переменные окружения
ENV APP_ENV=production
ENV APP_DEBUG=false
ENV PORT=8081

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD curl -f http://localhost:8081/health || exit 1

# Команда запуска
CMD ["php", "-S", "0.0.0.0:8081", "-t", "public"]