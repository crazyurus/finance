FROM php:5.6-apache

COPY . /var/www/html/

RUN echo "ServerName 0.0.0.0" >> $APACHE_CONFDIR/apache2.conf
RUN a2enmod rewrite
RUN service apache2 restart
