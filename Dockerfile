FROM php:7.3-cli AS builder

# install composer to /composer.phar
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php -r "if (hash_file('sha384', 'composer-setup.php') === 'a5c698ffe4b8e849a443b120cd5ba38043260d5c4023dbf93e1558871f1f07f58274fc6f4c93bcfd858c6bd0775cd8d1') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
RUN php composer-setup.php

RUN apt-get update && DEBIAN_FRONTEND=noninteractive apt-get install -yq \
    git \
    unzip \
    yarnpkg

# copy app sources
COPY / /app
WORKDIR /app

RUN php /composer.phar install

RUN yarnpkg install
RUN yarnpkg build

#------------------------------------------------------------------------------

FROM php:7.3-apache

ENV APACHE_DOCUMENT_ROOT /app/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN sed -ri -e 's!Options Indexes FollowSymLinks!FallbackResource /index.php!' /etc/apache2/apache2.conf

COPY --from=builder /app /app
WORKDIR /app

RUN bin/console doctrine:database:create -n && \
    bin/console doctrine:schema:create -n && \
    bin/console doctrine:fixtures:load -n

ENV APP_ENV=prod
RUN bin/console cache:warmup -n
