FROM php:7.3-apache
ARG TIMEZONE=Europe/Paris
ARG APP_ENV=dev
ENV APACHE_ROOT=/var/www/html/
ENV APACHE_DOCUMENT_ROOT=${APACHE_ROOT}public
ENV LOG_PATH=${APACHE_ROOT}var/log
ENV CACHE_PATH=${APACHE_ROOT}var/cache
ENV VENDOR_PATH=${APACHE_ROOT}vendor
ENV COMPOSER_AUTH='{"github-oauth": {"github.com": ""}}'
ENV COMPOSER_VENDOR_DIR=${VENDOR_PATH}

RUN apt-get update && apt-get install -y \
        openssl \
        unzip \
        libicu-dev \
        libzip-dev \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
        libpq-dev \
        zlib1g-dev \
        mariadb-client \
        gnupg2 \
        libxml2-dev \
        wget \
        nano \
        libc-client-dev \
        libkrb5-dev

RUN rm -r /var/lib/apt/lists/*

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer --version=1.10.19 \
    && composer --version

# Node js
RUN curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key | gpg --dearmor -o /etc/apt/trusted.gpg.d/nodesource.gpg \
    && NODE_MAJOR=18 \
    && echo "deb [signed-by=/etc/apt/trusted.gpg.d/nodesource.gpg] https://deb.nodesource.com/node_$NODE_MAJOR.x nodistro main" | tee /etc/apt/sources.list.d/nodesource.list \
    && apt-get update && apt-get install nodejs -y \
    && rm -rf /var/lib/apt/lists/*

# Yarn
RUN curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add - >/dev/null \
    && echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list \
    && apt-get update && apt-get install -y yarn \
    && rm -rf /var/lib/apt/lists/*

# Type docker-php-ext-install to see available extensions
RUN docker-php-ext-install pdo pdo_mysql intl opcache mbstring zip \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ && docker-php-ext-install gd

# Set timezone
RUN ln -snf /usr/share/zoneinfo/${TIMEZONE} /etc/localtime && echo ${TIMEZONE} > /etc/timezone \
    && printf '[PHP]\ndate.timezone = "%s"\n', ${TIMEZONE} > /usr/local/etc/php/conf.d/tzone.ini \
    && "date"

RUN a2enmod rewrite
COPY ./docker/php7-apache/conf.d/symfony-${APP_ENV}.ini /usr/local/etc/php/conf.d/symfony.ini
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
    && sed -i '/<Directory ${APACHE_DOCUMENT_ROOT}>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf \
    && sed -ri 's!AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript!AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/json!g' /etc/apache2/mods-available/deflate.conf

RUN echo 'alias lla="ls -la"' >> ~/.bashrc \
    && echo 'alias ll="ls -l"' >> ~/.bashrc

# Add installation file
COPY ./application/package.json ./application/yarn.lock ${APACHE_ROOT}
ENV NODE_OPTIONS=--openssl-legacy-provider
RUN yarn install

# Add source code in webserver root dir
COPY ./application/ ${APACHE_ROOT}

WORKDIR ${APACHE_ROOT}

# Build application
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN COMPOSER_MEMORY_LIMIT=-1 composer install --optimize-autoloader --prefer-dist --no-interaction --no-cache \
    && php bin/console assets:install --symlink public

RUN if [ "$APP_ENV" = "preprod" -o "$APP_ENV" = "prod" ]; then \
    yarn ${APP_ENV} \
    && mkdir -p ${LOG_PATH} \
    && sed -ri -e 's!\$\{APACHE_LOG_DIR\}!${LOG_PATH}!g' /etc/apache2/sites-available/000-default.conf; \
  fi

RUN chmod a+x chmod.sh