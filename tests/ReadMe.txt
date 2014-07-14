======== WolfNet IDX for WordPress  =========
Author:             WolfNet Technologies, LLC


===============================
To run unit test on this plugin
===============================

From a command line:
$ cd /path/to/your/git/clone
$ vagrant up wp39
$ vagrant ssh wp39
$ cd /var/www/src/wp-content/plugins/wolfnet-idx-for-wordpress
$ php unit

"wp39" can be replaced by any virtual box set up in the "vagrantfile" file


================================
To run the WordPress unit tests
=================================
$ cd /var/www/
$ sudo phpunit

Since the vagrant user does not own the files in /var/www/ we must run phpunit as root with sudo.


===============
Additional Info
===============

The tests are run using existing WordPress PHPUnit test configuration.

Wordpress testing environment
http://make.wordpress.org/core/handbook/automated-testing/

PHPUnit Documentation
http://phpunit.de/documentation.html

QUnit - javascript unit testing
http://qunitjs.com/

See the phpunit.xml in the base dir of this plugin file and testing-bootstrap.php for configuration details.

The test are in the /tests/ directory. phpunit will run any tests in ".php" file beginning with "test-". 
