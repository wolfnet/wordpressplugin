<?php

/**
 * WolfNet Data
 *
 * This class represents the search configuration and listing data and associated
 * functions to retrieve it.
 *
 * @package Wolfnet
 * @copyright 2015 WolfNet Technologies, LLC.
 * @license GPLv2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 *
 */
class Wolfnet_Data
{
	/**
    * This property holds the current instance of the Wolfnet_Plugin.
    * @var Wolfnet_Plugin
    */
    protected $plugin = null;


    public function __construct($plugin) {
        $this->plugin = $plugin;
    }


    /**
     * This method returns an array of integer values to be used as possible pagination item counts.
     * @return array An array of integers.
     */
    public function getItemsPerPage()
    {
        return array(5,10,15,20,25,30,35,40,45,50);
    }


    public function getMarketName($apiKey)
    {
        $data = $this->plugin->api->sendRequest($apiKey, '/settings');

        return $data['responseData']['data']['market']['datasource_name'];
    }


    public function soldListingsEnabled()
    {
        try {
            $data = $this->plugin->api->sendRequest(
                $this->plugin->keyService->getDefault(),
                '/settings',
                'GET'
            );
        } catch(Wolfnet_Exception $e) {
            return $this->plugin->displayException($e);
        }

        $marketEnabled = $data['responseData']['data']['market']['has_sold_property'];
        $siteEnabled = $data['responseData']['data']['site']['sold_property_enabled'];

        return ($marketEnabled && $siteEnabled);
    }


    public function getOffices()
    {
        try {
            $data = $this->plugin->api->sendRequest(
                $this->plugin->keyService->getDefault(),
                '/office',
                'GET'
            );
        } catch (Wolfnet_Exception $e) {
            return $this->plugin->displayException($e);
        }

        return $data;
    }


    public function getMap($listingsData, $keyid, $productKey = null)
    {
        return $this->plugin->views->mapView($listingsData, $keyid, $productKey);
    }


    public function getHideListingTools($hideId, $showId, $collapseId, $instance_id)
    {
        return $this->plugin->views->hideListingsToolsView(
        	$hideId, $showId, $collapseId, $instance_id
        );
    }


    public function getToolbar($data, $class)
    {
        $args = array_merge($data['wpMeta'], array(
            'toolbarClass' => $class . ' ',
            'maxresults'   => $data['maxresults'], // total results on all pages
            'numrows'      => $data['wpMeta']['maxresults'], // total results per page
            'prevClass'    => ($data['wpMeta']['startrow'] <= 1) ? 'wolfnet_disabled' : '',
            'lastitem'     => $data['wpMeta']['startrow'] + $data['wpMeta']['maxresults'] - 1,
            'action'       => 'wolfnet_listings'
        ));

        if ($args['total_rows'] < $args['maxresults']) {
            $args['maxresults'] = $args['total_rows'];
        }

        $args['nextClass'] = ($args['lastitem'] >= $args['maxresults']) ? 'wolfnet_disabled' : '';

        if ($args['lastitem'] > $args['total_rows']) {
            $args['lastitem'] = $args['total_rows'];
        }

        $prev = $args['startrow'] - $args['numrows'];

        if ($prev < 1) {
            $prev = $prev - $args['numrows'] + 1;
        }

        if ($prev < 1) {
            $prev = $args['startrow'];
        }

        $args['prevLink'] = $this->buildUrl(
            admin_url('admin-ajax.php'),
            array_merge($args, array('startrow'=>$prev))
        );

        $next = $args['startrow'] + $args['numrows'];

        if ($next >= $args['maxresults']) {
            $next = 1;
        }

        $args['nextLink']  = $this->buildUrl(
            admin_url('admin-ajax.php'),
            array_merge($args, array('startrow'=>$next))
        );

        $args = $this->plugin->convertDataType($args);

        return $this->plugin->views->toolbarView($args);
    }


