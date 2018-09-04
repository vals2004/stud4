FROM mysql:5.7

MAINTAINER Valeriy Zavolodko <vals2004@gmail.com>

ARG HOST_UID=1000

VOLUME ["/var/lib/mysql"]

RUN usermod -u ${HOST_UID} mysql

# Setup Mysql character to utf-8
#RUN sed -i "/\[mysqld]/a collation-server=utf8_unicode_ci" /etc/mysql/mysql.conf.d/mysqld.cnf
#RUN sed -i "/\[mysqld]/a character-set-server=utf8" /etc/mysql/mysql.conf.d/mysqld.cnf

RUN echo "max_allowed_packet=32M" >> /etc/mysql/conf.d/mysql.cnf

RUN echo Europe/Kiev > /etc/timezone && \
    dpkg-reconfigure -f noninteractive tzdata

EXPOSE 3306
