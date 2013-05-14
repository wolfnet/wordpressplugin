#!/usr/bin/env bash
cd /var
wget -q http://wordpress.org/latest.zip
unzip -qq latest.zip
chown www-data /var/wordpress
rm -rf /var/www
ln -fs /var/wordpress /var/www
ln -fs /vagrant /var/wordpress/wp-content/plugins/vagrant
cd -
