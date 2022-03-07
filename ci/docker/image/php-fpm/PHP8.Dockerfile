FROM php:8.0.9-fpm-buster

RUN apt-get update -y \
    && apt-get install -y  \
        git \
        zip \
        libxml2-dev \
        gettext-base \
        telnet \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:1 /usr/bin/composer /usr/bin/composer

RUN docker-php-ext-install pdo pdo_mysql soap pcntl bcmath sockets

RUN yes "" | pecl install apcu \
    && echo "extension=apcu.so" > /usr/local/etc/php/conf.d/ext-apcu.ini

# @TODO: either create separate git user and/or create Kubernetes secret for this key:
# Add the keys, known_hosts, set permissions
RUN mkdir -p /root/.ssh \
    && chmod 0600 /root/.ssh \
    && ssh-keyscan -t rsa "stash.dev.opticplanet.net,bitbucket.ecentria.tools,172.16.16.58" > /root/.ssh/known_hosts

ADD ssh/id_rsa /root/.ssh/id_rsa
RUN chmod 600 /root/.ssh/id_rsa
