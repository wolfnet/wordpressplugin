<?php

/**
 * @title         Wolfnet_Ajax.php
 * @copyright     Copyright (c) 2012 - 2015, WolfNet Technologies, LLC
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

class Wolfnet_Ajax
{
	/*
	 *
	 * Registration functions
	 *
	 */
	public function registerAdminAjaxActions()
    {
        $ajxActions = array(
            'wolfnet_validate_key'            => 'remoteValidateProductKey',
            'wolfnet_saved_searches'          => 'remoteGetSavedSearches',
            'wolfnet_save_search'             => 'remoteSaveSearch',
            'wolfnet_delete_search'           => 'remoteDeleteSearch',
            'wolfnet_scb_options_agent'       => 'remoteShortcodeBuilderOptionsAgent',
            'wolfnet_scb_options_featured'    => 'remoteShortcodeBuilderOptionsFeatured',
            'wolfnet_scb_options_grid'        => 'remoteShortcodeBuilderOptionsGrid',
            'wolfnet_scb_options_list'        => 'remoteShortcodeBuilderOptionsList',
            'wolfnet_scb_options_quicksearch' => 'remoteShortcodeBuilderOptionsQuickSearch',
            'wolfnet_scb_savedsearch'         => 'remoteShortcodeBuilderSavedSearch',
            'wolfnet_content'                 => 'remoteContent',
            'wolfnet_content_header'          => 'remoteContentHeader',
            'wolfnet_content_footer'          => 'remoteContentFooter',
            'wolfnet_listings'                => 'remoteListings',
            'wolfnet_get_listings'            => 'remoteListingsGet',
            'wolfnet_css'                     => 'remotePublicCss',
            'wolfnet_market_name'             => 'remoteGetMarketName',
            'wolfnet_map_enabled'             => 'remoteMapEnabled',
            'wolfnet_price_range'             => 'remotePriceRange',
            'wolfnet_base_url'                => 'remoteGetBaseUrl',
            'wolfnet_route_quicksearch'       => 'remoteRouteQuickSearch',
            );

        foreach ($ajxActions as $action => $method) {
            $GLOBALS['wolfnet']->addAction('wp_ajax_' . $action, array(&$this, $method));
        }

    }

    public function registerAjaxActions()
    {
        $ajxActions = array(
            'wolfnet_content'           => 'remoteContent',
            'wolfnet_content_header'    => 'remoteContentHeader',
            'wolfnet_content_footer'    => 'remoteContentFooter',
            'wolfnet_listings'          => 'remoteListings',
            'wolfnet_get_listings'      => 'remoteListingsGet',
            'wolfnet_css'               => 'remotePublicCss',
            'wolfnet_base_url'          => 'remoteGetBaseUrl',
            'wolfnet_price_range'       => 'remotePriceRange',
            'wolfnet_route_quicksearch' => 'remoteRouteQuickSearch',
            );

        foreach ($ajxActions as $action => $method) {
            $GLOBALS['wolfnet']->addAction('wp_ajax_nopriv_' . $action, array(&$this, $method));
        }

    }


	/*
	 *
	 * Remote functions
	 *
	 */
	public function remoteValidateProductKey()
    {
        $productKey = (array_key_exists('key', $_REQUEST)) ? $_REQUEST['key'] : '';

        try {
            $response = ($GLOBALS['wolfnet']->productKeyIsValid($productKey)) ? 'true' : 'false';

        } catch (Wolfnet_Exception $e) {
            status_header(500);

            $response = array(
                'message' => $e->getMessage(),
                'data' => $e->getData(),
            );

        }

        wp_send_json($response);

    }


    public function remoteGetSavedSearches($keyid = null)
    {

        try {
            if ($keyid == null) {
                $keyid = (array_key_exists('keyid', $_REQUEST)) ? $_REQUEST['keyid'] : '1';
            }

            $response = $GLOBALS['wolfnet']->getSavedSearches(-1, $keyid);

        } catch (Wolfnet_Exception $e) {
            status_header(500);

            $response = array(
                'message' => $e->getMessage(),
                'data' => $e->getData(),
            );

        }

        wp_send_json($response);

    }


    public function remoteSaveSearch()
    {

        try {
            if (array_key_exists('post_title', $_REQUEST)) {
                // Create post object
                $my_post = array(
                    'post_title'  => $_REQUEST['post_title'],
                    'post_status' => 'publish',
                    'post_author' => wp_get_current_user()->ID,
                    'post_type'   => $GLOBALS['wolfnet']->customPostTypeSearch
                    );

                // Insert the post into the database
                $post_id = wp_insert_post($my_post);

                foreach ($_REQUEST['custom_fields'] as $field => $value) {
                    add_post_meta($post_id, $field, $value, true);
                }

                $key = $_REQUEST['custom_fields']['keyid'];

            }

            $this->remoteGetSavedSearches($key);

            $response = null;

        } catch (Wolfnet_Exception $e) {
            status_header(500);

            $response = array(
                'message' => $e->getMessage(),
                'data' => $e->getData(),
            );

        }

        wp_send_json($response);

    }


    public function remoteDeleteSearch()
    {

        try {
            if (array_key_exists('id', $_REQUEST)) {
                wp_delete_post($_REQUEST['id'], true);
            }

            $this->remoteGetSavedSearches();
            $response = null;

        } catch (Wolfnet_Exception $e) {
            status_header(500);

            $response = array(
                'message' => $e->getMessage(),
                'data' => $e->getData(),
            );

        }

        wp_send_json($response);

    }


    public function remoteShortcodeBuilderOptionsAgent()
    {

        try {
            $args = $GLOBALS['wolfnet']->getAgentPagesOptions();

            $response = $GLOBALS['wolfnet']->views->agentPagesOptionsFormView($args);

        } catch (Wolfnet_Exception $e) {
            status_header(500);

            $response = $GLOBALS['wolfnet']->displayException($e);

        }

        echo $response;

        die;

    }


    public function remoteShortcodeBuilderOptionsFeatured()
    {

        try {
            $args = $GLOBALS['wolfnet']->getFeaturedListingsOptions();

            $response = $GLOBALS['wolfnet']->views->featuredListingsOptionsFormView($args);

        } catch (Wolfnet_Exception $e) {
            status_header(500);

            $response = $GLOBALS['wolfnet']->displayException($e);

        }

        echo $response;

        die;

    }


    public function remoteShortcodeBuilderOptionsGrid()
    {

        try {
            $args = $GLOBALS['wolfnet']->getListingGridOptions();

            $response = $GLOBALS['wolfnet']->views->listingGridOptionsFormView($args);

        } catch (Wolfnet_Exception $e) {
            status_header(500);

            $response = $GLOBALS['wolfnet']->displayException($e);

        }

        echo $response;

        die;

    }


    public function remoteShortcodeBuilderOptionsList()
    {

        try {
            $args = $GLOBALS['wolfnet']->getPropertyListOptions();

            $response = $GLOBALS['wolfnet']->views->listingGridOptionsFormView($args);

        } catch (Wolfnet_Exception $e) {
            status_header(500);

            $response = $GLOBALS['wolfnet']->displayException($e);

        }

        echo $response;

        die;

    }


    public function remoteShortcodeBuilderOptionsQuickSearch()
    {

        try {
            $args = $GLOBALS['wolfnet']->getQuickSearchOptions();

            $response = $GLOBALS['wolfnet']->views->quickSearchOptionsFormView($args);

        } catch (Wolfnet_Exception $e) {
            status_header(500);

            $response = $GLOBALS['wolfnet']->displayException($e);

        }

        echo $response;

        die;

    }


    public function remoteShortcodeBuilderSavedSearch()
    {

        try {
            $id = (array_key_exists('id', $_REQUEST)) ? $_REQUEST['id'] : 0;

            $response = $GLOBALS['wolfnet']->getSavedSearch($id);

        } catch (Wolfnet_Exception $e) {
            status_header(500);

            $response = array(
                'message' => $e->getMessage(),
                'data' => $e->getData(),
            );

        }

        wp_send_json($response);

    }


    public function remoteContent()
    {

        try {
            $response = $GLOBALS['wolfnet']->getWpHeader() . $GLOBALS['wolfnet']->getWpFooter();

        } catch (Wolfnet_Exception $e) {
            status_header(500);

            $response = $GLOBALS['wolfnet']->displayException($e);

        }

        echo $response;

        die;

    }


    public function remoteContentHeader()
    {

        try {
            $response = $GLOBALS['wolfnet']->getWpHeader();

        } catch (Wolfnet_Exception $e) {
            status_header(500);

            $response = $GLOBALS['wolfnet']->displayException($e);

        }

        echo $response;

        die;

    }


    public function remoteContentFooter()
    {

        try {
            $GLOBALS['wolfnet']->getWpHeader();

            $response = $GLOBALS['wolfnet']->getWpFooter();

        } catch (Wolfnet_Exception $e) {
            status_header(500);

            $response = $GLOBALS['wolfnet']->displayException($e);

        }

        echo $response;

        die;

    }


    public function remoteListings()
    {

        try {
            $args = $GLOBALS['wolfnet']->getListingGridOptions($_REQUEST);

            $response = $GLOBALS['wolfnet']->getWpHeader()
            	. $GLOBALS['wolfnet']->listingGrid($args)
            	. $GLOBALS['wolfnet']->getWpFooter();

        } catch (Wolfnet_Exception $e) {
            status_header(500);

            $response = $GLOBALS['wolfnet']->displayException($e);

        }

        echo $response;

        die;

    }


    public function remoteListingsGet()
    {

        try {
            $args = $GLOBALS['wolfnet']->getListingGridOptions($_REQUEST);

            // used by pagination dropdown "per page"
            if (!empty($_REQUEST['numrows'])) {
                $_REQUEST['maxrows'] = $_REQUEST['numrows'];
            }

            $criteria = $GLOBALS['wolfnet']->prepareListingQuery($_REQUEST);

            $keyid = (array_key_exists('keyid', $_REQUEST)) ? $_REQUEST["keyid"] : null;

            $productKey = $GLOBALS['wolfnet']->getProductKeyById($keyid);

            $data = $GLOBALS['wolfnet']->apin->sendRequest($productKey, '/listing', 'GET', $criteria);

            $GLOBALS['wolfnet']->augmentListingsData($data, $productKey);

        } catch (Wolfnet_Exception $e) {
            status_header(500);
            $data = array(
                'message' => $e->getMessage(),
                'data' => $e->getData(),
            );

        }

        // TODO: Do we really need to support AjaxP here?
        $callback = (array_key_exists('callback', $_REQUEST)) ? $_REQUEST['callback'] : false;

        if ($callback !== false) {
            header('Content-Type: application/javascript');
            echo $callback . '(' . json_encode($data) . ');';
        } else {
            wp_send_json($data);
        }

        die;

    }


    public function remotePublicCss()
    {

        try {
            $response = $GLOBALS['wolfnet']->views->getPublicCss();

        } catch (Wolfnet_Exception $e) {
            status_header(500);

            echo $GLOBALS['wolfnet']->displayException($e);
            die;

        }

        header('Content-type: text/css');
        echo $response;

        die;

    }


    public function remotePriceRange()
    {

        try {
            // TODO: Assign default value.
            $keyid = $_REQUEST["keyid"];

            $productKey = $GLOBALS['wolfnet']->getProductKeyById($keyid);

            $response = $GLOBALS['wolfnet']->getPrices($productKey);

        } catch (Wolfnet_Exception $e) {
            status_header(500);

            $response = array(
                'message' => $e->getMessage(),
                'data' => $e->getData(),
            );

        }

        wp_send_json($response);

    }


    public function remoteGetMarketName()
    {

        try {
            // TODO: Assign default value.
            $productKey = $_REQUEST["productkey"];

            $marketName = $GLOBALS['wolfnet']->getMarketName($productKey);
            $response = strtoupper($marketName);

        } catch (Wolfnet_Exception $e) {
            status_header(500);

            $response = array(
                'message' => $e->getMessage(),
                'data' => $e->getData(),
            );

        }

        wp_send_json($response);

    }


    public function remoteMapEnabled()
    {

        try {
            // TODO: Assign default value.
            $keyid = $_REQUEST["keyid"];

            $productKey = $GLOBALS['wolfnet']->getProductKeyById($keyid);

            $response = $GLOBALS['wolfnet']->getMaptracksEnabled($productKey);

        } catch (Wolfnet_Exception $e) {
            status_header(500);

            $response = array(
                'message' => $e->getMessage(),
                'data' => $e->getData(),
            );

        }

        wp_send_json($response);

    }


    public function remoteGetBaseUrl()
    {

        try {
            // TODO: Assign default value.
            $keyid = $_REQUEST["keyid"];
            $productKey = $GLOBALS['wolfnet']->getProductKeyById($keyid);
            $response = $GLOBALS['wolfnet']->getBaseUrl($productKey);

        } catch (Wolfnet_Exception $e) {
            status_header(500);

            $response = array(
                'message' => $e->getMessage(),
                'data' => $e->getData(),
            );

        }

        wp_send_json($response);

    }


    public function remoteRouteQuickSearch()
    {
        try {
            $response = $GLOBALS['wolfnet']->routeQuickSearch($_REQUEST['formData']);
        } catch (Wolfnet_Exception $e) {
            status_header(500);

            $response = array(
                'message' => $e->getMessage(),
                'data' => $e->getData(),
            );

        }

        wp_send_json($response);
    }

}

?>