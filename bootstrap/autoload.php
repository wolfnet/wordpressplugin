<?php

/**
 * This file is responsible for auto-loading all WolfNet plugin classes.
 */

// Define the root location classes should be loaded from.
// NOTE: We will be changing this to a 'src' directory in the future.
$wntClassRoot = dirname(dirname(__FILE__));

$wntClassMap = array(
    'Wolfnet' => '/wolfnet/Wolfnet_Plugin.php',
    'Wolfnet_Admin' => '/wolfnet/Wolfnet_Admin.php',
    'Wolfnet_Views' => '/wolfnet/Wolfnet_Views.php',
    'Wolfnet_Api_Wp_Client' => '/wolfnet/wolfnet-api-wp-client/WolfnetApiClient.php',
    'Wolfnet_AbstractWidget' => '/widget/AbstractWidget.php',
    'Wolfnet_FeaturedListingsWidget' => '/widget/FeaturedListingsWidget.php',
    'Wolfnet_ListingGridWidget' => '/widget/ListingGridWidget.php',
    'Wolfnet_PropertyListWidget' => '/widget/PropertyListWidget.php',
    'Wolfnet_QuickSearchWidget' => '/widget/QuickSearchWidget.php',
);

function wntAutoload($class)
{
    global $wntClassMap;
    global $wntClassRoot;
    if (array_key_exists($class, $wntClassMap)) {
        include $wntClassRoot . $wntClassMap[$class];
    }
}

spl_autoload_register('wntAutoload');
