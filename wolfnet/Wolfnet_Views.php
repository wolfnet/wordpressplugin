<?php

/**
 * @title         Wolfnet_Views.php
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

class Wolfnet_Views
{


    /* Constructor Method *********************************************************************** */
    /*   ____                _                   _                                                */
    /*  / ___|___  _ __  ___| |_ _ __ _   _  ___| |_ ___  _ __                                    */
    /* | |   / _ \| '_ \/ __| __| '__| | | |/ __| __/ _ \| '__|                                   */
    /* | |__| (_) | | | \__ \ |_| |  | |_| | (__| || (_) | |                                      */
    /*  \____\___/|_| |_|___/\__|_|   \__,_|\___|\__\___/|_|                                      */
    /*                                                                                            */
    /* ****************************************************************************************** */

    function __construct($wolfnet)
    {
        $this->wolfnet = $wolfnet;
    }


    /* Public Methods *************************************************************************** */
    /*  ____        _     _ _        __  __      _   _               _                            */
    /* |  _ \ _   _| |__ | (_) ___  |  \/  | ___| |_| |__   ___   __| |___                        */
    /* | |_) | | | | '_ \| | |/ __| | |\/| |/ _ \ __| '_ \ / _ \ / _` / __|                       */
    /* |  __/| |_| | |_) | | | (__  | |  | |  __/ |_| | | | (_) | (_| \__ \                       */
    /* |_|    \__,_|_.__/|_|_|\___| |_|  |_|\___|\__|_| |_|\___/ \__,_|___/                       */
    /*                                                                                            */
    /* ****************************************************************************************** */

    /* Admin Menus ****************************************************************************** */
    /*                                                                                            */
    /*  /\   _| ._ _  o ._    |\/|  _  ._       _                                                 */
    /* /--\ (_| | | | | | |   |  | (/_ | | |_| _>                                                 */
    /*                                                                                            */
    /* ****************************************************************************************** */

    public function amSettingsPage()
    {
        ob_start(); settings_fields($this->wolfnet->optionGroup); $formHeader = ob_get_clean();
        $productKey = json_decode($this->wolfnet->getProductKey());

        // add the market name
        for($i=1; $i<=count($productKey); $i++) {
            $productKey[$i-1]->market = strtoupper($this->wolfnet->api->getMarketName($productKey[$i-1]->key));
        }

        include $this->wolfnet->dir . '/template/adminSettings.php';

    }


    public function amEditCssPage()
    {
        ob_start(); settings_fields($this->wolfnet->CssOptionGroup); $formHeader = ob_get_clean();
        $publicCss = $this->getPublicCss();
        $adminCss = $this->wolfnet->admin->getAdminCss();

        include $this->wolfnet->dir .'/template/adminEditCss.php';

    }


    public function amSearchManagerPage()
    {
        $key = (array_key_exists("keyid", $_REQUEST)) ? $_REQUEST["keyid"] : "1";
        $productkey = $this->wolfnet->getProductKeyById($key);

        if (!$this->wolfnet->api->productKeyIsValid($productkey)) {
            include $this->wolfnet->dir .'/template/invalidProductKey.php';
            return;
        }
        else {

            $searchForm = ($this->wolfnet->smHttp !== null) ? $this->wolfnet->smHttp['body'] : '';
            $markets = json_decode($this->wolfnet->getProductKey());
            $selectedKey = $key;
            $url = $this->wolfnet->url;
            include $this->wolfnet->dir .'/template/adminSearchManager.php';

        }


    }


    public function amSupportPage()
    {
        $imgdir = $this->wolfnet->url . 'img/';
        include $this->wolfnet->dir .'/template/adminSupport.php';

    }


    public function getPublicCss()
    {
        return get_option(trim($this->wolfnet->publicCssOptionKey));
    }


    /**
     * This method is used in the context of admin_print_styles to output custom CSS.
     * @return void
     */
    public function adminPrintStyles()
    {
        $adminCss = $this->wolfnet->getAdminCss();
        echo '<style>' . $adminCss . '</style>';

    }


    /* Views ************************************************************************************ */
    /*                                                                                            */
    /* \  / o  _        _                                                                         */
    /*  \/  | (/_ \/\/ _>                                                                         */
    /*                                                                                            */
    /* ****************************************************************************************** */

    public function featuredListingsOptionsFormView(array $args=array())
    {
        $defaultArgs = array(
            'instance_id'     => str_replace('.', '', uniqid('wolfnet_featuredListing_')),
            'markets'         => json_decode($this->wolfnet->getProductKey()),
            'selectedKey'     => (array_key_exists("keyid", $_REQUEST)) ? $_REQUEST["keyid"] : "1",
            );

        $args = array_merge($defaultArgs, $args);

        return $this->parseTemplate('template/featuredListingsOptions.php', $args);

    }


    public function listingGridOptionsFormView(array $args=array())
    {
        $defaultArgs = array(
            'instance_id'      => str_replace('.', '', uniqid('wolfnet_listingGrid_')),
            'markets'          => json_decode($this->wolfnet->getProductKey()),
            'keyid'            => ''
            );

        $args = array_merge($defaultArgs, $args);

        $args['criteria'] = esc_attr($args['criteria']);

        return $this->parseTemplate('template/listingGridOptions.php', $args);

    }


    public function quickSearchOptionsFormView(array $args=array())
    {
        $markets = json_decode($this->wolfnet->getProductKey());
        $keyids = array();
        foreach($markets as $market) {
            array_push($keyids, $market->id);
        }
        $defaultArgs = array(
            'instance_id' => str_replace('.', '', uniqid('wolfnet_quickSearch_')),
            'markets'     => $markets,
            'keyids'      => $keyids,
            );


        $args = array_merge($defaultArgs, $args);

        return $this->parseTemplate('template/quickSearchOptions.php', $args);

    }


    public function listingView(array $args=array())
    {
        foreach ($args as $key => $item) {
            $args[$key] = apply_filters('wolfnet_listingView_' . $key, $item);
        }

        ob_start();
        echo $this->parseTemplate('template/listing.php', $args);

        return apply_filters('wolfnet_listingView', ob_get_clean());

    }


    public function listingBriefView(array $args=array())
    {
        foreach ($args as $key => $item) {
            $args[$key] = apply_filters('wolfnet_listingBriefView_' . $key, $item);
        }

        ob_start();
        echo $this->parseTemplate('template/briefListing.php', $args);

        return apply_filters('wolfnet_listingBriefView', ob_get_clean());

    }


    public function listingResultsView(array $args=array())
    {
        foreach ($args as $key => $item) {
            $args[$key] = apply_filters('wolfnet_listingResultsView_' . $key, $item);
        }

        ob_start();
        echo $this->parseTemplate('template/resultsListing.php', $args);

        return apply_filters('wolfnet_listingResultsView', ob_get_clean());

    }


    public function featuredListingView(array $args=array())
    {
        foreach ($args as $key => $item) {
            $args[$key] = apply_filters('wolfnet_featuredListingView_' . $key, $item);
        }

        ob_start();
        echo $this->parseTemplate('template/featuredListings.php', $args);

        return apply_filters('wolfnet_featuredListingView', ob_get_clean());

    }


    public function propertyListView(array $args=array())
    {
        if(!array_key_exists('keyid', $args)) {
            $args['productkey'] = $this->wolfnet->getDefaultProductKey();
        } else {
            $args['productkey'] = $this->wolfnet->getProductKeyById($args["keyid"]);
        }
        $args['itemsPerPage'] = $this->wolfnet->getItemsPerPage();
        $args['sortOptions'] = $this->wolfnet->api->getSortOptions($args['productkey']);

        foreach ($args as $key => $item) {
            $args[$key] = apply_filters('wolfnet_propertyListView_' . $key, $item);
        }

        ob_start();
        echo $this->parseTemplate('template/propertyList.php', $args);

        return apply_filters('wolfnet_propertyListView', ob_get_clean());

    }


    public function listingGridView(array $args=array())
    {

        if(!array_key_exists('keyid', $args)) {
            $args['productkey'] = $this->wolfnet->getDefaultProductKey();
        } else {
            $args['productkey'] = $this->wolfnet->getProductKeyById($args["keyid"]);
        }
        $args['itemsPerPage'] = $this->wolfnet->getItemsPerPage();
        $args['sortOptions'] = $this->wolfnet->api->getSortOptions($args['productkey']);

        foreach ($args as $key => $item) {
            $args[$key] = apply_filters('wolfnet_listingGridView_' . $key, $item);
        }

        ob_start();
        echo $this->parseTemplate('template/listingGrid.php', $args);

        return apply_filters('wolfnet_listingGridView', ob_get_clean());

    }


    public function quickSearchView(array $args=array())
    {
        foreach ($args as $key => $item) {
            $args[$key] = apply_filters( 'wolfnet_quickSearchView_' . $key, $item );
        }

        ob_start();
        echo $this->parseTemplate('template/quickSearch.php', $args);

        return apply_filters('wolfnet_quickSearchView', ob_get_clean());

    }


    public function mapView($listingsData, $productKey=null)
    {
        ob_start();
        $args = $this->wolfnet->api->getMapParameters($listingsData, $productKey);
        $args["url"] = $this->wolfnet->url;

        echo $this->parseTemplate('template/map.php', $args);

        return apply_filters('wolfnet_mapView', ob_get_clean());

    }


    public function hideListingsToolsView($hideId,$showId,$collapseId,$instance_id)
    {
        ob_start();

        $args['hideId'] = $hideId;
        $args['showId'] = $showId;
        $args['collapseId'] = $collapseId;
        $args['instance_id'] = $instance_id;

        echo $this->parseTemplate('template/hideListingsTools.php', $args);

        return apply_filters('wolfnet_hideListingsTools', ob_get_clean());

    }


    public function toolbarView(array $args=array())
    {
        foreach ($args as $key => $item) {
            $args[$key] = apply_filters('wolfnet_toolbarView_' . $key, $item);
        }

        ob_start();
        echo $this->parseTemplate('template/toolbar.php', $args);

        return apply_filters('wolfnet_toolbarView', ob_get_clean());

    }


    /* PRIVATE METHODS ************************************************************************** */
    /*  ____       _            _         __  __      _   _               _                       */
    /* |  _ \ _ __(_)_   ____ _| |_ ___  |  \/  | ___| |_| |__   ___   __| |___                   */
    /* | |_) | '__| \ \ / / _` | __/ _ \ | |\/| |/ _ \ __| '_ \ / _ \ / _` / __|                  */
    /* |  __/| |  | |\ V / (_| | ||  __/ | |  | |  __/ |_| | | | (_) | (_| \__ \                  */
    /* |_|   |_|  |_| \_/ \__,_|\__\___| |_|  |_|\___|\__|_| |_|\___/ \__,_|___/                  */
    /*                                                                                            */
    /* ****************************************************************************************** */

    private function parseTemplate($template, array $vars=array())
    {
        extract($vars, EXTR_OVERWRITE);
        ob_start();

        include $this->wolfnet->dir .'/'. $template;

        return ob_get_clean();

    }


}
