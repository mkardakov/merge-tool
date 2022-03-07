FROM php:8.0.9-fpm-buster

RUN curl -fsSL https://deb.nodesource.com/setup_17.x | bash -

RUN apt-get update -y \
    && apt-get install -y  \
        git \
        zip \
        libxml2-dev \
        gettext-base \
        telnet \
        nodejs \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*


COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN docker-php-ext-install pdo pdo_mysql soap pcntl bcmath sockets

RUN yes "" | pecl install apcu \
    && echo "extension=apcu.so" > /usr/local/etc/php/conf.d/ext-apcu.ini

RUN echo "Install php-xdebug" \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# @TODO: either create separate git user and/or create Kubernetes secret for this key:
# Add the keys, known_hosts, set permissions
RUN mkdir -p /root/.ssh \
    && chmod 0600 /root/.ssh \
    && ssh-keyscan -t rsa "stash.dev.opticplanet.net,bitbucket.ecentria.tools,172.16.16.58" > /root/.ssh/known_hosts

ADD ssh/id_rsa /root/.ssh/id_rsa
RUN chmod 600 /root/.ssh/id_rsa
