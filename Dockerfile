FROM debian:bookworm-slim

RUN apt-get update
RUN apt-get upgrade -y
RUN apt-get install curl git zip dos2unix -y
RUN apt-get install php php-mysql php-xdebug php-curl php-zip php-xml php-mbstring -y
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer
COPY ./config/xdebug.ini /etc/php/8.2/mods-available/
COPY ./config/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY ./config/startup.sh /startup.sh
RUN dos2unix /startup.sh
RUN chmod +x /startup.sh

CMD /startup.sh
