FROM php:5.6-fpm-stretch

WORKDIR /var/www

RUN docker-php-ext-install mysqli mysql

RUN useradd fishy
USER fishy
COPY --chown=fishy:fishy . /var/www

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
