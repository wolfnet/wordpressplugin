#!/usr/bin/env bash

sudo sed -i.bak "s/upload_max_filesize = 2M/upload_max_filesize = 100M/g" /etc/php5/apache2/php.ini