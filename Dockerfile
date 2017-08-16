FROM ubuntu:14.04
MAINTAINER Jeff Claud <jefferson.claud@nuworks.ph>

RUN apt-get update -y
RUN apt-get install -y software-properties-common
RUN apt-get install -y python-software-properties
RUN LC_ALL=C.UTF-8 add-apt-repository ppa:ondrej/php

RUN apt-get update -y
RUN apt-get install -y nginx php7.0
RUN apt-get update -y
RUN apt-get install -y php7.0-mbstring php7.0-mcrypt php7.0-tokenizer php7.0-curl php7.0-dom zip vim nano php7.0-fpm php7.0-gd php7.0-mysql wkhtmltopdf libxrender1 php7.0-imap git
RUN apt-get install cron
RUN echo "\ndaemon off;" >> /etc/nginx/vhost-nginx.conf
RUN sed -i -e "s/;\?daemonize\s*=\s*yes/daemonize = no/g" /etc/php/7.0/fpm/php-fpm.conf
RUN echo "\ncgi.fix_pathinfo=0" >> /etc/php/7.0/fpm/php.ini
# Nginx config
RUN rm /etc/nginx/sites-enabled/default
ADD ./vhost-nginx.conf /etc/nginx/sites-available/
RUN ln -s /etc/nginx/sites-available/vhost-nginx.conf /etc/nginx/sites-enabled/vhost-nginx

# WEB SERVER ENVIRONMENT
ENV RUN_USER www-data
ENV RUN_GROUP www-data

# PHP config
#RUN sed -i -e "s/;\?date.timezone\s*=\s*.*/date.timezone = Europe\/Kiev/g" /etc/php/fpm/php.ini

# Expose ports.
EXPOSE 80


# Copy this repo into place.
#ADD . /var/www/vhosts/live/iris-backend

# Define default command.
CMD sudo service php7.0-fpm start && nginx
CMD cron && tail -f /var/log/cron.log
