<?php

/**
 * API Client Caching Decorator
 *
 * This class is meant to decorator (see Decorator Pattern) the ApiClient class and apply caching
 * logic to it's requests. We are doing it this way to maintain separation of concerns, the API need
 * not know anything about caching. A benefit of using a decorator is that we can easily exclude
 * the decorator from the execution of a request (even at run time) when debugging issues,
 * effectively eliminating caching from the equation to ensure it is not the source of any issues.
 *
 * This class uses the WordPress Transients API (see http://codex.wordpress.org/Transients_API) to
 * store (cache) and later retrieve certain API responses. This means that there are fewer requests
 * being made directly to the API server, reducing load and lower response times.
 *
 * Along with the individual requests being cached we also cache a sort of registry which is used to
 * group all of our (WolfNet) entries in the cache. This way we can target them and clear them all
 * explicitly without affecting any other transient data.
 *
 * @package Wolfnet\Api
 * @copyright 2015 WolfNet Technologies, LLC.
 * @license GPLv2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 *
 */
class Wolfnet_Api_CachingDecorator extends Wolfnet_Api_Client
{


    /* CONSTANTS ******************************************************************************** */

    /**
     * This constant is use hold the default cache length for standard API requests.
     * @var int
     */
    const CACHE_SPAN = 1800; // 30 minutes

    /**
     * This constant is use hold the default cache length for authentication API requests.
     * @var int
     */
    const AUTH_CACHE_SPAN = 3600; // 60 minutes


    /* PROPERTIES ******************************************************************************* */

    /**
     * This property holds a reference to the API client which is being decorated.
     * @var Wolfnet_Api_Client
     */
    private $client;

    /**
     * This property holds a reference to the WolfNet caching service which is a facade to the
     * WordPress Transients API.
     * @var Wolfnet_Service_CachingService
     */
    private $service;


    /* CONSTRUCTOR ****************************************************************************** */

    /**
     * This constructor method must received a reference to the API Client which is being decorated
     * and a reference to the Caching Service. The caching service will be used to set and get
     * cached data while the API Client be used to perform actual requests to the API when no cached
     * data exists.
     *
     * @param  Wolfnet_Api_Client  $client  A reference to an API client
     * @param  Wolfnet_Service_CachingService  $service  A reference to the WolfNet caching service.
     *
     */
    public function __construct(Wolfnet_Api_Client &$client, Wolfnet_Service_CachingService &$service)
    {
        $this->client = $client;
        $this->service = $service;

    }


    /* PUBLIC METHODS *************************************************************************** */

    /**
     * This method will first attempt to retrieve the request from the cache when appropriate. If
     * no value is found in the cache it will defer the request to the API client being decorated
     * and will then cache the response if appropriate.
     *
     * NOTE: Only GET requests should be cached per HTTP specs
     *
     * @param  string  $token     The API token to be used to make the request.
     * @param  string  $resource  The API endpoint the request should be made to.
     * @param  string  $method    The HTTP verb that should be used to make the request.
     * @param  array   $data      Any data that should be passed along with the request.
     * @param  array   $headers   The HTTP headers to be sent with the request.
     * @param  array   $options   Extra options that may be passed into the request. This argument
     *                            mostly exists to facilitate the decorators. Possible keys used by
     *                            this decorator include 'key' and 'force'.
     *
     * @return array              The API response structure.
     *
     */
    public function sendRequest(
        $token,
        $resource,
        $method = "GET",
        $data = array(),
        $headers = array(),
        $options = array()
    ) {
        $args = func_get_args();
        $result = null;

        /* Attempt to retrieve a 'key' value from the options argument which will be use to uniquely
         * identify requests being made for that specific key. If none is present we fall back to
         * the token which is even more unique. */
        $key = array_key_exists('key', $options) ? $options['key'] : $token;

        /* Attempt to retrieve the 'cache' value from the options argument. */
        $cache = array_key_exists('cache', $options) ? $options['cache'] : true;

        /* If the force key is present we should force the decorator to retrieve new data from the
         * API even if cached data was found. */
        $force = array_key_exists('force', $options) ? $options['force'] : false;

        // The request is not a GET request we should not be caching.
        if ($method != 'GET') {
            $cache = false;
        }

        // Generate a cache key if appropriate
        $cacheKey = ($cache) ? $this->cacheKeyFromRequest($key, $resource, $data) : null;

        // Attempt to use the key to retrieve data.
        if ($cacheKey !== null) {
            $result = $this->service->cacheGet($cacheKey);
        }

        // If we don't have any data yet perform the request.
        if ($force || $result === null) {
            $result = call_user_func_array(array($this->client, 'sendRequest'), $args);

            // Now that we have the data set it in the cache if we have a key.
            if ($cacheKey !== null) {
                $this->service->cachePut($cacheKey, $result, self::CACHE_SPAN);
            }

        } else {
            $result['fromCache'] = true;
        }

        $result['cacheKey'] = $cacheKey;

        return $result;

    }


