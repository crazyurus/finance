FROM php:7.4-apache

RUN apt-get update -y && apt-get install -y libfreetype6-dev libjpeg62-turbo-dev
RUN docker-php-ext-install gd mysqli pdo pdo_mysql

COPY . /var/www/html/

RUN echo "ServerName 0.0.0.0" >> $APACHE_CONFDIR/apache2.conf
RUN a2enmod rewrite
RUN service apache2 restart
