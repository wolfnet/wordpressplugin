<?php

/**
 * This class is the Settings Service and is a Facade used to interact with all other
 * market information.
 *
 * @package       com.wolfnet.wordpress
 * @subpackage    settings
 * @title         service.php
 * @extends       com_greentiedev_wppf_abstract_service
 * @implements    com_greentiedev_wppf_interface_iService
 * @singleton     True
 * @contributors  AJ Michels (aj.michels@wolfnet.com)
 * @version       1.0
 * @copyright     Copyright (c) 2012, WolfNet Technologies, LLC
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
 *
 */
class WNT_WP_Service_SettingsService
{


    /* PROPERTIES ******************************************************************************* */

    private $apiUrl;


    /* PUBLIC METHODS *************************************************************************** */

    /**
     * This method returns all featured property listings.
     *
     * @param   string   $type  The type of disclaimer to return. (search_results)
     * @return  array    An array of listing objects (com_wolfnet_wordpress_listing_entity)
     *
     */
    public function getSettings ()
    {
        $productKey = get_option('wolfnet_productKey');
        $url = $this->getApiUrl() . '/setting/' . $productKey . '.json?setting=getallsettings';
        $http = wp_remote_get($url);
        $data = (!is_wp_error($http) && $http['response']['code'] == '200') ? json_decode($http['body'], true) : array();

        return $data['settings'];

    }


    public function getSiteBaseUrl ()
    {
        $productKey = get_option('wolfnet_productKey');
        $url = $this->getApiUrl() . '/setting/' . $productKey . '.json?setting=SITE_BASE_URL';
        $http = wp_remote_get($url);
        $data = (!is_wp_error($http) && $http['response']['code'] == '200') ? json_decode($http['body'], true) : array();

        if (array_key_exists('site_base_url', $data)) {
            return $data['site_base_url'];
        }
        else {
            return '';
        }

    }


    public function isKeyValid($key=null)
    {
        $isValid = false;
        $productKey = ($key != null) ? $key : get_option('wolfnet_productKey');
        $url = $this->getApiUrl() . '/validateKey/' . $productKey . '.json';
        $http = wp_remote_get($url, array('timeout'=>180));
        $data = (!is_wp_error($http) && $http['response']['code'] == '200') ? json_decode($http['body']) : array();


        if (property_exists($data, 'error') && property_exists($data->error, 'status') && $data->error->status === false) {
            $isValid = true;
        }

        return $isValid;

    }


    /* ACCESSOR METHODS ************************************************************************* */

    public function getApiUrl()
    {
        return $this->apiUrl;
    }


    public function setApiUrl($url)
    {
        $this->apiUrl = $url;
    }


}
