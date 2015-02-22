<?php

/**
 * This file is responsible for auto-loading all WolfNet plugin classes.
 */

// Define the root location classes should be loaded from.
// NOTE: We will be changing this to a 'src' directory in the future.
$wntClassRoot = dirname(dirname(__FILE__));

$wntClassMap = array(
    'Wolfnet' => '/src/Wolfnet_Plugin.php',
    'Wolfnet_Admin' => '/src/Wolfnet_Admin.php',
    'Wolfnet_Views' => '/src/Wolfnet_Views.php',
    'Wolfnet_Api_Wp_Client' => '/src/wolfnet-api-wp-client/WolfnetApiClient.php',
    'Wolfnet_AbstractWidget' => '/src/widget/AbstractWidget.php',
    'Wolfnet_FeaturedListingsWidget' => '/src/widget/FeaturedListingsWidget.php',
    'Wolfnet_ListingGridWidget' => '/src/widget/ListingGridWidget.php',
    'Wolfnet_PropertyListWidget' => '/src/widget/PropertyListWidget.php',
    'Wolfnet_QuickSearchWidget' => '/src/widget/QuickSearchWidget.php',
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