    /**
     * This method will attempt to retrieve authentication data from the cache. If none is found it
     * will defer to the API client being decorated to perform a new authentication attempt against
     * the API directly. This response will then be cached.
     *
     * The authentication is cached to reduce the number of authentication requests which need to be
     * made to the API.
     *
     * @param  string  $key      The API key to authenticate with.
     * @param  array   $headers  The HTTP headers to be sent with the request.
     * @param  array   $options  Extra options that may be passed into the request. This argument
     *                           mostly exists to facilitate the decorators. Possible keys used by
     *                           this decorator include 'force'.
     *
     * @return array             The API response structure.
     *
     */
    public function authenticate($key, array $headers=array(), array $options=array())
    {
        /* If the force key is present we should force the decorator to retrieve new data from the
         * API even if cached data was found. */
        $force = array_key_exists('force', $options) ? $options['force'] : false;

        // Generate a cache key
        $cacheKey = $this->cacheKeyFromApiKey($key);

        // var_dump($cacheKey); exit;

        // Attempt to retrieve the token from the cache.
        $result = $this->service->cacheGet($cacheKey);

        // var_dump($result); exit;

        // If we don't have a token at this point we need to get one from the API.
        if ($force || $result === null) {
            // Retrieve a token from the API if one was not in the cache.
            $result = $this->client->authenticate($key, $headers, $options);

            // Put the new token into the cache.
            $this->service->cachePut($cacheKey, $result, self::AUTH_CACHE_SPAN);

        } else {
            $result['fromCache'] = true;
        }

        $result['cacheKey'] = $cacheKey;

        return $result;

    }


    /* PROPERTIES ******************************************************************************* */

    /**
     * This method uses portions of the API request which make it unique to generate a reproducible
     * and unique cache key which will be used to store and later retrieve cached data.
     *
     * The key is a SHA1 hashes of the unique data prefixed with a string that makes our keys easier
     * to identify in the cache for debugging purposes.
     *
     * @param  string  $key       The API key that was used to authenticate with the API.
     * @param  string  $resource  The endpoint (URI) being requested from the API.
     * @param  string  $data      Any request data being included with the request (query string).
     *
     * @return string             The generated cache key.
     *
     */
    private function cacheKeyFromRequest($key, $resource, $data)
    {
        return sha1($key . $resource . json_encode($data));
    }


    /**
     * This method uses the API key being used to authenticate to generate a reproducible and unique
     * cache key which will be used to store and later retrieve cached data.
     *
     * The key is a SHA1 hashes of the API key prefixed with a string that makes our keys easier to
     * identify in the cache for debugging purposes.
     *
     * @param  string  $key       The API key that was used to authenticate with the API.
     *
     * @return string             The generated cache key.
     *
     */
    private function cacheKeyFromApiKey($key)
    {
        return sha1($key);
    }


}
