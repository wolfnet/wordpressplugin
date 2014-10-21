#!/bin/bash

runfile=".runonce.vagrant"
tempdir="/vagrant/.vagrant/temp"

if [ ! -f "${runfile}" ]; then

    if [ ! -d $tempdir ]; then
        mkdir -p "${tempdir}"
    fi

    export DEBIAN_FRONTEND=noninteractive

    echo "Downloading required software ..."
    apt-get -qq update > /dev/null 2> /dev/null
    apt-get -qq -y -o dir::cache::archives="${tempdir}" install \
        curl \
        git \
        apache2 \
        php5 \
        php5-mysql \
        php5-curl \
        php5-xdebug \
        mysql-server \
        mysql-client \
        > /dev/null 2> /dev/null

    service apache2 stop > /dev/null 2> /dev/null

    echo "Creating SSL ..."
    a2enmod ssl > /dev/null
    openssl req -x509 -nodes -days 365 -newkey rsa:2048 -batch \
        -config /vagrant/.vagrant/openssl-cert.config \
        -keyout /vagrant/.vagrant/certs/wolfnet.key \
        -out /vagrant/.vagrant/certs/wolfnet.crt > /dev/null 2> /dev/null

    echo "Configure apache ..."
    rm -f /etc/apache2/httpd.conf
    ln -sf /vagrant/.vagrant/httpd.conf /etc/apache2/httpd.conf
    rm -f /etc/apache2/sites-enabled/000-default
    ln -sf /vagrant/.vagrant/vhost.conf /etc/apache2/sites-enabled/000-default
    a2enmod rewrite > /dev/null

    echo "Start apache ..."
    service apache2 start > /dev/null 2> /dev/null

    echo "Installing phpUnit ..."
    wget -q https://phar.phpunit.de/phpunit.phar > /dev/null
    chmod +x phpunit.phar
    mv phpunit.phar /usr/local/bin/phpunit

    touch "${runfile}"

fi
