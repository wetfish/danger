FROM docker.io/php:8.5.12-fpm-alpine

# mysqli for the database
RUN docker-php-ext-install mysqli

# php's production defaults, plus danger's own settings
RUN cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY ./php.ini "$PHP_INI_DIR/conf.d/danger.ini"

# php only needs the php files, nginx serves everything else
COPY ./public_html/*.php /var/www/

RUN set -exu \
  && addgroup --gid 1101 fishy \
  && adduser \
      --uid 1101 \
      --ingroup fishy \
      --no-create-home \
      --shell /sbin/nologin \
      --disabled-password \
      fishy \
  && chown -R fishy:fishy /var/www \
  && chmod -R 755 /var/www

USER fishy

# Expose port 9000 and start php-fpm server
EXPOSE 9000

WORKDIR /var/www

CMD ["php-fpm"]
