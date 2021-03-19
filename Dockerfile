FROM thecodingmachine/php:7.4-v4-apache

ENV APACHE_DOCUMENT_ROOT=/var/www/public

#COPY ./composer.json /var/www
#COPY ./composer.lock /var/www

#WORKDIR /var/www

#RUN composer install