# =============================================
# Inventro — Dockerfile Produção
# PHP 7.4 + Apache + Extensões necessárias
# =============================================
FROM php:7.4-apache

ARG DEBIAN_FRONTEND=noninteractive
ARG APP_ENV=production

# ---- Dependências do sistema ----
RUN apt-get update && apt-get install -y --no-install-recommends \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libssl-dev \
    zip \
    unzip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# ---- Extensões PHP ----
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo \
    pdo_mysql \
    mysqli \
    mbstring \
    exif \
    bcmath \
    gd \
    zip \
    intl \
    opcache \
    && docker-php-ext-enable opcache

# ---- Módulos Apache ----
RUN a2enmod rewrite headers expires deflate

# ---- Permitir .htaccess ----
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# ---- Segurança: esconder versão do Apache ----
RUN echo "ServerTokens Prod" >> /etc/apache2/conf-available/security.conf \
    && echo "ServerSignature Off" >> /etc/apache2/conf-available/security.conf \
    && a2enconf security

# ---- Diretório de trabalho ----
WORKDIR /var/www/html

# ---- Copiar código ----
COPY . /var/www/html

# ---- Permissões seguras (755 dirs, 644 files) ----
RUN chown -R www-data:www-data /var/www/html \
    && find /var/www/html -type d -exec chmod 755 {} \; \
    && find /var/www/html -type f -exec chmod 644 {} \;

# ---- Diretórios que precisam de escrita ----
RUN mkdir -p /var/www/html/application/cache \
    && mkdir -p /var/www/html/application/logs \
    && mkdir -p /var/www/html/admin_assets/uploads \
    && chown -R www-data:www-data /var/www/html/application/cache \
    && chown -R www-data:www-data /var/www/html/application/logs \
    && chown -R www-data:www-data /var/www/html/admin_assets/uploads \
    && chmod -R 775 /var/www/html/application/cache \
    && chmod -R 775 /var/www/html/application/logs \
    && chmod -R 775 /var/www/html/admin_assets/uploads

# ---- Remover arquivos perigosos em produção ----
RUN rm -f /var/www/html/test.php 2>/dev/null; \
    rm -f /var/www/html/error_log 2>/dev/null; \
    true

EXPOSE 80

# ---- Health check nativo ----
HEALTHCHECK --interval=30s --timeout=10s --start-period=40s --retries=3 \
    CMD curl -f http://localhost/ || exit 1

CMD ["apache2-foreground"]
