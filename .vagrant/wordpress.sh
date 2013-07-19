#!/bin/bash

runfile=".runonce.wordpress.vagrant"
tempdir="/vagrant/.vagrant/temp"

if [ ! -f "${runfile}" ]; then

    if [ ! -d $tempdir ]; then
        mkdir -p "${tempdir}"
    fi

    if [ ! -f "${tempdir}/wordpress-3.5.2.tar.gz" ]; then
        echo "Downloading wordpress code ..."
        wget -qO "${tempdir}/wordpress-3.5.2.tar.gz" http://wordpress.org/wordpress-3.5.2.tar.gz
    fi

    echo "Installing wordpress ..."
    rm -rf /var/www/*
    tar -xzf "${tempdir}/wordpress-3.5.2.tar.gz" -C /var/www --strip-components 1

    echo "Configure wordpress ..."
    rm -f /var/www/wp-config.php
    ln -fs /vagrant/.vagrant/wp-config.php /var/www/wp-config.php
    rm -f /var/www/.htaccess
    cp -f /vagrant/.vagrant/wp-htaccess /var/www/.htaccess

    echo "Set project directory ..."
    ln -fs /vagrant /var/www/wp-content/plugins/vagrant

    echo "Configure mysql ..."
    cat /vagrant/.vagrant/wp-prep.sql | mysql -u root
    cat /vagrant/.vagrant/wp3.5.1-setup.sql | mysql -u root

    echo "Start apache ..."
    service apache2 start

    touch "${runfile}"

fi
