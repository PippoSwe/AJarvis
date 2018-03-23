FROM php:5.6-apache

RUN apt-get update

RUN apt-get install -y curl git unzip
RUN cd /tmp/; curl -sS https://getcomposer.org/installer -o composer-setup.php; php -r "if (hash_file('SHA384', 'composer-setup.php') === '544e09ee996cdf60ece3804abc52599c22b1f40f4323403c44d44fdfdd586475ca9813a858088ffbc1f233e9b180f061') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;";
RUN cd /tmp/; php composer-setup.php --install-dir=/usr/local/bin --filename=composer

RUN pecl install xdebug-2.5.4 \
    && docker-php-ext-enable xdebug

RUN docker-php-ext-install mysqli
RUN a2enmod ssl

ADD certs/apache-selfsigned.crt /etc/ssl/certs/ssl-cert-snakeoil.pem
ADD certs/apache-selfsigned.key /etc/ssl/private/ssl-cert-snakeoil.key
RUN a2ensite default-ssl

RUN echo deb http://httpredir.debian.org/debian jessie-backports main non-free >>/etc/apt/sources.list
RUN echo deb-src http://httpredir.debian.org/debian jessie-backports main non-free >>/etc/apt/sources.list
RUN apt-get update && apt-get install -y ffmpeg

RUN a2enmod rewrite
ADD docker/php.addon.ini /usr/local/etc/php/conf.d/

EXPOSE 80
EXPOSE 443
CMD ["apache2-foreground"]