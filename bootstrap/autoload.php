<?php

/**
 * This file is responsible for auto-loading all WolfNet plugin classes.
 */

define('WNT_CLASS_ROOT', dirname(dirname(__FILE__)) . '/src');

include WNT_CLASS_ROOT . '/Autoloader.php';

$wntAutoloader = new Wolfnet_Autoloader(WNT_CLASS_ROOT, include dirname(__FILE__) . '/classmap.php');

spl_autoload_register(array($wntAutoloader, 'load'));
