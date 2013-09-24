#!/bin/bash

runfile=".runonce.wordpress.vagrant"
tempdir="/vagrant/.vagrant/temp"
# wpVersion="3.5.2"
wpVersion="3.6.1"

if [ ! -f "${runfile}" ]; then

    if [ ! -d $tempdir ]; then
        mkdir -p "${tempdir}"
    fi

    if [ ! -f "${tempdir}/wordpress-${wpVersion}.tar.gz" ]; then
        echo "Downloading wordpress code ..."
        wget -qO "${tempdir}/wordpress-${wpVersion}.tar.gz" "http://wordpress.org/wordpress-${wpVersion}.tar.gz"
    fi

    echo "Installing wordpress ..."
    rm -rf /var/www/*
    tar -xzf "${tempdir}/wordpress-${wpVersion}.tar.gz" -C /var/www --strip-components 1

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

    echo "Updating file ownership ..."
    chown -R www-data:www-data /var/www

    echo "Start apache ..."
    service apache2 start

    touch "${runfile}"

fi
