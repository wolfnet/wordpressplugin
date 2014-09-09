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
        ob_start(); settings_fields($GLOBALS['wolfnet']->optionGroup); $formHeader = ob_get_clean();
        $productKey = json_decode($GLOBALS['wolfnet']->getProductKey());

        // add the market name
        for($i=1; $i<=count($productKey); $i++) {
            $productKey[$i-1]->market = strtoupper($GLOBALS['wolfnet']->api->getMarketName($productKey[$i-1]->key));
        }

        include $GLOBALS['wolfnet']->dir . '/template/adminSettings.php';

    }


    public function amEditCssPage()
    {
        ob_start(); settings_fields($GLOBALS['wolfnet']->CssOptionGroup); $formHeader = ob_get_clean();
        $publicCss = $this->getPublicCss();
        $adminCss = $GLOBALS['wolfnet']->admin->getAdminCss();

        include $GLOBALS['wolfnet']->dir .'/template/adminEditCss.php';

    }


    public function amSearchManagerPage()
    {
        $key = (array_key_exists("keyid", $_REQUEST)) ? $_REQUEST["keyid"] : "1";
        $productkey = $GLOBALS['wolfnet']->getProductKeyById($key);

        if (!$GLOBALS['wolfnet']->api->productKeyIsValid($productkey)) {
            include $GLOBALS['wolfnet']->dir .'/template/invalidProductKey.php';
            return;
        }
        else {

            $searchForm = ($GLOBALS['wolfnet']->smHttp !== null) ? $GLOBALS['wolfnet']->smHttp['body'] : '';
            $markets = json_decode($GLOBALS['wolfnet']->getProductKey());
            $selectedKey = $key;
            $url = $GLOBALS['wolfnet']->url;
            include $GLOBALS['wolfnet']->dir .'/template/adminSearchManager.php';

        }


    }


    public function amSupportPage()
    {
        $imgdir = $GLOBALS['wolfnet']->url . 'img/';
        include $GLOBALS['wolfnet']->dir .'/template/adminSupport.php';

    }


    public function getPublicCss()
    {
        return get_option(trim($GLOBALS['wolfnet']->publicCssOptionKey));
    }


    /**
     * This method is used in the context of admin_print_styles to output custom CSS.
     * @return void
     */
    public function adminPrintStyles()
    {
        $adminCss = $GLOBALS['wolfnet']->getAdminCss();
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
            'markets'         => json_decode($GLOBALS['wolfnet']->getProductKey()),
            'selectedKey'     => (array_key_exists("keyid", $_REQUEST)) ? $_REQUEST["keyid"] : "1",
            );

        $args = array_merge($defaultArgs, $args);

        return $this->parseTemplate('template/featuredListingsOptions.php', $args);

    }


    public function listingGridOptionsFormView(array $args=array())
    {
        $defaultArgs = array(
            'instance_id'      => str_replace('.', '', uniqid('wolfnet_listingGrid_')),
            'markets'          => json_decode($GLOBALS['wolfnet']->getProductKey()),
            'keyid'            => ''
            );

        $args = array_merge($defaultArgs, $args);

        $args['criteria'] = esc_attr($args['criteria']);

        return $this->parseTemplate('template/listingGridOptions.php', $args);

    }


    public function quickSearchOptionsFormView(array $args=array())
    {
        $markets = json_decode($GLOBALS['wolfnet']->getProductKey());
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
            $args['productkey'] = $GLOBALS['wolfnet']->getDefaultProductKey();
        } else {
            $args['productkey'] = $GLOBALS['wolfnet']->getProductKeyById($args["keyid"]);
        }
        $args['itemsPerPage'] = $GLOBALS['wolfnet']->getItemsPerPage();
        $args['sortOptions'] = $GLOBALS['wolfnet']->api->getSortOptions($args['productkey']);

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
            $args['productkey'] = $GLOBALS['wolfnet']->getDefaultProductKey();
        } else {
            $args['productkey'] = $GLOBALS['wolfnet']->getProductKeyById($args["keyid"]);
        }

        $args['itemsPerPage'] = $GLOBALS['wolfnet']->getItemsPerPage();
        $args['sortOptions'] = $GLOBALS['wolfnet']->apin->sendRequest($args['productkey'], '/search_criteria/sort_option');


        foreach ($args as $key => $item) {
            $args[$key] = apply_filters('wolfnet_listingGridView_' . $key, $item);
        }

        // echo "<pre>\$args: \n";
        // print_r($args);
        // echo "</pre>";
  
        return apply_filters('wolfnet_listingGridView', $this->parseTemplate('template/listingGrid.php', $args));

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
        //$args = $GLOBALS['wolfnet']->api->getMapParameters($listingsData, $productKey);
        $args = $GLOBALS['wolfnet']->getMapParameters($listingsData, $productKey);
        $args["url"] = $GLOBALS['wolfnet']->url;

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

    public function houseOver($args) 
    {

        return $this->parseTemplate('template/listingHouseover.php', $args);

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
        include $GLOBALS['wolfnet']->dir .'/'. $template;
        return ob_get_clean();

    }


}
