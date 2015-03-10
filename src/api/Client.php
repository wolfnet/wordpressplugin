<?php

/**
 * Client interface for the WolfNet API.
 *
 * This class is a WordPress specific PHP implementation of the WolfNet API Client. It is used to
 * make requests to the API and receive responses from the API. The scope of this class should not
 * extend beyond basic HTTP communication. Any other logic such as caching should be accomplished
 * by decorating this class.
 *
 * In order for the API client to perform requests to the API it must first prove to the API that it
 * has valid credentials to do so, namely a valid and active *API key*. With the API key the client
 * can retrieve an API token (see API documentation) which is then used to make any subsequent
 * requests.
 *
 * @package Wolfnet\Api
 * @copyright 2015 WolfNet Technologies, LLC.
 * @license GPLv2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 *
 */
class Wolfnet_Api_Client
{


    /* CONSTANTS ******************************************************************************** */

    /**
     * @var  int  The number of seconds to wait for the API to respond.
     */
    const REQUEST_TIMEOUT = 10;

    /**
     * @var  string  This code is received from the API when a request is made without proper
     *               client authentication.
     */
    const NO_AUTH_ERROR = 1001;

    /**
     * @var  string  This code is received from the API when the user must be authenticated.
     */
    const USER_AUTH_ERROR = 1004;

    /**
     * @var  string  This code is received from the API when an invalid API token is provided.
     */
    const AUTH_ERROR = 1005;


    /* PROPERTIES ******************************************************************************* */

    /**
     * @var  string  The hostname for the API where requests will be sent.
     */
    private $host;

    /**
     * @var  int  The port for the API where requests will be sent
     */
    private $port;

    /**
     * @var  int  The API version that will be interacted with.
     */
    private $version;


    /* CONSTRUCTOR ****************************************************************************** */

    /**
     * Constructor Method
     *
     * This constructor method instantiates the ApiClient class and allows the consumer to specify
     * details about what API should be interacted with.
     *
     * @param string  $host    The hostname for the API where requests will be sent.
     * @param integer $port    The port for the API where requests will be sent
     * @param integer $version The API version that will be interacted with.
     */
    public function __construct($host='api.wolfnet.com', $port=80, $version=1)
    {
        $this->host = $host;
        $this->port = $port;
        $this->version = $version;

    }


    /* PUBLIC METHODS *************************************************************************** */

    /**
     * This method is used to authenticate with the API and retrieve an API token which is needed
     * to perform any other requests to the API.
     *
     * @param  string  $key  API key to be used for authentication.
     * @param  array   $headers  The HTTP headers to be sent with the request.
     * @param  array   $options  Extra options that may be passed into the request. This parameter
     *                           mostly exists to facilitate the decorators.
     *
     * @return array         The API response structure.
     *
     */
    public function authenticate($key, array $headers=array(), array $options=array())
    {
        $data = array(
            'key' => $key,
            'v' => $this->version,
        );

        return $this->performRequest($key, null, '/core/auth', 'POST', $data, $headers);

    }


    /**
     * This method makes pre-authenticated requests to the WolfNet API and returns the response.
     *
     * @param  string  $token    The API token that should be used to the API requests.
     * @param  string  $resource The API endpoint the request should be made to.
     * @param  string  $method   The HTTP verb that should be used to make the request.
     * @param  array   $data     Any data that should be passed along with the request.
     * @param  array   $headers  The HTTP headers to be sent with the request.
     * @param  array   $options  Extra options that may be passed into the request. This parameter
     *                           mostly exists to facilitate the decorators.
     *
     * @return array  An array containing the HTTP response.
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

        $headers['api_token'] = $token;

        return $this->performRequest(null, $token, $resource, $method, $data, $headers);

    }


    /* PRIVATE METHODS ************************************************************************** */

    /**
     * This method takes in request parameters and performs HTTP requests to the WolfNet API using
     * the WordPress HTTP API (see http://codex.wordpress.org/HTTP_API).
     *
     * @param  string  $key      The API key that should be used to make authentication the requests.
     * @param  string  $token    The API token that should be used to make regular requests.
     * @param  string  $resource The API endpoint the request should be made to.
     * @param  string  $method   The HTTP verb that should be used to make the request.
     * @param  array   $data     Any data that should be passed along with the request.
     * @param  array   $headers  The HTTP headers to be sent with the request.
     *
     * @throws Wolfnet_Api_ApiException  This exception is thrown any time there is an issue
     *                                   with the request. This exception should then be caught
     *                                   later and displayed as a user friendly message.
     *
     * @return array  An array containing the HTTP response.
     */
    private function performRequest(
        $key,
        $token,
        $resource,
        $method = "GET",
        array $data = array(),
        array $headers = array()
    ) {

        $uri = $this->uriFromResource($resource);

        try {
            $this->validateRequestData($data);
        }
        catch (Wolfnet_Api_ApiException $e) {
            throw $e->appendDetails('While attempting request to (' . $uri . ').');
        }

        $requestArgs = array(
            'method'   => $method,
            'headers'  => $headers,
            'timeout'  => self::REQUEST_TIMEOUT,
            'body'     => ($method != 'GET') ? $data : '',
        );

        if ($method == 'GET') {
            $this->encodeData($data);
            $uri = add_query_arg($data, $uri);
        }

        $response = wp_remote_request($uri, $requestArgs);

        try {
            $this->validateResponse($response);
        }
        catch (Wolfnet_Api_ApiException $e) {
            throw $e->appendDetails('While attempting request to (' . $uri . ').');
        }

        return $this->parseResponse($response, $uri, $method, $data);

    }


