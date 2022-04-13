FROM php:8.1-fpm
RUN apt-get update -y && apt-get install -y libpng-dev && apt-get install -y libjpeg-dev
RUN docker-php-ext-install mysqli
RUN docker-php-ext-configure gd \
            --prefix=/usr \
            --with-jpeg
RUN docker-php-ext-install gd