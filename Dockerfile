FROM docker.io/php:5.6-fpm-alpine

COPY ./wwwroot /var/www

RUN set -exu \
  && addgroup --gid 1101 fishy \
  && adduser \
      --uid 1101 \
      --ingroup fishy \
      --no-create-home \
      --shell /sbin/nologin \
      --disabled-password \
      fishy \
  && chown -R fishy:fishy /var/www

RUN set -exu \
  && docker-php-ext-install mysqli mysql

USER fishy

# Expose port 9000 and start php-fpm server
EXPOSE 9000

WORKDIR /var/www

CMD ["php-fpm"]
