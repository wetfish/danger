#
# runtime container
FROM docker.io/debian:bookworm-slim

# install some packages
RUN set -exu \
  && DEBIAN_FRONTEND=noninteractive apt-get -yq update \
  && DEBIAN_FRONTEND=noninteractive apt-get -yq install \
    libjpeg-dev \
    libpng-dev \
    libfreetype6-dev \
    software-properties-common \
    ca-certificates \
    lsb-release \
    apt-transport-https \
    curl

# setup php5.6 repo
RUN set -exu \
  && echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/php.list \
  && curl -fsSL https://packages.sury.org/php/apt.gpg | apt-key add -

# install php5.6, some extensions, and nodejs
RUN set -exu \
  && DEBIAN_FRONTEND=noninteractive apt-get -yq update \
  && DEBIAN_FRONTEND=noninteractive apt-get -yq install \
    php5.6 \
    php5.6-fpm \
    php5.6-mysqli \
    php5.6-mysql


# clean apt caches
RUN set -exu \
  && DEBIAN_FRONTEND=noninteractive apt-get -yq clean

COPY ./wwwroot /var/www

# back to root
USER root

WORKDIR /var/www

RUN set -exu \
  && chown -R www-data:www-data /var/www

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["/usr/sbin/php-fpm5.6", "--nodaemonize", "--force-stderr"]
