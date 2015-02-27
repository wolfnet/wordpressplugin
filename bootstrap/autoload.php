<?php

/**
 * This file is responsible for auto-loading all WolfNet plugin classes.
 */

$wntClassRoot = dirname(dirname(__FILE__)) . '/src';

include $wntClassRoot . '/Autoloader.php';

$autoloader = new Wolfnet_Autoloader($wntClassRoot, include dirname(__FILE__) . '/classmap.php');

spl_autoload_register(array($autoloader, 'load'));
