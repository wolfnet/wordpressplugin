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

    /* PROPERTIES ******************************************************************************* */

    /**
     * location of images hosted remotely
     * @var string
     */
    private $remoteImages = '//common.wolfnet.com/wordpress/';


    /* CONSTRUCTOR ****************************************************************************** */

    public function __construct()
    {
        $this->templateDir = dirname(__FILE__) . '/template';
    }


    /* Public Methods *************************************************************************** */

    public function amSettingsPage()
    {

        try {
            $productKey = json_decode($GLOBALS['wolfnet']->getProductKey());

            // add the market name
            for ($i=1; $i<=count($productKey); $i++) {
                $key = $productKey[$i-1]->key;

                try {
                    $validKey = $GLOBALS['wolfnet']->productKeyIsValid($key);
                } catch (Wolfnet_Api_ApiException $e) {
                    $validKey = false;
                }

                if ($validKey) {
                    try {
                        $market = $GLOBALS['wolfnet']->getMarketName($key);

                    } catch (Wolfnet_Api_ApiException $e) {
                        // Catch the error and display no market
                        // TODO: We may want to display an error about this.
                        $market = '';
                    }

                } else {
                    $market = '';
                }

                if (!is_wp_error($market)) {
                    $productKey[$i-1]->market = strtoupper($market);
                }

            }

            $sslEnabled = $GLOBALS['wolfnet']->getSslEnabled();

            $out = $this->parseTemplate('adminSettings', array(
                'formHeader' => $this->settingsFormHeaders(),
                'productKey' => $productKey,
                'sslEnabled' => $sslEnabled,
            ));

        } catch (Wolfnet_Exception $e) {
            $out = $this->exceptionView($e);
        }

        echo $out;

        return $out;

    }


    public function amEditCssPage()
    {

        try {
            $out = $this->parseTemplate('adminEditCss', array(
                'formHeader' => $this->cssFormHeaders(),
                'publicCss' => $this->getPublicCss(),
                'adminCss' => $GLOBALS['wolfnet']->admin->getAdminCss(),
            ));

        } catch (Wolfnet_Exception $e) {
            $out = $this->exceptionView($e);
        }

        echo $out;

        return $out;

    }


    public function amSearchManagerPage()
    {

        try {
            $key = (array_key_exists("keyid", $_REQUEST)) ? $_REQUEST["keyid"] : "1";
            $productKey = $GLOBALS['wolfnet']->getProductKeyById($key);

            if (!$GLOBALS['wolfnet']->productKeyIsValid($productKey)) {
                $out = $this->parseTemplate('invalidProductKey');
            } else {
                $out = $this->parseTemplate('adminSearchManager', array(
                    'searchForm' => ($GLOBALS['wolfnet']->smHttp !== null) ? $GLOBALS['wolfnet']->smHttp['body'] : '',
                    'markets' => json_decode($GLOBALS['wolfnet']->getProductKey()),
                    'selectedKey' => $key,
                    'url' => $GLOBALS['wolfnet']->url,
                ));

            }

        } catch (Wolfnet_Exception $e) {
            $out = $this->exceptionView($e);
        }

        echo $out;

        return $out;

    }


    public function amSupportPage()
    {

        try {
            $out = $this->parseTemplate('adminSupport', array(
                'imgdir' => $this->remoteImages,
            ));

        } catch (Wolfnet_Exception $e) {
            $out = $this->exceptionView($e);
        }

        echo $out;

        return $out;

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


    public function agentPagesOptionsFormView(array $args = array())
    {
        $markets = json_decode($GLOBALS['wolfnet']->getProductKey());
        $keyids = array();

        foreach ($markets as $market) {
            array_push($keyids, $market->id);
        }

        $defaultArgs = array(
            'instance_id'     => str_replace('.', '', uniqid('wolfnet_agentPages_')),
            'markets'         => $markets,
            'keyids'          => $keyids
        );

        $args = array_merge($defaultArgs, $args);

        return $this->parseTemplate('agentPagesOptions', $args);

    }


    public function featuredListingsOptionsFormView(array $args = array())
    {
        $defaultArgs = array(
            'instance_id'     => str_replace('.', '', uniqid('wolfnet_featuredListing_')),
            'markets'         => json_decode($GLOBALS['wolfnet']->getProductKey()),
            'selectedKey'     => (array_key_exists('keyid', $_REQUEST)) ? $_REQUEST['keyid'] : '1',
        );

        $args = array_merge($defaultArgs, $args);

        return $this->parseTemplate('featuredListingsOptions', $args);

    }


    public function listingGridOptionsFormView(array $args = array())
    {
        $defaultArgs = array(
            'instance_id'      => str_replace('.', '', uniqid('wolfnet_listingGrid_')),
            'markets'          => json_decode($GLOBALS['wolfnet']->getProductKey()),
            'keyid'            => ''
        );

        $args = array_merge($defaultArgs, $args);

        $args['criteria'] = esc_attr($args['criteria']);

        return $this->parseTemplate('listingGridOptions', $args);

    }


    public function quickSearchOptionsFormView(array $args = array())
    {
        $markets = json_decode($GLOBALS['wolfnet']->getProductKey());
        $keyids = array();
        $view = '';

        foreach ($markets as $market) {
            array_push($keyids, $market->id);
        }

        $defaultArgs = array(
            'instance_id' => str_replace('.', '', uniqid('wolfnet_quickSearch_')),
            'markets'     => $markets,
            'keyids'      => $keyids,
            'view'        => $view,
        );


        $args = array_merge($defaultArgs, $args);

        return $this->parseTemplate('quickSearchOptions', $args);

    }


    public function listingView(array $args = array())
    {
        foreach ($args as $key => $item) {
            $args[$key] = apply_filters('wolfnet_listingView_' . $key, $item);
        }

        return apply_filters('wolfnet_listingView', $this->parseTemplate('listing', $args));

    }


    public function listingBriefView(array $args = array())
    {
        foreach ($args as $key => $item) {
            $args[$key] = apply_filters('wolfnet_listingBriefView_' . $key, $item);
        }

        return apply_filters('wolfnet_listingBriefView', $this->parseTemplate('briefListing', $args));

    }


    public function listingResultsView(array $args = array())
    {
        foreach ($args as $key => $item) {
            $args[$key] = apply_filters('wolfnet_listingResultsView_' . $key, $item);
        }

        return apply_filters('wolfnet_listingResultsView', $this->parseTemplate('resultsListing', $args));

    }


    public function agentsListView(array $args = array())
    {
        foreach ($args as $key => $item) {
            $args[$key] = apply_filters('wolfnet_agentPagesView_' . $key, $item);
        }

        return apply_filters('wolfnet_agentPagesView', $this->parseTemplate('agentPagesListAgents', $args));
    }


    public function agentView(array $args = array()) {
        foreach ($args as $key => $item) {
            $args[$key] = apply_filters('wolfnet_agentPagesView_' . $key, $item);
        }

        return apply_filters('wolfnet_agentPagesView', $this->parseTemplate('agentPagesShowAgent', $args));
    }


    public function featuredListingView(array $args = array())
    {
        foreach ($args as $key => $item) {
            $args[$key] = apply_filters('wolfnet_featuredListingView_' . $key, $item);
        }

        return apply_filters('wolfnet_featuredListingView', $this->parseTemplate('featuredListings', $args));

    }


    public function propertyListView(array $args = array())
    {
        if (!array_key_exists('keyid', $args)) {
            $args['productkey'] = $GLOBALS['wolfnet']->getDefaultProductKey();
        } else {
            $args['productkey'] = $GLOBALS['wolfnet']->getProductKeyById($args['keyid']);
        }

        $args['itemsPerPage'] = $GLOBALS['wolfnet']->getItemsPerPage();

        $data = $GLOBALS['wolfnet']->apin->sendRequest($args['productkey'], '/search_criteria/sort_option');
        $args['sortOptions'] = $data['responseData']['data']['options'];

        foreach ($args as $key => $item) {
            $args[$key] = apply_filters('wolfnet_propertyListView_' . $key, $item);
        }

        // Ensure the styles for property list are used instead of listing grid styles.
        $args['class'] = str_replace('wolfnet_listingGrid', 'wolfnet_propertyList', $args['class']);

        return apply_filters('wolfnet_propertyListView', $this->parseTemplate('propertyList', $args));

    }


    public function listingGridView(array $args = array())
    {

        if (!array_key_exists('keyid', $args)) {
            $args['productkey'] = $GLOBALS['wolfnet']->getDefaultProductKey();
        } else {
            $args['productkey'] = $GLOBALS['wolfnet']->getProductKeyById($args['keyid']);
        }

        $args['itemsPerPage'] = $GLOBALS['wolfnet']->getItemsPerPage();

        $data = $GLOBALS['wolfnet']->apin->sendRequest($args['productkey'], '/search_criteria/sort_option');
        $args['sortOptions'] = $data['responseData']['data']['options'];

        foreach ($args as $key => $item) {
            $args[$key] = apply_filters('wolfnet_listingGridView_' . $key, $item);
        }

        return apply_filters('wolfnet_listingGridView', $this->parseTemplate('listingGrid', $args));

    }


    public function quickSearchView(array $args = array())
    {
        // array containing possible values for 'view' arg
        $views = array('basic', 'legacy');

        //set up a custom css class for the wrapper. default 'wolfnet_quickSearch_legacy'
        $args['viewclass'] = 'wolfnet_quickSearch_' . (in_array($args['view'], $views) ? $args['view'] : 'legacy');

        foreach ($args as $key => $item) {
            $args[$key] = apply_filters('wolfnet_quickSearchView_' . $key, $item);
        }

        return apply_filters('wolfnet_quickSearchView', $this->parseTemplate('quickSearch', $args));

    }


    public function mapView($listingsData, $productKey = null)
    {
        $args = $GLOBALS['wolfnet']->getMapParameters($listingsData, $productKey);
        $args['url'] = $GLOBALS['wolfnet']->url;

        return apply_filters('wolfnet_mapView', $this->parseTemplate('map', $args));

    }


    public function hideListingsToolsView($hideId, $showId, $collapseId, $instance_id)
    {
        $args['hideId'] = $hideId;
        $args['showId'] = $showId;
        $args['collapseId'] = $collapseId;
        $args['instance_id'] = $instance_id;

        return apply_filters('wolfnet_hideListingsTools', $this->parseTemplate('hideListingsTools', $args));

    }


    public function toolbarView(array $args = array())
    {
        foreach ($args as $key => $item) {
            $args[$key] = apply_filters('wolfnet_toolbarView_' . $key, $item);
        }

        return apply_filters('wolfnet_toolbarView', $this->parseTemplate('toolbar', $args));

    }


    public function errorView($error)
    {
        return $this->parseTemplate('error', array('error'=>$error));
    }


    public function exceptionView(Wolfnet_Exception $exception)
    {
        return $this->parseTemplate('exception', array('exception'=>$exception));
    }


    public function houseOver($args)
    {
        return $this->parseTemplate('listingHouseover', $args);
    }


    /* PRIVATE METHODS ************************************************************************** */

    private function parseTemplate($template, array $vars = array())
    {
        extract($vars, EXTR_OVERWRITE);

        ob_start();

        include $this->templateDir . '/' . $template . '.php';

        return trim(ob_get_clean());

    }


    private function cssFormHeaders()
    {
        ob_start();

        settings_fields($GLOBALS['wolfnet']->CssOptionGroup);

        return trim(ob_get_clean());

    }


    private function settingsFormHeaders()
    {
        ob_start();

        settings_fields($GLOBALS['wolfnet']->optionGroup);

        return trim(ob_get_clean());

    }


}
