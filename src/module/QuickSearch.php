<?php

/**
 * WolfNet QuickSearch module
 *
 * This module represents the quick search and its related assets and functions.
 *
 * @package Wolfnet
 * @copyright 2015 WolfNet Technologies, LLC.
 * @license GPLv2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 *
 */
class Wolfnet_Module_QuickSearch
{
    /**
    * This property holds the current instance of the Wolfnet_Plugin.
    * @var Wolfnet_Plugin
    */
    protected $plugin = null;


    public function __construct($plugin) {
        $this->plugin = $plugin;
    }


	public function scQuickSearch($attrs, $content = '')
    {
        try {
            $defaultAttributes = $this->getDefaults();

            $criteria = array_merge($defaultAttributes, (is_array($attrs)) ? $attrs : array());

            $this->plugin->decodeCriteria($criteria);

            $out = $this->quickSearch($criteria);

        } catch (Wolfnet_Exception $e) {
            $out = $this->plugin->displayException($e);
        }

        return $out;
    }


    public function getDefaults()
    {
        return array(
            'title'      => 'QuickSearch',
            'keyid'      => '',
            'keyids'     => '',
            'view'       => '',
            'routing'    => '',
            'smartsearch'=> false,
        );
    }


    public function getOptions($instance = null)
    {
        $options = $this->plugin->getOptions($this->getDefaults(), $instance);

        $options['smartsearch_false_wps'] = selected($options['smartsearch'], 'false', false);
        $options['smartsearch_true_wps']  = selected($options['smartsearch'], 'true', false);

        return $options;
    }


    public function routeQuickSearch($formData)
    {
        /*
         * Loop over each key and get the number of matching listings for each.
         * We'll save the key with the highest number of matches so we can route
         * to the site associated with that key.
         */
        $highestCount = 0;
        $highestMatchKey = '';

        foreach (explode(',', $formData['keyids']) as $keyID) {
            try {
                $key = $this->plugin->keyService->getById($keyID);

                $listings = $this->plugin->api->sendRequest(
                    $key,
                    '/listing?detaillevel=1&startrow=1&maxrows=1',
                    'GET',
                    $formData
                );
                $count = $listings['responseData']['data']['total_rows'];

                if($count > $highestCount) {
                    $highestCount = $count;
                    $highestMatchKey = $key;
                }
            } catch (Wolfnet_Exception $e) {
                echo $this->plugin->displayException($e);
            }
        }

        /*
         * Route to the site associated with key determined above.
        */
        $baseUrl = $this->plugin->data->getBaseUrl($highestMatchKey);

        $redirect = $baseUrl . "?";
        foreach($formData as $key => $param) {
            $redirect .= $key . "=" . $param . "&";
        }

        return $redirect;
    }


    /**
     * Get markup for Quick Search form
     * @param  array  $criteria
     * @return string form markup
     */
    public function quickSearch(array $criteria)
    {
        if (array_key_exists("keyids", $criteria) && !empty($criteria['keyids'])) {
            $keyids = explode(",", $criteria["keyids"]);
        } else {
            $keyids[0] = 1;
        }

        if (count($keyids) == 1) {
            $productKey = $this->plugin->keyService->getById($keyids[0]);
        } else {
            $productKey = $this->plugin->keyService->getDefault();
        }

        if (is_wp_error($productKey)) {
            return $this->plugin->getWpError($productKey);
        }

        // Get data
        $prices = $this->plugin->data->getPrices($productKey);
        $beds = $this->plugin->data->getBeds();
        $baths = $this->plugin->data->getBaths();
        $formAction = $this->plugin->data->getBaseUrl($productKey);
        $markets = $this->plugin->keyService->get();

        if (is_wp_error($prices)) {
            return $this->plugin->getWpError($prices);
        }

        if (is_wp_error($beds)) {
            return $this->plugin->getWpError($beds);
        }

        if (is_wp_error($baths)) {
            return $this->plugin->getWpError($baths);
        }

        if (is_wp_error($formAction)) {
            return $this->plugin->getWpError($formAction);
        }

        if (is_wp_error($markets)) {
            return $this->plugin->getWpError($markets);
        }

        $vars = array(
            'instance_id'  => str_replace('.', '', 'wolfnet_quickSearch_' . $this->plugin->createUUID()),
            'siteUrl'      => site_url(),
            'keyids'       => $keyids,
            'markets'      => json_decode($markets),
            'prices'       => $prices,
            'beds'         => $beds,
            'baths'        => $baths,
            'formAction'   => $formAction,
            );

        $args = $this->plugin->convertDataType(array_merge($criteria, $vars));

        return $this->plugin->views->quickSearchView($args);
    }
}
?>
