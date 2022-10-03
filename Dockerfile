FROM php:7.4-fpm

COPY composer.json /var/www/


# Set working directory
WORKDIR /var/www

# Arguments defined in docker-compose.yml
ARG user
ARG uid

# Install dependencies for the operating system software
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
    libz-dev \
    unzip \
    git \
    libonig-dev \
    curl  \
    zlib1g-dev \
    libicu-dev \
    g++ 

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*


# Install PHP extensions
RUN docker-php-ext-install pdo mysqli pdo_mysql mbstring exif zip pcntl bcmath intl gd
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-configure intl 
RUN docker-php-ext-install gd
RUN docker-php-ext-enable intl

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer


# Create system user to run Composer 
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# Install Composer package
RUN composer install 

# Copy existing application directory contents
COPY . /var/www

USER $user

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
