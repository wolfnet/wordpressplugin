<?php
/**
 * Bootstrap the plugin unit testing environment.
 *
 * Edit 'active_plugins' setting below to point to your main plugin file.
 *
 * @package wordpress-plugin-tests
 */

// Activates this plugin in WordPress so it can be tested.
$GLOBALS['wp_tests_options'] = array(
    'active_plugins'     => array( 'wolfnet-idx-for-wordpress/wolfnet.php' ),
);

// TODO
// get a 2nd good Key. currently good1 and good2 are the same key

$GLOBALS['wnt_tests_options'] = array(
    'api_key_good1'  => 'wp_8f0d2c30ddb3b3ac5b5df4c3d4fad11b',
    'api_key_good2'  => 'wp_e30d1090d28bf1b863d77bfea17891ef',
    'api_key_bad'    => 'wp_d1bf90d8bf9046d1d1c43f1fad34ec7e',
);


define ('WNT_INSTALL_BASE', '/var/www');

// If the develop repo location is defined (as WP_DEVELOP_DIR), use that
// location. Otherwise, we'll just assume that this plugin is installed in a
// WordPress develop SVN checkout.

if( false !== getenv('WP_DEVELOP_DIR') ) {
    require getenv('WP_DEVELOP_DIR') . '/tests/phpunit/includes/bootstrap.php';
} else {
    // can not use a relative path like this in our dev envornment because the 
    // plugin is a symbolic link. sym links cant folow relative paths
    //require '../../../../../tests/phpunit/includes/bootstrap.php';
    
    // so useing absolute path instead.
    require  WNT_INSTALL_BASE .'/tests/phpunit/includes/bootstrap.php';
}
