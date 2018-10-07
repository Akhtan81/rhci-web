FROM php:7.2-apache

MAINTAINER Gram <gram7gram@gmail.com>

ENV APACHE_LOCK_DIR /var/lock/apache2
ENV APACHE_RUN_DIR /var/run/apache2
ENV APACHE_PID_FILE ${APACHE_RUN_DIR}/apache2.pid
ENV APACHE_LOG_DIR /var/log/apache2
ENV APACHE_RUN_USER www-data
ENV APACHE_RUN_GROUP www-data
ENV APACHE_MAX_REQUEST_WORKERS 32
ENV APACHE_MAX_CONNECTIONS_PER_CHILD 1024
ENV APACHE_ALLOW_OVERRIDE None
ENV APACHE_ALLOW_ENCODED_SLASHES Off

ENV DEBIAN_FRONTEND noninteractive
ENV LANG C
ENV TIME_ZONE UTC

RUN apt-get update && apt-get install -y libpq-dev zlib1g-dev git curl zip unzip cron \
    && apt-get clean -y && apt-get autoclean -y && apt-get autoremove -y \
	&& rm -rf /var/lib/{apt,dpkg,cache,log}/ && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure pgsql --with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install -j$(nproc) pgsql pdo_pgsql zip

RUN a2enmod rewrite && a2enmod ssl

COPY ./php/php.ini /usr/local/etc/php/conf.d/php.ini

COPY ./php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

COPY ./apache2/http.conf /etc/apache2/sites-available/000-default.conf

RUN a2ensite 000-default

COPY ./apache2/https.conf /etc/apache2/sites-available/default-ssl.conf

RUN a2ensite default-ssl

EXPOSE 443

COPY ./cron/refresh-access-tokens.conf /etc/cron.d/10-refresh-access-tokens.conf

RUN chmod 644 /etc/cron.d/10-refresh-access-tokens.conf

RUN crontab /etc/cron.d/10-refresh-access-tokens.conf

RUN service cron start

CMD apache2 -DFOREGROUND
