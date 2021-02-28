FROM php:7.2-apache 

ARG HOST_UID
ARG OAUTH_KEY
ARG PASSWORD

VOLUME ["/var/www/html"]

ENV DEBIAN_FRONTEND noninteractive

RUN apt-get update && apt-get install -y apt-utils
RUN apt-get install -y \
        sudo \
        zlib1g-dev \
        libicu-dev \
        libpq-dev \
        git \
        nano \
        zip \
        mcrypt \
        vim \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libmcrypt-dev \
        #libpng12-dev \
        #mysql-client \
    && docker-php-ext-install -j$(nproc) iconv \
    #&& docker-php-ext-configure mcrypt \
    #&& docker-php-ext-install -j$(nproc) mcrypt \
    && docker-php-ext-install mysqli \
    && docker-php-ext-configure mbstring \
    && docker-php-ext-install -j$(nproc) mbstring \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install intl zip opcache pdo_mysql \
    && docker-php-ext-configure calendar --enable-calendar \
    && docker-php-ext-install -j$(nproc) calendar 

RUN echo Europe/Kiev > /etc/timezone && \
    dpkg-reconfigure -f noninteractive tzdata

RUN mkdir /etc/apache2/ssl
COPY ./apache.crt /etc/apache2/ssl/apache.crt
COPY ./apache.key /etc/apache2/ssl/apache.key
COPY ./virtualhost.conf /etc/apache2/sites-available/000-default.conf
COPY ./virtualhost-ssl.conf /etc/apache2/sites-available/000-default-ssl.conf
#COPY ./php.ini /usr/local/etc/php/
RUN echo "LogFormat \"%a %l %u %t \\\"%r\\\" %>s %O \\\"%{User-Agent}i\\\"\" mainlog" >> /etc/apache2/apache2.conf
RUN a2enmod rewrite remoteip ssl
RUN a2ensite 000-default.conf
RUN a2ensite 000-default-ssl.conf
#RUN apt-get install -y python-certbot-apache 
#RUN certbot --authenticator webroot --installer apache -d stud.ddns.net

RUN set -x && curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer 

RUN usermod -u ${HOST_UID} www-data
RUN groupmod -g ${HOST_UID} www-data

RUN chsh -s /bin/bash www-data
RUN chown -R www-data:www-data /var/www
RUN sudo -u www-data composer config --global github-oauth.github.com "${OAUTH_KEY}"

# install node.js & yarn
RUN apt-get install -y gnupg2
RUN curl -sL https://deb.nodesource.com/setup_8.x | bash -
RUN apt-get install -y nodejs
RUN curl -sS http://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add -
RUN echo "deb http://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list
RUN apt-get update && apt-get install -y yarn

# install phpMyAdmin
RUN mkdir /var/www/phpmyadmin \
    && curl https://files.phpmyadmin.net/phpMyAdmin/4.7.0/phpMyAdmin-4.7.0-english.tar.gz \
    | tar -xz -C /var/www/phpmyadmin --strip-components=1
RUN cat /var/www/phpmyadmin/config.sample.inc.php | sed -e 's/localhost/db/' > /var/www/phpmyadmin/config.inc.php 

EXPOSE 80
EXPOSE 443
