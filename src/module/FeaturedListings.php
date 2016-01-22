<?php

/**
 * WolfNet Featured Listings module
 *
 * This module represents the featured listings and its related assets and functions.
 *
 * @package Wolfnet
 * @copyright 2015 WolfNet Technologies, LLC.
 * @license GPLv2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 *
 */
class Wolfnet_Module_FeaturedListings
{
	/**
    * This property holds the current instance of the Wolfnet_Plugin.
    * @var Wolfnet_Plugin
    */
    protected $plugin = null;


    public function __construct($plugin) {
        $this->plugin = $plugin;
    }


    public function scFeaturedListings($attrs, $content = '')
    {
        try {
            $defaultAttributes = $this->getDefaults();

            $criteria = array_merge($defaultAttributes, (is_array($attrs)) ? $attrs : array());

            $this->plugin->decodeCriteria($criteria);

            $out = $this->featuredListings($criteria);

        } catch (Wolfnet_Exception $e) {
            $out = $this->plugin->displayException($e);
        }

        return $out;
    }


    public function getDefaults()
    {
        return array(
            'title'      => '',
            'direction'  => 'left',
            'autoplay'   => true,
            'speed'      => 5,
            'ownertype'  => 'agent_broker',
            'maxresults' => 50,
            'numrows'    => 50,
            'startrow'   => 1,
            'keyid'      => '',
        );
    }


    public function getOptions($instance = null)
    {
        $options = $this->plugin->getOptions($this->getDefaults(), $instance);

        $options['autoplay_false_wps']  = selected($options['autoplay'], 'false', false);
        $options['autoplay_true_wps']   = selected($options['autoplay'], 'true', false);
        $options['direction_left_wps']  = selected($options['direction'], 'left', false);
        $options['direction_right_wps'] = selected($options['direction'], 'right', false);
        $options['ownertypes']          = $this->plugin->getOwnerTypes();

        return $options;
    }


    public function featuredListings(array $criteria)
    {
        $key = $this->plugin->keyService->getFromCriteria($criteria);

        if (!$this->plugin->keyService->isSaved($key)) {
            return false;
        }

        if (!array_key_exists('startrow', $criteria)) {
            $criteria['startrow'] = 1;
        }

        $qdata = $this->plugin->prepareListingQuery($criteria);

        try {
            $data = $this->plugin->apin->sendRequest($key, '/listing', 'GET', $qdata);
        } catch (Wolfnet_Exception $e) {
            return $this->plugin->displayException($e);
        }

        $this->plugin->augmentListingsData($data, $key);

        $listingsData = array();

        if (is_array($data['responseData']['data'])) {
            $listingsData = $data['responseData']['data']['listing'];
        }

        $listingsHtml = '';


        foreach ($listingsData as &$listing) {
            $vars = array(
                'listing' => $listing
                );

            $listingsHtml .= $this->plugin->views->listingView($vars);

        }

        $_REQUEST['wolfnet_includeDisclaimer'] = true;
        $_REQUEST[$this->plugin->requestPrefix.'productkey'] = $key;

        // Keep a running array of product keys so we can output all necessary disclaimers
        if (!array_key_exists('keyList', $_REQUEST)) {
            $_REQUEST['keyList'] = array();
        }

        if (!in_array($_REQUEST[$this->plugin->requestPrefix.'productkey'], $_REQUEST['keyList'])) {
            array_push($_REQUEST['keyList'], $_REQUEST[$this->plugin->requestPrefix.'productkey']);
        }

        $vars = array(
            'instance_id'  => str_replace('.', '', uniqid('wolfnet_featuredListing_')),
            'listingsHtml' => $listingsHtml,
            'siteUrl'      => site_url(),
            'criteria'     => json_encode($criteria)
            );

        $args = $this->plugin->convertDataType(array_merge($criteria, $vars));

        return $this->plugin->views->featuredListingView($args);
    }
}

?>