    public function buildUrl($url = '', array $params = array())
    {
        if (!strstr($url, '?')) {
            $url .= '?';
        }

        $restrictedParams = array('criteria','toolbarTop','toolbarBottom','listingsHtml','prevLink',
            'nextLink','prevClass','nextClass','toolbarClass','instance_id','siteUrl','class','_','key');

        $restrictedSuffix = array('_wpid', '_wpname', '_wps', '_wpc');

        foreach ($params as $key => $value) {
            $valid = true;
            $valid = (array_search($key, $restrictedParams) !== false) ? false : $valid;
            $valid = (!is_string($value) && !is_numeric($value) && !is_bool($value)) ? false : $valid;

            foreach ($restrictedSuffix as $suffix) {
                $valid = (substr($key, strlen($suffix)*-1) == $suffix) ? false : $valid;
            }

            if ($valid) {
                $url .= '&' . $key . '=' . urlencode($this->plugin->htmlEntityDecodeNumeric($value));
            }

        }

        return $url;

    }


    /**
     * get the api display setting for "Max Results". If it is not set use 250
     * @param  string $productKey
     * @return int
     */
    public function getMaxResults($productKey = null)
    {
        if ($productKey == null) {
            $productKey = json_decode($this->plugin->keyService->getDefault());
        }

        $data = $this->plugin->api->sendRequest($productKey, '/settings');

        $maxResults = $data['responseData']['data']['market']['display_rules']['Max Results'];

        return (is_numeric($maxResults) && $maxResults <= 250 ) ? $maxResults : 250;
    }


    /**
     * Get the Broker Reciprocity Logo. returns array containing url, height, width $alt text
     * @param  string $productKey
     * @return array               keys: "SRC", "ALT", "HEIGHT", "WIDTH"
     */
    public function getBrLogo($productKey = null)
    {
        if ($productKey == null) {
            $productKey = json_decode($this->plugin->keyService->getDefault());
        }

        $data = $this->plugin->api->sendRequest($productKey, '/settings');

        return $data['responseData']['data']['market']['broker_reciprocity_logo'];
    }


    public function getMaptracksEnabled($productKey = null)
    {

        if ($productKey == null) {
            $productKey = json_decode($this->plugin->keyService->getDefault());
        }

        $data = $this->plugin->api->sendRequest($productKey, '/settings');

        if (is_wp_error($data)) {
            return $data;
        }

        return ($data['responseData']['data']['site']['maptracks_enabled'] == 'Y');
    }


	public function sendMapTrack($productKey=null, $map_data=array())
	{
		if ($productKey == null) {
			$productKey = json_decode($this->plugin->keyService->getDefault());
		}

		$result = $this->plugin->api->sendRequest($productKey, '/user/map_track', 'POST', $map_data);

		return $result;

	}


    public function getOwnerTypes()
    {
        return array(
            array('value'=>'agent_broker', 'label'=>'Agent Then Broker'),
            array('value'=>'agent', 'label'=>'Agent Only'),
            array('value'=>'broker', 'label'=>'Broker Only')
        );
    }


    public function getSpeedSettings()
    {
        return array(
            array('value'=>'slow', 'label'=>'Slow'),
            array('value'=>'medium', 'label'=>'Medium'),
            array('value'=>'fast', 'label'=>'Fast')
        );
    }


    public function getMapTypes()
    {
        return array(
            array('value'=>'disabled', 'label'=>'No'),
            array('value'=>'above',    'label'=>'Above Listings'),
            array('value'=>'below',    'label'=>'Below Listings'),
            array('value'=>'map_only', 'label'=>'Map Only')
        );
    }


