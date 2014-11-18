<?php

/**
 * @title         Wolfnet_Api.php
 * @copyright     Copyright (c) 2012, 2013, WolfNet Technologies, LLC
 *
 *                This program is free software; you can redistribute it and/or
 *                modify it under the terms of the GNU General Public License
 *                as published by the Free Software Foundation; either version 2
 *                of the License, or (at your option) any later version.
 *
 *                This program is distributed in the hope that it will be useful,
 *                but WITHOUT ANY WARRANTY; without even the implied warranty of
 *                MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *                GNU General Public License for more details.
 *
 *                You should have received a copy of the GNU General Public License
 *                along with this program; if not, write to the Free Software
 *                Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

class Wolfnet_Api
{


    /* Properties ******************************************************************************* */
    /*  ____                            _   _                                                     */
    /* |  _ \ _ __ ___  _ __   ___ _ __| |_(_) ___  ___                                           */
    /* | |_) | '__/ _ \| '_ \ / _ \ '__| __| |/ _ \/ __|                                          */
    /* |  __/| | | (_) | |_) |  __/ |  | |_| |  __/\__ \                                          */
    /* |_|   |_|  \___/| .__/ \___|_|   \__|_|\___||___/                                          */
    /*                 |_|                                                                        */
    /* ****************************************************************************************** */

    /**
     * This will be set to the version of the injected class in the constructor
     * @var string
     */
    protected $version;

    /**
     * This property is a unique identifier for a value in the WordPress Transient API where
     * references to other transient values are stored.
     * @var string
     */
    public $transientIndexKey = 'wolfnet_transients';

    /**
     * The maximum amount of time a wolfnet value should be stored in the as a transient object.
     * Currently set to 1 week.
     * @var integer
     */
    protected $transientMaxExpiration = 604800;

    private $serviceUrl = 'http://services.mlsfinder.com/v1';

    /**
     * This property is used as a request scope key for storing the unique session key value for the
     * current user.
     * @var string
     */
    private $requestSessionKey = 'wntSessionKey';

    /**
     * This property contains the admin CSS as defined in the Edit CSS page.
     * @var string
     */
    public $adminCssOptionKey = "wolfnetCss_adminCss";

    /**
     * This property is used to determine how long a WNT session should last.
     * @var integer
     */
    private $sessionLength = 3600; // one hour


    private $url;


    /* Public Methods *************************************************************************** */
    /*  ____        _     _ _        __  __      _   _               _                            */
    /* |  _ \ _   _| |__ | (_) ___  |  \/  | ___| |_| |__   ___   __| |___                        */
    /* | |_) | | | | '_ \| | |/ __| | |\/| |/ _ \ __| '_ \ / _ \ / _` / __|                       */
    /* |  __/| |_| | |_) | | | (__  | |  | |  __/ |_| | | | (_) | (_| \__ \                       */
    /* |_|    \__,_|_.__/|_|_|\___| |_|  |_|\___|\__|_| |_|\___/ \__,_|___/                       */
    /*                                                                                            */
    /* ****************************************************************************************** */

    /* Featured Listings ************************************************************************ */

    public function getFeaturedListings(array $criteria=array())
    {
        $criteria['numrows']     = $criteria['maxresults'];
        $criteria['max_results'] = $criteria['maxresults'];
        $criteria['owner_type']  = $criteria['ownertype'];

        $productKey = $GLOBALS['wolfnet']->getProductKeyById($criteria['keyid']);

        $url = $this->serviceUrl . '/propertyBar/' . $productKey . '.json';
        $url = $GLOBALS['wolfnet']->buildUrl($url, $criteria);

        return $this->getApiData($url, 900)->listings;

    }


    public function getListings(array $criteria=array())
    {
        $keyConversion = array(
            'maxresults' => 'max_results',
            'ownertype'  => 'owner_type',
            'minprice'   => 'min_price',
            'maxprice'   => 'max_price',
            'zipcode'    => 'zip_code',
            'exactcity'  => 'exact_city',
            );

        foreach ($keyConversion as $key => $value) {
            if (!array_key_exists($value, $criteria) && array_key_exists($key, $criteria)) {
                $criteria[$value] = $criteria[$key];
            }
            unset($criteria[$key]);
        }

        $productKey = $GLOBALS['wolfnet']->getProductKeyById($criteria['keyid']);

        $url = $this->serviceUrl . '/propertyGrid/' . $productKey . '.json';
        $url = $GLOBALS['wolfnet']->buildUrl($url, $criteria);

        $data = $this->getApiData($url, 900);

        $absMaxResults = $this->getMaxResults($productKey);
        $absMaxResults = ($data->total_rows < $absMaxResults) ? $data->total_rows : $absMaxResults;

        foreach ($data->listings as &$listing) {
            $listing->numrows    = $criteria['numrows'];
            $listing->startrow   = $criteria['startrow'];
            $listing->maxresults = $absMaxResults;
        }

        return $data->listings;

    }


    public function transientIndex($data=null)
    {
        $key = $this->transientIndexKey;

        // Set transient index data.
        if ($data !== null && is_array($data)) {
            set_transient($key, $data, $this->transientMaxExpiration);
        }
        // Get transient index data.
        else {
            $data = get_transient($key);

            if ($data === false) {
                $data = $this->transientIndex(array());
            }

        }

        return $data;

    }


    public function getMapParameters($listingsData, $productKey=null)
    {
        if($productKey == null) {
            $productKey = $this->getDefaultProductKey();
        }

        $url = $this->serviceUrl . '/setting/' . $productKey . '.json'
             . '?setting=getallsettings';
        $data = $this->getApiData($url, 86400);

        $args['maptracks_map_provider'] = $data->settings->MAPTRACKS_MAP_PROVIDER;
        $args['map_start_lat'] = $data->settings->MAP_START_LAT;
        $args['map_start_lng'] = $data->settings->MAP_START_LNG;
        $args['map_start_scale'] = $data->settings->MAP_START_SCALE;
        $args['houseoverIcon'] = $GLOBALS['wolfnet']->url . 'img/houseover.png';
        $args['houseoverData'] = $this->getHouseoverData($listingsData,$data->settings->SHOWBROKERIMAGEHO);

        return $args;

    }


    public function getPricesFromApi($productKey)
    {
        $url  = $this->serviceUrl . '/setting/' . $productKey . '.json'
              . '?setting=site_text';
        $data = $this->getApiData($url, 86400);
        $data = (property_exists($data, 'site_text')) ? $data->site_text : new stdClass();
        $prcs = (property_exists($data, 'Price Range Values')) ? $data->{'Price Range Values'} : '';

        return explode(',', $prcs);

    }


    public function getMaptracksEnabled($productKey=null)
    {
        if($productKey == null) {
            $productKey = $GLOBALS['wolfnet']->getDefaultProductKey();
        }
        $url  = $this->serviceUrl . '/setting/' . $productKey
              . '?setting=maptracks_enabled';
        $data = $this->getApiData($url, 86400);
        $data = (property_exists($data, 'maptracks_enabled')) ? ($data->maptracks_enabled == 'Y') : false;

        return $data;

    }


    public function getSortOptions($productKey=null)
    {
        if($productKey == null) {
            $productKey = $GLOBALS['wolfnet']->getDefaultProductKey();
        }
        $url  = $this->serviceUrl . '/sortOptions/' . $productKey . '.json';

        return $this->getApiData($url, 86400)->sort_options;

    }


    public function getBaseUrl($productKey=null)
    {
        if($productKey == null) {
            $productKey = $GLOBALS['wolfnet']->getDefaultProductKey();
        }

        $url  = $this->serviceUrl . '/setting/' . $productKey . '.json';
        $url .= '?setting=SITE_BASE_URL';

        return $this->getApiData($url, 86400)->site_base_url;

    }


    public function productKeyIsValid($key=null)
    {
        $valid = false;

        if ($key != null) {
            $productKey = $key;
        }
        else {
            $productKey = json_decode($GLOBALS['wolfnet']->getDefaultProductKey());
        }

        $url = $this->serviceUrl . '/validateKey/' . $productKey . '.json';

        $http = wp_remote_get($url, array('timeout'=>180));

        if (!is_wp_error($http) && $http['response']['code'] == '200') {
            $data = json_decode($http['body']);
            $errorExists = property_exists($data, 'error');
            $statusExists = ($errorExists) ? property_exists($data->error, 'status') : false;

            if ($errorExists && $statusExists && $data->error->status === false) {
                $valid = true;
            }

        }

        return $valid;

    }


    public function getMarketDisclaimer($productKey=null)
    {
        if($productKey == null) {
            $productKey = $GLOBALS['wolfnet']->getDefaultProductKey();
        }
        $url = $this->serviceUrl . '/marketDisclaimer/' . $productKey . '.json';
        $url = $GLOBALS['wolfnet']->buildUrl($url, array('type'=>'search_results'));

        return $this->getApiData($url, 86400)->disclaimer;

    }


    public function getMarketName($apiKey)
    {
        $url = $this->serviceUrl . "/setting/" . $apiKey . ".json?setting=DATASOURCE";

        return $this->getApiData($url, 1000)->datasource;

    }


    /**
     * Decodes all HTML entities, including numeric and hexadecimal ones.
     *
     * @param mixed $string
     * @return string decoded HTML
     */
    public function html_entity_decode_numeric($string, $quote_style=ENT_COMPAT, $charset='utf-8')
    {
        $hexCallback = array(&$this, 'chr_utf8_hex_callback');
        $nonHexCallback = array(&$this, 'chr_utf8_nonhex_callback');

        $string = html_entity_decode($string, $quote_style, $charset);
        $string = preg_replace_callback('~&#x([0-9a-fA-F]+);~i', $hexCallback, $string);
        $string = preg_replace_callback('~&#([0-9]+);~i', $nonHexCallback, $string);

        return $string;

    }

    /**
     * Callback helper
     */
    public function chr_utf8_hex_callback($matches)
    {
        return $this->chr_utf8(hexdec($matches[1]));
    }


    public function chr_utf8_nonhex_callback($matches)
    {
        return $this->chr_utf8($matches[1]);
    }


    /* PRIVATE METHODS ************************************************************************** */
    /*  ____       _            _         __  __      _   _               _                       */
    /* |  _ \ _ __(_)_   ____ _| |_ ___  |  \/  | ___| |_| |__   ___   __| |___                   */
    /* | |_) | '__| \ \ / / _` | __/ _ \ | |\/| |/ _ \ __| '_ \ / _ \ / _` / __|                  */
    /* |  __/| |  | |\ V / (_| | ||  __/ | |  | |  __/ |_| | | | (_) | (_| \__ \                  */
    /* |_|   |_|  |_| \_/ \__,_|\__\___| |_|  |_|\___|\__|_| |_|\___/ \__,_|___/                  */
    /*                                                                                            */
    /* ****************************************************************************************** */


    private function getApiData($url, $cacheFor=900)
    {
        // Retrieve the WordPress version variable from the global scope for later use.
        global $wp_version;
        // Generate a key for caching based on a hash of the $url being requested.
        $key = 'wolfnet_' . md5($url);
        // Retrieve an index of all transient objects currently in use.
        $index = $this->transientIndex();
        // Create a time stamp of the current time.
        $time = time();
        // Attempt to retrieve a transient (cached) version of the data being requested.
        $data = (array_key_exists($key, $index)) ? get_transient($key) : false;

        // Add some extra values to the URL for metrics purposes.
        $url = $GLOBALS['wolfnet']->buildUrl($url, array(
            'pluginVersion' => $GLOBALS['wolfnet']->version,
            'phpVersion'    => phpversion(),
            'wpVersion'     => $wp_version,
            ));

        // If there was no matching data in the transient database or the time has expired we need
        // to attempt to retrieve fresh data form the API.
        if ($data === false || $time > $index[$key]) {

            // Perform an HTTP request to the API.
            $http = wp_remote_get($url, array('timeout'=>180));

            // If we didn't get any data from the transient database we need to generate an object
            // to populate with data from the API response.
            if (!is_object($data)) {
                $data = new stdClass();
                $data->error = new stdClass();
                $data->error->status = true;
                $data->error->message = 'Unknown error.';
                $data->url = $url;
            }

            // The API responded with a server error so capture that for later use
            if (!is_wp_error($http) && $http['response']['code'] >= 500) {
                $data->error->message = 'A remote server error occurred!';
            }
            // The API responded with a bad request error capture for later use
            elseif (is_wp_error($http) || $http['response']['code'] >= 400) {
                $data->error->message = 'A connection error occurred!';
                $index[$key] = $time;
                // We will cache this response since it may be a valid response such as the client's
                // API key has expired.
                set_transient($key, $data, $this->transientMaxExpiration);
            }
            else {
                // The API response should be formated as JSON so we will deserialize it into a PHP
                // standard object.
                $tmp = json_decode($http['body']);

                // If an error occurred while deserializing the JSON string (or what should have been
                // one), generate an error message which can be used later.
                if ($tmp === false) {
                    $data->error->message = 'An error occurred while attempting '
                        . 'to decode the body as Json.';
                }
                // The response was valid and decoded so we will use it as the data for this request.
                else {
                    $data = $tmp;
                }

                // If there is a data object we want to capture what URL the data came from.
                if (is_object($data)) {
                    $data->url = $url;
                }

                // Save the data to the transient database so we don't have to call the API again right away.
                $index[$key] = $time + $cacheFor;
                set_transient($key, $data, $this->transientMaxExpiration);

            }

        }

        $errorExists = property_exists($data, 'error');
        $statusExists = ($errorExists) ? property_exists($data->error, 'status') : false;

        // If any errors occurred during this process output them to make debugging easier.
        if ($errorExists && $statusExists && $data->error->status) {
            print('<!-- WNT Plugin Error: ' . $data->error->message . ' -->');
        }

        // Save a "lookup" value in our transient database index to make future retrieval easier.
        $this->transientIndex($index);

        return $data;

    }


    // TODO: Make this a template
    private function getHouseoverData($listingsData,$showBrokerImage)
    {

        $houseoverData = array();

        foreach ($listingsData as $listing) {

            if (!is_null($listing->lat) && !is_null($listing->lng)) {

                $concatHouseover  = '<a style="display:block" rel="follow" href="' . $listing->property_url . '">';
                $concatHouseover .= '<div class="wolfnet_wntHouseOverWrapper">';
                $concatHouseover .= '<div data-property-id="' . $listing->property_id . '" class="wntHOItem">';
                $concatHouseover .= '<table class="wolfnet_wntHOTable">';
                $concatHouseover .= '<tbody>';
                $concatHouseover .= '<tr>';
                $concatHouseover .= '<td class="wntHOImgCol" valign="top" style="vertical-align:top;">';
                $concatHouseover .= '<div class="wolfnet_wntHOImg">';
                $concatHouseover .= '<img src="' . $listing->thumbnail_url . '" style="max-height:100px;width:auto">';
                $concatHouseover .= '</div>';
                if ($showBrokerImage) {
                    $concatHouseover .= '<div class="wolfnet_wntHOBroker" style="text-align: center">';
                    $concatHouseover .= '<img src="' . $listing->branding->brokerLogo . '" style="max-height:50px;width:auto" alt="Broker Reciprocity">';
                    $concatHouseover .= '</div>';
                }
                $concatHouseover .= '</td>';
                $concatHouseover .= '<td valign="top" style="vertical-align:top;">';
                $concatHouseover .= '<div class="wolfnet_wntHOContentContainer">';
                $concatHouseover .= '<div style="text-align:left;font-weight:bold">' . $listing->listing_price;
                $concatHouseover .= '</div>';
                $concatHouseover .= '<div style="text-align:left;">' . $listing->display_address;
                $concatHouseover .= '</div>';
                $concatHouseover .= '<div style="text-align:left;">' . $listing->city . ', ' . $listing->state;
                $concatHouseover .= '</div>';
                $concatHouseover .= '<div style="text-align:left;">' . $listing->bedsbaths;
                $concatHouseover .= '</div>';
                $concatHouseover .= '<div style="text-align:left;padding-top:20px;">' . $listing->branding->content;
                $concatHouseover .= '</div>';
                $concatHouseover .= '</div>';
                $concatHouseover .= '</td>';
                $concatHouseover .= '</tr>';
                $concatHouseover .= '</tbody>';
                $concatHouseover .= '</table>';
                $concatHouseover .= '</div>';
                $concatHouseover .= '</div>';
                $concatHouseover .= '</a>';
            }

            array_push($houseoverData, array(
                'lat'        => $listing->lat,
                'lng'        => $listing->lng,
                'content'    => $concatHouseover,
                'propertyId' => $listing->property_id,
                'propertyUrl'=> $listing->property_url
                ));
        }

        return $houseoverData;

    }


    private function getMaxResults($productKey)
    {
        $url  = $this->serviceUrl . '/setting/' . $productKey . '.json'
              . '?setting=site_text';
        $data = $this->getApiData($url, 86400)->site_text;
        $maxResults = (property_exists($data, 'Max Results')) ? $data->{'Max Results'} : '';

        return (is_numeric($maxResults)) ? $maxResults : 250;

    }


    /**
    * Multi-byte chr(): Will turn a numeric argument into a UTF-8 string.
    *
    * @param mixed $num
    * @return string
    */
    private function chr_utf8($num)
    {
        if ($num < 128) {
            return chr($num);
        }

        if ($num < 2048) {
            return chr(($num >> 6) + 192) . chr(($num & 63) + 128);
        }

        if ($num < 65536) {
            return chr(($num >> 12) + 224) . chr((($num >> 6) & 63) + 128) . chr(($num & 63) + 128);
        }

        if ($num < 2097152) {
            return chr(($num >> 18) + 240) . chr((($num >> 12) & 63) + 128)
                . chr((($num >> 6) & 63) + 128) . chr(($num & 63) + 128);
        }

        return '';

    }


}
