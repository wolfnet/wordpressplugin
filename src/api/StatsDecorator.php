<?php

/**
 * WolfNet API Statistics Decorator
 *
 * This class decorates the API Client and is used to collect statistical data about the environment
 * the plugin is running on. This data is used for debugging purposes.
 *
 * @package Wolfnet\Api
 * @copyright 2015 WolfNet Technologies, LLC.
 * @license GPLv2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 *
 */
class Wolfnet_Api_StatsDecorator extends Wolfnet_Api_Client
{


    /* PROPERTIES ******************************************************************************* */

    /**
     * This property holds a reference to the API client which is being decorated.
     * @var Wolfnet_Api_Client
     */
    private $client;


    /* CONSTRUCTOR ****************************************************************************** */

    /**
     * This constructor method must received a reference to the API Client which is being decorated.
     * This reference will then be used to perform actual requests to the API.
     *
     * @param  Wolfnet_Api_Client  $client  A reference to an API client
     */
    public function __construct(Wolfnet_Api_Client &$client)
    {
        $this->client = $client;
    }


    /* PUBLIC METHODS *************************************************************************** */

    /**
     * This method currently just passes the request along to the client.
     *
     * @param  string  $token    The API token that should be used to the API requests.
     * @param  string  $resource  The API endpoint the request should be made to.
     * @param  string  $method    The HTTP verb that should be used to make the request.
     * @param  array   $data      Any data that should be passed along with the request.
     * @param  array   $headers   The HTTP headers to be sent with the request.
     * @param  array   $options   Extra options that may be passed into the request. This argument
     *                            mostly exists to facilitate the decorators. Possible keys used by
     *                            this decorator include 'reAuth' and 'attempts'
     *
     * @return array              The API response structure.
     *
     */
    public function sendRequest(
        $token,
        $resource,
        $method = "GET",
        array $data = array(),
        array $headers = array(),
        array $options = array()
    ) {

        // Get an array of the arguments to this function call.
        $args = func_get_args();

        return call_user_func_array(array($this->client, 'sendRequest'), $args);

    }


    /**
     * This method just passes the authentication request off to the decorated client.
     *
     * @param  string  $key      The API key to be used to authenticate with the API.
     * @param  array   $headers  The HTTP headers to be sent with the request.
     * @param  array   $options  Extra options that may be passed into the request. This parameter
     *                           mostly exists to facilitate the decorators.
     *
     * @return array             The API response structure
     *
     */
    public function authenticate($key, array $headers = array(), array $options = array())
    {
        $this->injectStatHeaders($headers);

        return $this->client->authenticate($key, $headers, $options);

    }


    /* PRIVATE METHODS ************************************************************************** */

    /**
     * This method takes in a reference to an array of header key/value pairs and adds additional
     * data to them.
     *
     * TODO: Most of this data should be injected into this decorator rather than being pulled
     * directly from their source as is currently being done.
     *
     * @param  array  $headers  The headers to be modified.
     *
     * @return void
     */
    private function injectStatHeaders(array &$headers)
    {
        global $wp_version;

        // This code needed for certain scenarios where wolfnet is not yet in globals
        if (array_key_exists('wolfnet',$GLOBALS)) {
        	$v = $GLOBALS['wolfnet']->version;
        } else {
        	$v = 'UNAVAILABLE';
        }

        $stats = array(
            'pluginVersion' => $v,
            'phpVersion'    => phpversion(),
            'wpVersion'     => $wp_version,
            'wpTheme'       => wp_get_theme()->get('Name'),
        );

        $headers = array_merge($stats, $headers);

    }


}
