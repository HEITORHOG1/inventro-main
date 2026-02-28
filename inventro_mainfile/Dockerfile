# Imagem base PHP 7.4 com Apache
FROM php:7.4-apache

# Argumentos de build
ARG DEBIAN_FRONTEND=noninteractive

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    zip \
    unzip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Instalar extensões PHP necessárias para CodeIgniter
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo \
    pdo_mysql \
    mysqli \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    intl

# Habilitar mod_rewrite do Apache (necessário para CodeIgniter)
RUN a2enmod rewrite headers

# Configurar o Apache para permitir .htaccess
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Definir diretório de trabalho
WORKDIR /var/www/html

# Copiar arquivos do projeto
COPY . /var/www/html

# Ajustar permissões
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Criar diretórios necessários e ajustar permissões
RUN mkdir -p /var/www/html/application/cache \
    && mkdir -p /var/www/html/application/logs \
    && mkdir -p /var/www/html/admin_assets/uploads \
    && chown -R www-data:www-data /var/www/html/application/cache \
    && chown -R www-data:www-data /var/www/html/application/logs \
    && chown -R www-data:www-data /var/www/html/admin_assets/uploads \
    && chmod -R 777 /var/www/html/application/cache \
    && chmod -R 777 /var/www/html/application/logs \
    && chmod -R 777 /var/www/html/admin_assets/uploads

# Expor porta 80
EXPOSE 80

# Comando padrão
CMD ["apache2-foreground"]
