#!/bin/bash

runfile=".runonce.wordpress.vagrant"
tempdir="/vagrant/.vagrant/temp"
wpVersion=$1
publicIp=$(/sbin/ifconfig eth1 | grep 'inet addr:' | cut -d: -f2 | awk '{ print $1}')

if [ ! -f "${runfile}" ]; then

	echo "Installing WordPress ..."
    rm -rf /var/www/*
    git clone --depth 1 --branch $wpVersion git://develop.git.wordpress.org/ /var/www > /dev/null
	echo "  done"

	echo "Configuring WordPress ..."
    # rm -f /var/www/wp-config.php
    # cp -fs /vagrant/.vagrant/wp-config.php /var/www/wp-config.php
    rm -f /var/www/src/.htaccess
    cp -f /vagrant/.vagrant/wp-htaccess /var/www/src/.htaccess
    export WP_DEVELOP_DIR=/var/www
    cp -f /vagrant/.vagrant/wp-tests-config.php /var/www/wp-tests-config.php
	echo "  done"

	echo "Setting project directory: /var/www/src/wp-content/plugins/wolfnet-idx-for-wordpress"
    ln -fs "/wolfnet-idx-for-wordpress" "/var/www/src/wp-content/plugins/wolfnet-idx-for-wordpress"

	echo "Updating file ownership of /var/www"
    chown -R www-data:www-data /var/www

	echo "Configuring mysql ..."
    cat /vagrant/.vagrant/wp-prep.sql | mysql -u root
    # cat /vagrant/.vagrant/wp3.5.1-setup.sql | mysql -u root
	echo "  done"

	echo "Removing default themes ..."
	rm -rf /var/www/src/wp-content/themes/twentyten
	rm -rf /var/www/src/wp-content/themes/twentyeleven
	rm -rf /var/www/src/wp-content/themes/twentytwelve
	rm -rf /var/www/src/wp-content/themes/twentythirteen
	rm -rf /var/www/src/wp-content/themes/twentyfourteen
	rm -rf /var/www/src/wp-content/themes/twentyfifteen
	rm -rf /var/www/src/wp-content/themes/twentysixteen
	echo "  done"

	echo "Installing BrandCo themes ..."
	unzip -qo /vagrant/.vagrant/themes/wolfnetresponsive.zip -d /var/www/src/wp-content/themes
	unzip -qo /vagrant/.vagrant/themes/wolfnetresponsivSKIN1.zip -d /var/www/src/wp-content/themes
	unzip -qo /vagrant/.vagrant/themes/wolfnetresponsivSKIN2.zip -d /var/www/src/wp-content/themes
	unzip -qo /vagrant/.vagrant/themes/wolfnet-skin-4.zip -d /var/www/src/wp-content/themes
	unzip -qo /vagrant/.vagrant/themes/wolfpressBLACK.zip -d /var/www/src/wp-content/themes
	unzip -qo /vagrant/.vagrant/themes/wolfpressDARK.zip -d /var/www/src/wp-content/themes
	unzip -qo /vagrant/.vagrant/themes/wolfpressGREY.zip -d /var/www/src/wp-content/themes
	unzip -qo /vagrant/.vagrant/themes/wolfpressRED.zip -d /var/www/src/wp-content/themes
	unzip -qo /vagrant/.vagrant/themes/wolfpresstheme.zip -d /var/www/src/wp-content/themes
	unzip -qo /vagrant/.vagrant/themes/wolfpressWHITE.zip -d /var/www/src/wp-content/themes
	echo "  done"

	echo "Starting apache ..."
    service apache2 start
	echo "  done"

	echo "Performing WordPress Setup ..."

    curl -s -d "dbname=wordpress&uname=root&pwd=&dbhost=localhost&prefix=wp_" \
        -H "Host:${publicIp}" http://127.0.0.1/wp-admin/setup-config.php?step=2 > /dev/null

    curl -s -H "Host:${publicIp}" http://127.0.0.1/wp-admin/install.php > /dev/null

    curl -s -d "weblog_title=WordPress-VM-${wpVersion}&user_name=admin&admin_password=admin&admin_password2=admin&admin_email=admin@localhost.com&blog_public=1" \
        -H "Host:${publicIp}" http://127.0.0.1/wp-admin/install.php?step=2 > /dev/null

    # Enable WordPress Debug mode - here because the config file does exist until this point.
    sed -i "s/define('WP_DEBUG', false);/define('WP_DEBUG', true);/g" /var/www/wp-config.php

	echo "  done"

    touch "${runfile}"

fi
