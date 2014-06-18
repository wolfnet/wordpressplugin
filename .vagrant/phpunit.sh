#!/bin/bash

cd /var/www/src/wp-content/plugins/wolfnet-idx-for-wordpress
export WP_DEVELOP_DIR=/var/www
phpunit
