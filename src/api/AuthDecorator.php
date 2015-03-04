<?php

/**
 * WolfNet API Authentication Decorator
 *
 * This class decorates the API Client class in order to simplify authentication with the API.
 * Rather than forcing the consumer of the API Client to perform authentication and keep track of
 * the authentication token this class takes care of that work. Any time the consumer would like to
 * make a request to the API it will do so using the sendRequest method and pass in the API Key.
 * This key will then automatically be used to authenticate with the API, retrieve a token, then
 * immediately use that toke to perform the request.
 *
 * NOTE: Because authentication is done with a key while standard requests are made with a token
 * this class slightly modifies the sendRequest method signature. Instead of the first argument
 * being the API Token that would normally be retrieved via an authentication request the consumer
 * will send an API key which this class will use to perform authentication.
 *
 * @package Wolfnet\Api
 * @copyright 2015 WolfNet Technologies, LLC.
 * @license GPLv2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 *
 */
class Wolfnet_Api_AuthDecorator extends Wolfnet_Api_Client
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
     * This method is a slightly modified version of the one in the parent API Client class. Rather
     * that taking in an API token and performing requests this method takes in an API key which is
     * then used to retrieve an API token from the API (authenticate) and then makes requests to the
     * client reference being decorated.
     *
     * @param  string  $key       The API key to be used to authenticate with the API.
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
        $key,
        $resource,
        $method = "GET",
        array $data = array(),
        array $headers = array(),
        array $options = array()
    ) {

        // Apply some default values for options that we may not get.
        $reAuth = array_key_exists('reAuth', $options) ? $options['reAuth'] : false;
        $attempts = array_key_exists('attempts', $options) ? $options['attempts'] : 1;

        // Get an array of the arguments to this function call.
        $args = func_get_args();

        // Retrieve authentication information.
        $auth = $this->authenticate($key, array('force'=>$reAuth));
        $token = $auth['responseData']['data']['api_token'];

        // var_dump($auth); exit;

        /* Since we want to pass an option we need to make sure default args get set. */
        $args[0] = $token;
        $args[2] = (isset($args[2])) ? $args[2] : 'GET';
        $args[3] = (isset($args[3])) ? $args[3] : array();
        $args[4] = (isset($args[4])) ? $args[4] : array();
        $args[5] = array('key' => $key);

        try {

            // Forward the request on to the API Client.
            $result = call_user_func_array(array($this->client, 'sendRequest'), $args);

        }
        catch (Wolfnet_Api_Exception $e) {

            if ($e->getCode() === Wolfnet_Api_Client::AUTH_ERROR && $attempts < 5) {
                $args[0] = $key;
                $args[5]['reAuth'] = true;
                $args[5]['attempts'] = $attempts + 1;
                $result = call_user_func_array(array($this, 'sendRequest'), $args);
            } else {
                throw $e;
            }

        }

        return $result;

    }


    /**
     * This method just passes the authentication request off to the decorated client.
     *
     * @param  string  $key      The API key to be used to authenticate with the API.
     * @param  array   $options  Extra options that may be passed into the request. This parameter
     *                           mostly exists to facilitate the decorators.
     *
     * @return array             The API response structure
     *
     */
    public function authenticate($key, array $options=array())
    {
        return $this->client->authenticate($key, $options);
    }


}
