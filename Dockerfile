FROM php:7.1-alpine

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN adduser -D -u 1000 composer

USER composer

VOLUME ["/usr/local/src"]

WORKDIR /usr/local/src

