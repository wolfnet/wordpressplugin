#!/bin/bash

runfile=".runonce.vagrant"
tempdir="/vagrant/.vagrant/temp"
certsdir="/vagrant/.vagrant/certs"

if [ ! -f "${runfile}" ]; then

	if [ ! -d $tempdir ]; then
		mkdir -p "${tempdir}"
	fi

	export DEBIAN_FRONTEND=noninteractive

	echo "Updating package list ..."
	apt-get -qq -y update > /dev/null 2> /dev/null
	echo "  done"

	echo "Installing curl ..."
	apt-get -qq -y -o dir::cache::archives="${tempdir}" install curl > /dev/null 2> /dev/null
	echo "  done"

	echo "Installing git ..."
	apt-get -qq -y -o dir::cache::archives="${tempdir}" install git > /dev/null 2> /dev/null
	echo "  done"

	echo "Installing apache2 ..."
	apt-get -qq -y -o dir::cache::archives="${tempdir}" install apache2 > /dev/null 2> /dev/null
	echo "  done"

	echo "Installing php5 and modules ..."
	apt-get -qq -y -o dir::cache::archives="${tempdir}" install php5 php5-mysql php5-curl php5-xdebug > /dev/null 2> /dev/null
	echo "  done"

	echo "Installing mysql server and client ..."
	apt-get -qq -y -o dir::cache::archives="${tempdir}" install mysql-server mysql-client > /dev/null 2> /dev/null
	echo "  done"

	service apache2 stop > /dev/null 2> /dev/null

	echo "Creating SSL ..."
	if [ ! -d $certsdir ]; then
		mkdir -p "${certsdir}"
	fi
	a2enmod ssl > /dev/null
	openssl req -x509 -nodes -days 365 -newkey rsa:2048 -batch \
		-config /vagrant/.vagrant/openssl-cert.config \
		-keyout /vagrant/.vagrant/certs/wolfnet.key \
		-out /vagrant/.vagrant/certs/wolfnet.crt > /dev/null 2> /dev/null
	echo "  done"

	echo "Configuring apache ..."
	cp /vagrant/.vagrant/vagrant-mounted.conf /etc/init/ > /dev/null 2> /dev/null
	rm -f /etc/apache2/httpd.conf
	ln -sf /vagrant/.vagrant/httpd.conf /etc/apache2/httpd.conf
	rm -f /etc/apache2/sites-enabled/000-default
	rm -f /etc/apache2/sites-enabled/000-default.conf
	ln -sf /vagrant/.vagrant/vhost.conf /etc/apache2/sites-enabled/000-default
	ln -sf /vagrant/.vagrant/vhost.conf /etc/apache2/sites-enabled/000-default.conf
	a2enmod rewrite > /dev/null
	echo "  done"

	echo "Configuring php ..."
	# if the development INI file exists use it instead of the default INI file.
	if [ -f /usr/share/php5/php.ini-development ]; then
		rm -f /etc/php5/apache2/php.ini
		cp -f /usr/share/php5/php.ini-development /etc/php5/apache2/php.ini
	fi
	echo "  done"

fi

echo "Restarting apache ..."
service apache2 start > /dev/null 2> /dev/null
echo "  done"

if [ ! -f "${runfile}" ]; then

	echo "Installing phpUnit ..."
	wget -q https://phar.phpunit.de/phpunit.phar > /dev/null
	chmod +x phpunit.phar
	mv phpunit.phar /usr/local/bin/phpunit
	echo "  done"

	touch "${runfile}"

fi