    /**
     * This method validates the data to be sent with the HTTP request.
     *
     * Specifically we are checking to make sure that the data which is being sent to the API can be
     * easily converted into a format which works with basic HTTP requests. This means we only want
     * Scalar values such as numbers, strings, and booleans.
     *
     * @param  array  $data  The data to be validated.
     *
     * @throws Wolfnet_Api_ApiException  This exception is thrown if any of the data that was
     *                                   given does not meet the validation criteria.
     *
     * @return null
     */
    private function validateRequestData(array $data)
    {

        // Loop over each key in the data and check if they are scalar (simple) values.
        foreach ($data as $key => $value) {

            if ($value != null && !is_scalar($value)) {
                $message = 'Tried to send invalid data to the API.';
                $details = '[' . $key . '] is not a valid API argument. '
                         . 'All API arguments must be scalar values. ';

                throw new Wolfnet_Api_ApiException($message, $details);

            }

        }

    }


    /**
     * This method turns a resource string into a fully qualified URL using the API host and port
     * that were passed into the constructor of API Client class.
     *
     * @param  string $resource The API resource (endpoint) be be converted to a URL.
     *
     * @return string           A fully qualified URL to the API.
     *
     */
    private function uriFromResource($resource)
    {
        return 'http://' . $this->host . ($this->port!=80 ? ':' . $this->port : '') . $resource;
    }


    /**
     * This method is use to encode any data to strings which are valid HTTP query string values.
     *
     * @param  array  $data  A reference of the key/value pair data to be encoded.
     *
     * @return null
     *
     */
    private function encodeData(array &$data)
    {

        foreach ($data as &$value) {
            $value = urlencode($value);
        }

    }


