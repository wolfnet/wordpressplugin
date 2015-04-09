<?php

class Wolfnet_Factory
{


    /* PROPERTIES ******************************************************************************* */

    private $singletons = array();

    private $args;


    /* CONSTRUCTOR ****************************************************************************** */

    public function __construct(array $args=array())
    {
        $this->args = $args;
    }


    /* PUBLIC METHODS *************************************************************************** */

    public function get($class, array $args=array())
    {

        if (array_key_exists($class, $this->singletons)) {
            $object = $this->singletons[$class];

        } else {
            $factoryMethod = 'get' . $class;

            if (!method_exists($this, $factoryMethod)) {
                $message = 'A programming exception has occurred. [' . $factoryMethod . ']';
                $details = 'The WolfNet IDX plugin attempted to retrieve a object from the factory '
                         . 'which has not been configured in the factory.';
                throw new Wolfnet_Exception($message, $details, $class);
            }

            $object = call_user_func(array(&$this, $factoryMethod), $args);

            $this->singletons[$class] = &$object;

        }

        return $object;

    }


    /* PRIVATE METHODS ************************************************************************** */

    /**
     * This method is used to retrieve an instance of the API Client. This logic is encapsulated in
     * its own function for clarity. Because we are decorating the API Client there are a number of
     * other objects that are needed before we can return the API Client.
     *
     * TODO: Ideally this kind of logic would be encapsulated inside of IOC container which handles
     * dependency injection. At some point we want to introduce such an object.
     *
     *                +--------------------+
     *                |                    |
     *    +---------->+ Wolfnet_Api_Client |
     *    |           |                    |
     *    |           +---------+----------+
     *    |                     ^
     *    |                     |
     *    |                     |
     *    |           +---------+------------------+
     *    |  extends  |                            |
     *    +-----------+ Wolfnet_Api_StatsDecorator |
     *    ^           |                            |
     *    |           +----------------------------+
     *    |                     ^
     *    |                     |
     *    |                     |
     *    |           +---------+--------------------+      +--------------------------------+
     *    |  extends  |                              |      |                                |
     *    +-----------+ Wolfnet_Api_CachingDecorator +----->+ Wolfnet_Service_CachingService |
     *    ^           |                              |      |                                |
     *    |           +---------+--------------------+      +--------------------------------+
     *    |                     ^
     *    |                     |
     *    |                     |
     *    |           +---------+-----------------+
     *    |  extends  |                           |
     *    +-----------+ Wolfnet_Api_AuthDecorator |
     *                |                           |
     *                +---------------------------+
     *
     * @return  Wolfnet_Api_Client  A decorated API client.
     *
     */
    private function getWolfnet_Api_Client()
    {
        $ssl = $this->args['sslEnabled'];
        $port = $ssl ? 443 : 80;

        $apiClient = new Wolfnet_Api_Client(Wolfnet_Api_Client::DEFAULT_HOST, $port, $ssl);

        $apiClient = new Wolfnet_Api_StatsDecorator($apiClient);
        $cachingService = $this->get('Wolfnet_Service_CachingService');
        $apiClient = new Wolfnet_Api_CachingDecorator($apiClient, $cachingService);
        $apiClient = new Wolfnet_Api_AuthDecorator($apiClient);

        return $apiClient;

    }


    private function getWolfnet_Service_CachingService()
    {
        return new Wolfnet_Service_CachingService($this->args['cacheRenew']);
    }


    private function getWolfnet_Views()
    {
        return new Wolfnet_Views();
    }


    private function getWolfnet_Admin()
    {
        return new Wolfnet_Admin($this->args['plugin']);
    }


}
