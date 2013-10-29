#!/bin/bash

runfile=".runonce.wordpress.vagrant"
tempdir="/vagrant/.vagrant/temp"
wpVersion=$1
publicIp=$(/sbin/ifconfig eth1 | grep 'inet addr:' | cut -d: -f2 | awk '{ print $1}')

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
    # rm -f /var/www/wp-config.php
    # cp -fs /vagrant/.vagrant/wp-config.php /var/www/wp-config.php
    rm -f /var/www/.htaccess
    cp -f /vagrant/.vagrant/wp-htaccess /var/www/.htaccess

    echo "Set project directory ..."
    ln -fs /vagrant /var/www/wp-content/plugins/vagrant

    echo "Updating file ownership ..."
    chown -R www-data:www-data /var/www

    echo "Configure mysql ..."
    cat /vagrant/.vagrant/wp-prep.sql | mysql -u root
    # cat /vagrant/.vagrant/wp3.5.1-setup.sql | mysql -u root

    echo "Start apache ..."
    service apache2 start

    echo "Perform WordPress Setup ..."
    curl -s -d "dbname=wordpress&uname=root&pwd=&dbhost=localhost&prefix=wp_" \
        -H "Host:${publicIp}" http://127.0.0.1/wp-admin/setup-config.php?step=2 > /dev/null

    curl -s -H "Host:${publicIp}" http://127.0.0.1/wp-admin/install.php > /dev/null

    curl -s -d "weblog_title=WordPress-VM-${wpVersion}&user_name=admin&admin_password=admin&admin_password2=admin&admin_email=admin@localhost.com&blog_public=1" \
        -H "Host:${publicIp}" http://127.0.0.1/wp-admin/install.php?step=2 > /dev/null

    touch "${runfile}"

fi
