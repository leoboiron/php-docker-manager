FROM php:8.4-cli

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN apt-get update && apt-get install -y git unzip
RUN apt-get install -y libzip-dev
RUN docker-php-ext-install zip

# Set working directory
WORKDIR /app

# Copy all files
COPY . .

# Install dependencies
RUN composer install --no-scripts --no-autoloader
