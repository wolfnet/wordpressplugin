<?php

/**
 * WolfNet Class Autoloader
 *
 * This class is responsible for loading all WolfNet plugin classes. This is currently done by
 * attempting to locate the requested class from within a 'class-map' array. At some point in the
 * future we will change this to use a directory/naming convention.
 *
 * @package Wolfnet
 * @license GNU v2
 * @copyright 2015 WolfNet Technologies, LLC.
 *
 */
class Wolfnet_Autoloader
{


    /* PROPERTIES ******************************************************************************* */

    /** @var  string  The base directory from which file will be located. */
    private $classRoot;

    /** @var  array  An array of classes. key = class name, value = file path */
    private $classMap;


    /* CONSTRUCTOR ****************************************************************************** */

    public function __construct($classRoot, $classMap)
    {
        $this->classRoot = $classRoot;
        $this->classMap = $classMap;
    }


    /* PUBLIC METHODS *************************************************************************** */

    /**
     * This method is registered to the Standard PHP Library function spl_autoload_register (see
     * http://php.net/manual/en/language.oop5.autoload.php) and is used to auto-load classes which
     * are requested by name but not yet defined in the current runtime.
     *
     * @param  string  $class  The class name to be loaded.
     */
    public function load($class)
    {

        if (array_key_exists($class, $this->classMap)) {
            include $this->classRoot . $this->classMap[$class];
        }

    }


}
