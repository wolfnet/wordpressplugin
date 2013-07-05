#!/bin/bash

runfile=".runonce.vagrant"
tempdir="/vagrant/.vagrant/temp"

if [ ! -f "${runfile}" ]; then

    if [ ! -d $tempdir ]; then
        mkdir -p "${tempdir}"
    fi

    echo "Downloading required software ..."

    export DEBIAN_FRONTEND=noninteractive

    apt-get -qq update
    apt-get -qq -y install apache2 php5 mysql-server mysql-client php5-mysql

    service apache2 stop

    if [ ! -f "${tempdir}/wordpress-3.5.1.tar.gz" ]; then
        echo "Downloading wordpress code ..."
        wget -qO "${tempdir}/wordpress-3.5.1.tar.gz" http://wordpress.org/wordpress-3.5.1.tar.gz
    fi

    echo "Installing wordpress ..."
    rm -rf /var/www/*
    tar -xzf "${tempdir}/wordpress-3.5.1.tar.gz" -C /var/www --strip-components 1

    echo "Configure wordpress ..."
    rm -f /var/www/wp-config.php
    ln -fs /vagrant/.vagrant/wp-config.php /var/www/wp-config.php
    rm -f /var/www/.htaccess
    cp -f /vagrant/.vagrant/wp-htaccess /var/www/.htaccess

    echo "Set project directory ..."
    cp /vagrant/.vagrant/wp-htaccess /var/www/.htaccess
    ln -fs /vagrant /var/www/wp-content/plugins/vagrant

    echo "Configure mysql ..."
    cat /vagrant/.vagrant/wp-prep.sql | mysql -u root
    cat /vagrant/.vagrant/wp3.5.1-setup.sql | mysql -u root

    echo "Configure php ..."
    ln -fs /vagrant/.vagrant/php.ini /var/www/php.ini

    echo "Configure apache ..."
    cp -f /vagrant/.vagrant/httpd.conf /etc/apache2/httpd.conf
    cp -f /vagrant/.vagrant/vhost.conf /etc/apache2/sites-enabled/000-default

    echo "Start apache ..."
    service apache2 start

    touch "${runfile}"

fi