    public function getMapParameters($listingsData, $productKey = null)
    {
        if ($productKey == null) {
            $productKey = $this->plugin->keyService->getDefault();
        }

        $data  = $this->plugin->api->sendRequest($productKey, '/settings');

        if (is_wp_error($data)) {
            return $this->plugin->getWpError($data);
        }

		$args['keyid'] = $this->plugin->keyService->getIdByKey($productKey);

        $args['mapParams'] = array(
            'centerLat'    => $data['responseData']['data']['market']['maptracks']['map_start_lat'],
            'centerLng'    => $data['responseData']['data']['market']['maptracks']['map_start_lng'],
            'zoomLevel'    => $data['responseData']['data']['market']['maptracks']['map_start_scale'],
            'houseoverIcon'=> $GLOBALS['wolfnet']->url . 'img/houseover.png',
            'mapId'        => 'wntMapTrack' . $this->plugin->createUUID(),
            'hideMapId'    => 'hideMap' . $this->plugin->createUUID(),
            'showMapId'    => 'showMap' . $this->plugin->createUUID(),
            'tlBoundLng'   => $data['responseData']['data']['market']['maptracks']['bounds_tl_lng'],
            'brBoundLat'   => $data['responseData']['data']['market']['maptracks']['bounds_br_lat'],
            'tlBoundLat'   => $data['responseData']['data']['market']['maptracks']['bounds_tl_lat'],
            'brBoundLng'   => $data['responseData']['data']['market']['maptracks']['bounds_br_lng'],
		);

        $args['houseoverData'] = $this->getHouseoverData(
            $listingsData,
            $data['responseData']['data']['resource']['searchResults']['allLayouts']['showBrokerReciprocityLogo']
        );

        return $args;
    }


    public function getHouseoverData($listingsData, $showBrokerImage)
    {

        $houseoverData = array();

        foreach ($listingsData as $listing) {
            $vars = array(
                'listing' => $listing,
                'showBrokerImage' => $showBrokerImage,
            );

            $concatHouseover = $this->plugin->views->houseOver($vars);

            array_push($houseoverData, array(
                'lat' => $listing['geo']['lat'],
                'lng' => $listing['geo']['lng'],
                'content' => $concatHouseover,
                'propertyId' => $listing['property_id'],
                'propertyUrl' => $listing['property_url'],
                ));
        }

        return $houseoverData;
    }


    /**
     * Get the wolfnet search url associated with given procuct key
     * @param  string   $productKey
     * @return string   base URL of the Wolfnet search solution
     */
    public function getBaseUrl($productKey = null)
    {
        if ($productKey == null) {
            $productKey = $this->plugin->keyService->getDefault();
        }

        $data  = $this->plugin->api->sendRequest($productKey, '/settings');

        if (is_wp_error($data)) {
            return $data;
        }

        return $data['responseData']['data']['site']['site_base_url'];
    }


    /**
     * Dynamic url for the wolfnet search url to be used with Search Manager
     * @param  string   $productKey
     * @return string   base URL of the Wolfnet search solution
     */
    public function getSearchManagerBaseUrl($productKey = null)
    {
        if ($productKey == null) {
            $productKey = $this->plugin->keyService->getDefault();
        }

        $data  = $this->plugin->api->sendRequest($productKey, '/settings');

        if(is_wp_error($data)) {
            return $data;
        }

        $baseUrl =
            $data['responseData']['data']['site']['mlsfinder_web_root'] . '/' .
            $data['responseData']['data']['market']['datasource_name'] . '/' .
            $data['responseData']['data']['site']['site_directory_name'];

        return $baseUrl;
    }


    public function getPrices($productKey)
    {
        $data = $this->plugin->api->sendRequest($productKey, '/search_criteria/property_feature');

        if (is_wp_error($data)) {
            return $data->get_error_message();
        }

        $prices = array();
        $prices['max_price'] = $data['responseData']['data']['max_price'];
        $prices['min_price'] = $data['responseData']['data']['min_price'];

        return $prices;
    }


    public function getBeds()
    {
        $values = array(1,2,3,4,5,6,7);
        $data   = array();

        foreach ($values as $value) {
            $data[] = array('value'=>$value, 'label'=>$value);
        }

        return $data;
    }


    public function getBaths()
    {
        return $this->getBeds();
    }


	public function isCanada()
	{
		$data = $this->plugin->api->sendRequest(
			$this->plugin->keyService->getDefault(),
			'/settings'
		);

		return $data['responseData']['data']['resource']['country'] == 'Canada' ? true : false;
	}

}
