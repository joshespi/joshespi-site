FROM wordpress:php8.4-apache

COPY ./conf/php.custom.ini /usr/local/etc/php/conf.d/custom.ini

RUN apt-get update \
  && apt-get install -y --no-install-recommends curl less \
  && rm -rf /var/lib/apt/lists/*

RUN curl -fsSL https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar -o /usr/local/bin/wp \
  && chmod +x /usr/local/bin/wp

RUN echo "ServerTokens Prod" >> /etc/apache2/apache2.conf \
  && echo "ServerName localhost" >> /etc/apache2/apache2.conf

RUN a2dissite default-ssl.conf

RUN chown -R www-data:www-data /var/www/html/ \
  && find /var/www/html/ -type d -exec chmod 775 {} \; \
  && find /var/www/html/ -type f -exec chmod 664 {} \; \
  && find /var/www/html/ -type d -exec chmod g+s {} \;

HEALTHCHECK --interval=30s --timeout=5s --start-period=30s --retries=5 \
  CMD curl -fsS http://localhost/wp-login.php || exit 1
