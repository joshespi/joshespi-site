FROM php:8.5-fpm

# Node stays in this image even though the separate node container is gone:
# public/build is gitignored and nothing else produces the Vite manifest, so
# entrypoint.sh runs "npm run build" against the bind-mounted source.
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && curl -fsSL https://deb.nodesource.com/setup_24.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Node deps install at build time, but deliberately outside /var/www/html: that
# path is a bind mount at runtime, so anything installed there during the build
# is shadowed the moment the container starts. entrypoint.sh links these in when
# package-lock.json still matches the hash recorded here, which keeps a 54MB
# npm ci off the deploy path and out of the host source tree.
COPY src/package.json src/package-lock.json /opt/node-deps/
RUN cd /opt/node-deps \
    && npm ci --no-audit --no-fund \
    && md5sum package-lock.json | cut -d' ' -f1 > .lock-hash

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]
