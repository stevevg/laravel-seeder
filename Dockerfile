FROM php:7.1-alpine

# Install Composer and non-root user
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && adduser -D -u 1000 composer

USER composer

VOLUME ["/usr/local/src"]

WORKDIR /usr/local/src