    /**
     * This method validates the HTTP response from the API. If the response does not pass validation
     * an exception is thrown which can be caught and acted upon later.
     *
     * @param  mixed  $response  A response from the WordPress HTTP API call. Could be an array or
     *                           a WP_Error object.
     *
     * @return null
     *
     */
    private function validateResponse($response)
    {

        // Check for a WordPress error object
        if (is_wp_error($response)) {
            $message = 'Remote request failed for unknown reason.';
            $details = 'WordPress error says: ' . $response->get_error_message();
            throw new Wolfnet_Api_ApiException($message, $details, $response);
        }

        $wpResponse = $response['response'];
        $responseCode = $wpResponse['code'];

        /**
         * This response code we received is not code 200. We don't know how to deal with this
         * response at this time so we will need to throw an exception.
         *
         * NOTE: At some point in the future we will probably need to make the client capable of
         * dealing with responses such as redirects.
         *
         */
        if ($responseCode != 200) {

            $responseText = $wpResponse['message'];
            $responseBody = array_key_exists('body', $response) ? $response['body'] : '';
            $responseData = json_decode($response['body']);
            $responseData = ($responseData !== null) ? $responseData : new stdClass();

            $message = array_key_exists('message', $wpResponse) ? $wpResponse['message'] : null;
            $metadata = property_exists($responseData, 'metadata') ? $responseData->metadata : new stdClass();
            $status = property_exists($metadata, 'status') ? $metadata->status : new stdClass();
            $errorCode = property_exists($status, 'errorCode') ? $status->errorCode : null;
            $statusCode = property_exists($status, 'statusCode') ? $status->statusCode : null;
            $errorID = property_exists($status, 'error_id') ? $status->error_id : null;
            $extendedInfo = property_exists($status, 'extendedInfo') ? $status->extendedInfo : null;

            // TODO: These two variables are used repeatedly below. Could be done better.
            $errorIDMessage = ($errorID !== null) ? 'API Error ID: ' . $errorID : null;
            $errorMessage = ($extendedInfo !== null) ? 'The API says: [' . $extendedInfo . ']' : null;


            // Here we will handle special API error responses.

            /**
             * The API has indicated that the request was made without a valid API token so we will
             * throw a special exception that we can can catch and attempt to re-authenticate.
             */
            $authErrorCode = 'Auth1005';
            if ($errorCode == $authErrorCode || $statusCode == $authErrorCode) {
                $message = 'Remote request was not authorized.';
                $details = 'The WolfNet API has responded that it did not receive a valid API token.'
                         . (($errorIDMessage !== null) ? $errorIDMessage : '') . ' '
                         . (($errorMessage !== null) ? $errorMessage : '');
                throw new Wolfnet_Api_ApiException($message, $details, $response, null, self::AUTH_ERROR);
            }

            /**
             * The API has indicated that the request was made but the data can only be accessed by
             * a user who has authenticated (double opt-in) their account.
             */
            $userAuthErrorCode = 'Auth1004';
            if ($errorCode == $userAuthErrorCode || $statusCode == $userAuthErrorCode) {
                $message = 'User must be authenticated to view this information.';
                $details = 'The WolfNet API has responded the data requested can only be viewed by '
                         . 'a user that has authenticated their account. '
                         . (($errorIDMessage !== null) ? $errorIDMessage : '') . ' '
                         . (($errorMessage !== null) ? $errorMessage : '');
                throw new Wolfnet_Api_ApiException($message, $details, $response, null, self::USER_AUTH_ERROR);
            }


            // The API returned a 401 Unauthorized
            if ($responseCode == 401) {
                $message = 'Remote request resulted in a [401 Unauthorized] response.';
                $details = 'The WolfNet API has indicated that the request was made '
                         . 'without properly authentication. '
                         . (($errorIDMessage !== null) ? $errorIDMessage : '') . ' '
                         . (($errorMessage !== null) ? $errorMessage : '');
                throw new Wolfnet_Api_ApiException($message, $details, $response, null, self::NO_AUTH_ERROR);
            }

            // The API returned a 500 Internal Server Error
            if ($responseCode == 500) {
                $message = 'Remote request resulted in a [500 Internal Server Error] response.';
                $details = 'The WolfNet API appears to be unresponsive at this time.'
                         . (($errorIDMessage !== null) ? $errorIDMessage : '') . ' '
                         . (($errorMessage !== null) ? $errorMessage : '');
                throw new Wolfnet_Api_ApiException($message, $details, $response);
            }

            // The API returned a 503 Service Unavailable
            if ($responseCode == 503) {
                $message = 'Remote request resulted in a [503 Service Unavailable] response.';
                $details = 'The WolfNet API appears to be unresponsive at this time but should be back soon.'
                         . (($errorIDMessage !== null) ? $errorIDMessage : '') . ' '
                         . (($errorMessage !== null) ? $errorMessage : '');
                throw new Wolfnet_Api_ApiException($message, $details, $response);
            }

            // The API returned a 403 Forbidden
            if ($responseCode == 403) {
                $message = 'Remote request resulted in a [403 Forbidden] response.';
                $details = 'An attempt was made to request data that is not available to the key that '
                         . 'was used to authenticate the request.'
                         . (($errorIDMessage !== null) ? $errorIDMessage : '') . ' '
                         . (($errorMessage !== null) ? $errorMessage : '');
                throw new Wolfnet_Api_ApiException($message, $details, $response);
            }

            // The API returned a 400 Bad Response
            // There are several reasons why this might have happened so we should check for those
            if ($responseCode == 400) {

                // If WordPress has provided a message use that for the exception
                if ($message !== null) {
                    throw new Wolfnet_Api_ApiException($message, 'See data for details.', $response);

                // Default exception for bad responses
                } else {
                    $message = 'Remote request was not successful.';
                    $details = 'The WolfNet API has indicated that the request was "bad" for an unknown reason.'
                             . (($errorIDMessage !== null) ? $errorIDMessage : '') . ' '
                             . (($errorMessage !== null) ? $errorMessage : '');
                    throw new Wolfnet_Api_ApiException($message, $details, $response);

                }

            }

            // There was some other issue that we have not anticipated.

            $message = 'Remote request was not successful.';
            $details = 'The WolfNet plugin received an API response it is not prepared to deal with. '
                     . 'Status: ' . $responseCode . ' ' . $responseText . ";\n"
                     . (($errorIDMessage !== null) ? $errorIDMessage : '') . ' '
                     . (($errorMessage !== null) ? $errorMessage : '');
            throw new Wolfnet_Api_ApiException($message, $details, $response);

        }

    }


    /**
     * This method is used to abstract a raw response from the WordPress HTTP API into a format that
     * we control, in this case an array. This way if the WP response changes we only have one place
     * in our code to change.
     *
     * Our structure currently contains for request and response data to make debugging easier.
     *
     * NOTE: This method expects that the response is an array at this point. If the response is not
     * and array it should have been caught by the validation method (validateResponse) and then
     * resulted in an exception.
     *
     * @param  array  $response    The WordPress HTTP API response.
     * @param  string $uri         The request URI.
     * @param  string $method      The request HTTP verb.
     * @param  array  $requestData The request data.
     *
     * @return array               A uniform array of request and response data.
     */
    private function parseResponse(array $response, $uri, $method, array $requestData)
    {

        $res = array(
            'requestUrl' => $uri,
            'requestMethod' => $method,
            'requestData' => $requestData,
            'responseStatusCode' => $response['response']['code'],
            'responseData' => $response['body'],
            'timestamp' => time(),
            'fromCache' => false,
        );

        $contentType = $response['headers']['content-type'];

        if (strpos($contentType, 'application/json') !== false) {
            $res['responseData'] = json_decode($res['responseData'], true);
        }

        return $res;

    }


}
