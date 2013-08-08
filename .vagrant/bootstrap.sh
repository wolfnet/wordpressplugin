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
        apache2 \
        php5 \
        php5-mysql \
        php5-curl \
        php5-xdebug \
        mysql-server \
        mysql-client \
        > /dev/null 2> /dev/null

    service apache2 stop > /dev/null 2> /dev/null

    echo "Configure apache ..."
    rm -f /etc/apache2/httpd.conf
    ln -sf /vagrant/.vagrant/httpd.conf /etc/apache2/httpd.conf
    rm -f /etc/apache2/sites-enabled/000-default
    ln -sf /vagrant/.vagrant/vhost.conf /etc/apache2/sites-enabled/000-default

    echo "Start apache ..."
    service apache2 start > /dev/null 2> /dev/null

    touch "${runfile}"

fi
