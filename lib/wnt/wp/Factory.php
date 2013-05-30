<?php

class WNT_WP_Factory
{


    static private $instance;
    private $parameters = array();
    private $singletonCache = array();


    private function __construct(array $parameters=array())
    {
        $this->parameters = $parameters;
    }


    static public function getInstance(array $parameters=array())
    {
        return (isset(self::$instance)) ? self::$instance : self::$instance = new self($parameters);
    }


    public function get($class)
    {

        // Check the singleton cache for the requested class.
        if (array_key_exists($class, $this->singletonCache)) {
            return $this->singletonCache[$class];
        }

        $singleton = true;

        switch($class) {

            case 'TemplateEngine':
                $obj = new WNT_WP_TemplateEngine();
                break;

            case 'ListingService':
                $obj = new WNT_WP_Service_ListingService();
                break;

            case 'MarketDisclaimerService':
                $obj = new WNT_WP_Service_MarketDisclaimerService();
                break;

            case 'SearchService':
                $obj = new WNT_WP_Service_SearchService();
                $obj->setSettingsService($this->get('SettingsService'));
                break;

            case 'SettingsService':
                $obj = new WNT_WP_Service_SettingsService();
                break;

            case 'SortService':
                $obj = new WNT_WP_Service_SortService();
                break;

            default:
                throw new Exception('The requested class does not exist.');
                die();
                break;

        }

        // Loop over the global factory parameters and if the object has a setter method for the
        // param set it.
        foreach ($this->parameters as $key=>$param) {
            if (method_exists($obj, 'set' . $key)) {
                call_user_func(array(&$obj, 'set' . $key), $param);
            }
        }

        // If the object has a factory setter set it to the factory.
        if (method_exists($obj, 'setFactory')) {
            $obj->setFactory($this);
        }

        // If the object should be a singleton add it to the singleton cache.
        if ($singleton) {
            $singletonCache[$class] = $obj;
        }

        return $obj;

    }


}
